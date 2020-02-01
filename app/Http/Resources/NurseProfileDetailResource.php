<?php


namespace App\Http\Resources;

use App\Models\Care;
use App\Models\City;
use App\Models\District;
use App\Models\Patient;
use App\Models\PatientInterest;
use App\User;
use Auth;
use Illuminate\Http\Resources\Json\JsonResource;

class NurseProfileDetailResource extends JsonResource
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
        $user_login = User::find($this->user_login);
        $end_time = $this->end_time;
        if($this->end_time_1 > 0){
            $end_time = $this->end_time_1;
        }
        $patient = Care::where('user_nurse',$this->user_login)->where('status',1)->pluck('user_patient')->toArray();
        $user_caring = Patient::whereIn('id',$patient)->get();
        $rate = Care::where('user_nurse',$this->user_login)
            ->where('rate','>',0)
            ->pluck('rate')->toArray();
        return [
            'id'=>$this->user_login,
            'name' => $user_login->name,
            'avatar'=>$user_login->avatar,
            'user_name'=>$user_login->user_name,
            'gender'=>$user_login->gender,
            'nationality'=>(int)$this->nationality,
            'address'=>$this->address,
            'start_date'=>$this->start_date,
            'end_date'=>$this->end_date,
            'start_time'=>(int)$this->start_time,
            'end_time'=>$end_time,
            'is_certificate'=>$this->is_certificate,
            'description'=>$this->description,
            'rate'=>(count($rate) >0)?round(array_sum($rate)/count($rate),1):0,
            'location'=>$listAddress,
            'birthday'=>$user_login->birthday,
            'is_interest'=>$this->is_interest($this->user_login,Auth::id()),
            'user_caring' => PatientShortResource::collection($user_caring),
            'salary' => $this->salary,
            'type_salary' => $this->type_salary
        ];
    }

    private function is_interest($id_nures, $id_login){
        $record = PatientInterest::where('user_nurse',$id_nures)->where('user_login',$id_login)->first();
        return ($record == null)?0:1;
    }
}
