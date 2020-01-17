<?php


namespace App\Http\Resources\CMS;


use App\Models\Patient;
use App\User;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestCMSResource extends JsonResource
{
    public function toArray($request)
    {
        $user_patient = Patient::select('name', 'id', 'user_login', 'avatar')->find($this->user_patient);
        return [
            'id_request' => $this->id,
            'created' => strtotime($this->created_at),
            'type' => $this->type,
            'nurse' => $this->formatNurse($this->user_nurse),
            'patient' => $this->formatPatient($user_patient),
            'user_login' => $this->formatUserLogin($this->user_login,$this->type),
            'status' => $this->status,

        ];
    }

    protected function formatPatient($patient)
    {
        return [
            'id' => $patient->id,
            'name' => $patient->name,
        ];
    }

    protected function formatNurse($nurse_id)
    {
        $user = User::select('id', 'phone', 'email', 'name', 'avatar')->find($nurse_id);
        return [
            'id' => $user->id,
            'name' => $user->name,
        ];
    }

    protected function formatUserLogin($user_login,$type)
    {
        $format = new \stdClass();
        if($type != 1){
            $user = User::select('id', 'name', 'avatar')->find($user_login);
            $format->id = $user->id;
            $format->name = $user->name;
        }
        return $format;
    }
}