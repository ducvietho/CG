<?php


namespace App\Http\Resources;

use App\User;
use App\MyConst;
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
                $user_patient = Patient::select('name','id','user_login')->find($this->user_patient);
               return [
                    'id_request'=>$this->id,
                    'user'=>$this->formatPatient($user_patient),
                    'status'=>$this->status,
                    'start_date'=>$this->start_date,
                    'end_date'=>$this->end_date,
                    'start_time'=>$this->start_time,
                    'end_time'=>$this->end_time,
                    'rate' => $this->rate
               ];
            case MyConst::PATIENT_REQUEST:
                $user_patient = Patient::select('name','id','user_login')->find($this->user_patient);
                return [
                     'id_request'=>$this->id,
                     'user'=>$this->formatNurse($this->user_nurse),
                     'name_patient' =>$user_patient->name,
                     'status'=>$this->status,
                     'start_date'=>$this->start_date,
                     'end_date'=>$this->end_date,
                     'start_time'=>$this->start_time,
                     'end_time'=>$this->end_time,
                     'rate' => $this->rate
                ];
            default:
                # code...
                break;
        }
    }
    protected function formatPatient($patient){
        $user = User::select('id','phone','email','avatar')->find($patient->user_login);
        return [
            'id'=>$patient->id,
            'name'=>$patient->name,
            'avatar'=>$user->avatar,
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
