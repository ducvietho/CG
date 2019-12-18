<?php


namespace App\Http\Resources;

use App\User;
use App\Models\Patient;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Resources\Json\JsonResource;

class CareResource extends JsonResource
{

    
    public function toArray($request)
    {
        $type_user = Auth::user()->type;
        switch ($type_user) {
            case Config::get('constants.type_user.nurse'):
                $user_patient = Patient::select('name','id','user_login')->find($this->user_patient);
               return [
                    'id_request'=>$this->id,
                    'user'=>$this->formatPatient($user_patient),
                    'start_date'=>$this->start_date,
                    'end_date'=>$this->end_date,
                    'start_time'=>$this->start_time,
                    'end_time'=>$this->end_time,           
               ];
            case Config::get('constants.type_user.patient'):
                return [
                     'id_request'=>$this->id,
                     'user'=>$this->formatNurse($this->user_nurse),
                     'start_date'=>$this->start_date,
                     'end_date'=>$this->end_date,
                     'start_time'=>$this->start_time,
                     'end_time'=>$this->end_time,         
                ];
            default:
                # code...
                break;
        }
    }
    protected function formatPatient($patient){
        $user = User::select('id','phone','email')->find($patient->user_login);
        return [
            'id'=>$patient->id,
            'name'=>$patient->name,
            'avatar'=>$patient->avatar,
            'phone'=>$user->phone,
            'email'=>$user->email
        ];
    }
    protected function formatNurse($nurse_id){
        $user = User::select('id','phone','email','name','avatar')->find($nurse_id);
        return [
            'id'=>$user->id,
            'name'=>$user->name,
            'avatar'=>$user->avatar,
            'phone'=>$user->phone,
            'email'=>$user->email
        ];
    }
}
