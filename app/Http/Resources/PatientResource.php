<?php


namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'relationship' => $this->relationship,
            'gender' => $this->gender,
            'age'=>$this->age($this->birthday),
            'city' => new CityResource($this->city),
            'district' => new DistrictResource($this->district),
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'address' => $this->address,
            'note' => ($this->note != null) ? $this->note : ''
        ];
    }
    private function age($year){
        return date("Y") - $year;
    }
}