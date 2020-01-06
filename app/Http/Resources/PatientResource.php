<?php


namespace App\Http\Resources;


use App\Models\Care;
use App\Models\City;
use App\Models\District;
use App\Models\NurseInterest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Resources\Json\JsonResource;


class PatientResource extends JsonResource
{
    public function toArray($request)
    {
        $city_code = substr($this->code_add,0,2);
        $city_name = City::where('code',$city_code)->first();
        $district = District::where('code',$this->code_add)->first();
        $end_time = $this->end_time;
        $isCare = 0;
        $care = Care::where('user_patient',$this->id)->where('status',1)->first();
        if($care != null){
            $isCare = 1;
        }
        if($this->end_time_1 > 0){
            $end_time = $this->end_time_1;
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'avatar'=>$this->avatar,
            'relationship' => $this->relationship,
            'gender' => $this->gender,
            'birthday'=>$this->birthday,
            'city' =>($city_name == null)? new \stdClass() : new CityResource($city_name) ,
            'district'=> ($district == null) ? new \stdClass() : new DistrictResource($district),
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'start_time' => $this->start_time,
            'end_time' => $end_time,
            'address' => $this->address,
            'note' => ($this->note != null) ? $this->note : '',
            'is_certificate' => $this->is_certificate,
            'is_interest'=>$this->is_interest($this->id,Auth::id()),
            'is_care' => $isCare
        ];
    }

    private function is_interest($id_nures, $id_login){
        $record = NurseInterest::where('user_patient',$id_nures)->where('user_nurse',$id_login)->first();
        return ($record == null)?0:1;
    }
}