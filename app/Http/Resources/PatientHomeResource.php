<?php


namespace App\Http\Resources;

use App\Models\City;
use App\Models\District;
use App\Http\Resources\CityResource;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
{
    
    public function toArray($request)
    {
        $city_code = substr($this->code_add,0,2);
        $city_name = new CityResource(City::where('code',$city_code)->first());
        $district = new DistrictResource(District::where('code',$this->code_add));
        return [
            'name' => $this->name,
            'city_name' => $this->check_null($city_name),
            'district'=>$this->check_null($district),
            'age'=>$this->age($this->birthday)
        ];
    }

    private function check_null($a){
        return ($a == null)?new \stdClass():$a;
    }
    private function age($year){
        return date("Y") - $year;
    }
    private function getCare($id_patient){
        $nures_care = Care::where('user_patient',$id_patient)->where('status',1);
    }
}
