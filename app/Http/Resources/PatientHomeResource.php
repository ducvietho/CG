<?php


namespace App\Http\Resources;

use App\Models\Care;
use App\Models\NurseInterest;
use App\User;
use Auth;
use App\Models\City;
use App\Models\District;
use App\Http\Resources\CityResource;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientHomeResource extends JsonResource
{
    
    public function toArray($request)
    {
        $city_code = substr($this->code_add,0,2);
        $city_name = City::where('code',$city_code)->first();
        $district = District::where('code',$this->code_add)->first();
        return [
            'id'=>$this->id,
            'name' => $this->name,
            'city' =>($city_name == null)? new \stdClass() : new CityResource($city_name) ,
            'district'=> ($district == null) ? new \stdClass() : new DistrictResource($district),
            'age'=>$this->age($this->birthday),
            'is_interest'=>$this->is_interest(Auth::id(),$this->id),
            'user_caring'=>$this->getCare($this->id)['first'],
            'remain_caring'=>$this->getCare($this->id)['remain_caring']
        ];
    }

    private function age($year){
        return date("Y") - $year;
    }
    private function getCare($id_patient){
        $nures_care_id = Care::where('user_patient',$id_patient)->where('status',1)->pluck('user_nurse');
        $nures_care_name = null;
        if(sizeof($nures_care_id)>0){
            $nures_care_name = User::select('name')->where('id',$nures_care_id[0])->first();
        }
        return [
            'first' => ($nures_care_name == null) ? "" : $nures_care_name->name,
            'remain_caring' => ((sizeof($nures_care_id) - 1) > 0) ? (sizeof($nures_care_id) - 1) : 0
        ];
    }

    private function is_interest($id_nures, $id_patient){
        $record = NurseInterest::where('user_nurse',$id_nures)->where('user_patient',$id_patient)->first();
        return ($record == null)?0:1;
    }
}
