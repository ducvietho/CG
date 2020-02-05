<?php


namespace App\Http\Resources;

use App\User;
use App\MyConst;
use App\Models\Care;
use App\Models\Patient;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Resources\Json\JsonResource;

class CareDetailResource extends JsonResource
{
    protected $type_user;

    public function __construct($resource, $type_user)
    {
        $this->resource = $resource;
        $this->type_user = $type_user;
    }

    public function toArray($request)
    {
        switch ($this->type_user) {
            case MyConst::NURSER_REQUEST:
                $user_patient = Patient::select('name', 'id', 'user_login', 'avatar','gender','birthday')->find($this->user_patient);
                return [
                    'id_request' => $this->id,
                    'user' => $this->formatPatient($user_patient),
                    'status' => $this->status,
                    'start_date' => $this->start_date,
                    'end_date' => $this->end_date,
                    'start_time' => $this->start_time,
                    'end_time' => $this->end_time,
                    'rate' => $this->rate
                ];
            case MyConst::PATIENT_REQUEST:
                $user_patient = Patient::select('name', 'id', 'user_login', 'avatar')->find($this->user_patient);
                return [
                    'id_request' => $this->id,
                    'user' => $this->formatNurse($this->user_nurse),
                    'name_patient' => $user_patient->name,
                    'status' => $this->status,
                    'start_date' => $this->start_date,
                    'end_date' => $this->end_date,
                    'start_time' => $this->start_time,
                    'end_time' => $this->end_time,
                    'rate' => $this->rate
                ];
            default:
                # code...
                break;
        }
    }

    protected function formatPatient($patient)
    {
        $user = User::select('id', 'phone', 'email', 'avatar')->find($patient->user_login);
        return [
            'id' => $patient->id,
            'name' => $patient->name,
            'avatar' => $patient->avatar,
            'phone' => $user->phone,
            'email' => $user->email,
            'gender' => $patient->gender,
            'birthday' => $patient->birthday
        ];
    }

    protected function formatNurse($nurse_id)
    {
        $user = User::select('id', 'phone', 'email', 'name', 'avatar','gender','birthday')->find($nurse_id);
        $rate = Care::where('user_nurse', $nurse_id)
            ->where('rate', '>', 0)
            ->pluck('rate')->toArray();
        return [
            'id' => $user->id,
            'name' => $user->name,
            'avatar' => $user->avatar,
            'phone' => $user->phone,
            'email' => $user->email,
            'gender' => $user->gender,
            'birthday' => $user->birthday,
            'rate' => (count($rate) > 0) ? round(array_sum($rate) / count($rate), 1) : 0
        ];
    }
}
