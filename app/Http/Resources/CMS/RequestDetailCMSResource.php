<?php


namespace App\Http\Resources\CMS;


use App\Models\Patient;
use App\User;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestDetailCMSResource extends JsonResource
{
    public function toArray($request)
    {

        $user_patient = Patient::find($this->user_patient);
        return [
            'id_request' => $this->id,
            'nurse' => $this->formatNurse($this->user_nurse),
            'patient' => $this->formatPatient($user_patient),
            'user_login' => $this->formatUserLogin($this->user_login,$this->type),
            'status' => $this->status,
            'type' => $this->type,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'rate' => $this->rate
        ];

    }

    protected function formatPatient($patient)
    {
        return [
            'id' => $patient->id,
            'name' => $patient->name,
            'birthday' => $patient->birthday,
        ];
    }

    protected function formatNurse($nurse_id)
    {
        $user = User::select('id', 'phone', 'email', 'name', 'avatar')->find($nurse_id);
        return [
            'id' => $user->id,
            'name' => $user->name,
            'avatar' => $user->avatar,
            'phone' => $user->phone,
            'email' => $user->email,
        ];
    }

    protected function formatUserLogin($user_login,$type)
    {
        $format = new \stdClass();
        if($type != 1){
            $user = User::select('id', 'name', 'avatar','phone','email')->find($user_login);
            $format->id = $user->id;
            $format->name = $user->name;
            $format->phone = $user->phone;
            $format->email = $user->email;
        }
        return $format;
    }
}