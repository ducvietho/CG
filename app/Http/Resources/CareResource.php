<?php


namespace App\Http\Resources;

use App\Models\Patient;
use App\Http\Resources\PatientHomeResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CareResource extends JsonResource
{

    
    public function toArray($request)
    {
        $patient = Patient::find($this->user_patient);
        return [
            'id_request'=>$this->id,
            'user'=>new PatientHomeResource($patient)
        ];
    }
    
}
