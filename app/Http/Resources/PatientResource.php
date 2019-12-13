<?php


namespace App\Http\Resources;


use App\Models\City;
use App\Models\District;
use Illuminate\Http\Resources\Json\JsonResource;


class PatientResource extends JsonResource
{
    public function toArray($request)
    {
        $city_code = substr($this->code_add,0,2);
        $city_name = City::where('code',$city_code)->first();
        $district = District::where('code',$this->code_add)->first();
        return [
            'id' => $this->id,
            'name' => $this->name,
            'relationship' => $this->relationship,
            'gender' => $this->gender,
            'age'=>$this->age($this->birthday),
            'city' =>($city_name == null)? new \stdClass() : new CityResource($city_name) ,
            'district'=> ($district == null) ? new \stdClass() : new DistrictResource($district),
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'address' => $this->address,
            'note' => ($this->note != null) ? $this->note : '',
            'is_certificate' => $this->is_certificate
        ];
    }
    private function age($year){
        return date("Y") - $year;
    }
}