<?php


namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class PatientShortResource extends JsonResource
{
    public function toArray($request)
    {
        $end_time = $this->end_time;
        if($this->end_time_1 > 0){
            $end_time = $this->end_time_1;
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'start_time' => $this->start_time,
            'end_time' => $end_time,
        ];
    }
}