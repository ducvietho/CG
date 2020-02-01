<?php


namespace App\Http\Resources;


use App\User;
use App\Models\Care;
use App\Models\City;
use App\Models\Patient;
use App\Models\District;
use App\Models\PatientInterest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Resources\Json\JsonResource;

class NurseHomeResource extends JsonResource
{
    public function toArray($request)
    {
        $codeCities = json_decode($this->code_add);
        $listAddress = [];
        foreach ($codeCities as $cityCode){
            $location = new \stdClass();
            $city_code = substr($cityCode, 0, 2);
            $city_name = City::where('code', $city_code)->first();
            $district = District::where('code', $cityCode)->first();
            $location->city = ($city_name == null) ? new \stdClass() : new CityResource($city_name);
            $location->district = ($district == null) ? new \stdClass() : new DistrictResource($district);
            array_push($listAddress,$location);
        }
        $rate = Care::where('user_nurse',$this->user_login)
            ->where('rate','>',0)
            ->pluck('rate')->toArray();
        return [
            'id' => $this->user_login,
            'name' => $this->user->name,
            'avatar' => $this->user->avatar,
            'birthday' => $this->user->birthday,
            'location' => $listAddress,
            'is_interest' => $this->is_interest($this->user_login, Auth::id()),
            'rate'=>(count($rate) >0)?round(array_sum($rate)/count($rate),1):0,
            'salary' => $this->salary,
            'type_salary' => $this->type_salary
        ];
    }


    private function is_interest($id_nures, $id_login)
    {
        $record = PatientInterest::where('user_nurse', $id_nures)->where('user_login', $id_login)->first();
        return ($record == null) ? 0 : 1;
    }

    private function userCare()
    {
        $patient_care_id = Care::where('user_nurse', $this->id)->where('status', 1)->pluck('user_patient');
        $patients_care_name = null;
        if (sizeof($patient_care_id) > 0) {
            $patients_care_name = Patient::select('name')->where('id', $patient_care_id[0])->first();
        }

        return [
            'first' => ($patients_care_name == null) ? "" : $patients_care_name->name,
            'remain_caring' => ((sizeof($patient_care_id) - 1) > 0) ? (sizeof($patient_care_id) - 1) : 0
        ];
    }
}