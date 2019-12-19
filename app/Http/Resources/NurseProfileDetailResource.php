<?php


namespace App\Http\Resources;

use App\Models\Care;
use App\Models\Patient;
use Auth;
use App\User;
use App\Models\City;
use App\Models\District;
use App\Models\PatientInterest;
use App\Http\Resources\CityResource;
use Illuminate\Http\Resources\Json\JsonResource;

class NurseProfileDetailResource extends JsonResource
{
    
    public function toArray($request)
    {
        $city_code = substr($this->code_add,0,2);
        $city_name = City::where('code',$city_code)->first();
        $district = District::where('code',$this->code_add)->first();
        $user_login = User::find($this->user_login);
        $end_time = $this->end_time;
        if($this->end_time_1 > 0){
            $end_time = $this->end_time_1;
        }
        $patient = Care::where('user_nurse',$this->user_login)->where('status',1)->pluck('user_patient')->toArray();
        $user_caring = Patient::whereIn('id',$patient)->get();
        return [
            'id'=>$this->user_login,
            'name' => $user_login->name,
            'avatar'=>$user_login->avatar,
            'user_name'=>$user_login->user_name,
            'gender'=>$user_login->gender,
            'nationality'=>(int)$this->nationality,
            'address'=>(int)$this->address,
            'start_date'=>$this->start_date,
            'end_date'=>$this->end_date,
            'start_time'=>(int)$this->start_time,
            'end_time'=>$end_time,
            'is_certificate'=>(int)$this->is_certificate,
            'description'=>$this->description,
            'rate'=>(float)($this->rate == null)?0:$this->rate,
            'city' =>($city_name == null)? new \stdClass() : new CityResource($city_name) ,
            'district'=> ($district == null) ? new \stdClass() : new DistrictResource($district),
            'age'=>$this->age($user_login->birthday),
            'is_interest'=>$this->is_interest($this->id,Auth::id()),
            'user_caring' => PatientShortResource::collection($user_caring)
        ];
    }
    private function age($year){
        return date("Y") - $year;
    }
    private function is_interest($id_nures, $id_login){
        $record = PatientInterest::where('user_nurse',$id_nures)->where('user_login',$id_login)->first();
        return ($record == null)?0:1;
    }
}
