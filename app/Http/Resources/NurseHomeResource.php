<?php


namespace App\Http\Resources;


use App\Models\Care;
use App\Models\City;
use App\Models\District;
use App\Models\Patient;
use App\Models\PatientInterest;
use App\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class NurseHomeResource extends JsonResource
{
    public function toArray($request)
    {

        $city_code = substr($this->code_add, 0, 2);
        $city_name = City::where('code', $city_code)->first();
        $district = District::where('code', $this->code_add)->first();
        return [
            'id' => $this->id,
            'name' => $this->user->name,
            'avatar' => $this->user->avatar,
            'age' => $this->age($this->user->birthday),
            'city' => ($city_name == null) ? new \stdClass() : new CityResource($city_name),
            'district' => ($district == null) ? new \stdClass() : new DistrictResource($district),
            'setting_care' => $this->user->setting_care,
            'is_interest' => $this->is_interest($this->id, Auth::id()),
            'user_caring' => $this->userCare()['first'],
            'remain_caring' => $this->userCare()['remain_caring']
        ];
    }

    private function age($year)
    {
        return date("Y") - $year;
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