<?php


namespace App\Http\Resources\CMS;


use App\Models\Patient;
use App\User;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientHistoryCareResource extends JsonResource
{
    public function toArray($request)
    {


        return [
            'id_request' => $this->id,
            'nurse' => $this->formatNurse($this->user_nurse),
            'status' => $this->status,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,

        ];

    }
    protected function formatNurse($nurse_id)
    {
        $user = User::select('id', 'phone', 'email', 'name')->find($nurse_id);
        return [
            'id' => $user->id,
            'name' => $user->name,
            'phone' => $user->phone,
            'email' => $user->email,
        ];
    }
}