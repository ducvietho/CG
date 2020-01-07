<?php


namespace App\Http\Resources;


use App\Models\Patient;
use App\MyConst;
use App\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class CareShortResource extends JsonResource
{

    public function toArray($request)
    {
        $type_user = Auth::user()->type;
        switch ($type_user) {
            case MyConst::NURSER_REQUEST:
                $user_patient = Patient::select('name','id','user_login','avatar')->find($this->user_patient);
                return [
                    'id_request'=>$this->id,
                    'user'=>$this->formatPatient($user_patient),
                    'created'=>strtotime($this->created_at)

                ];
            case MyConst::PATIENT_REQUEST:
                return [
                    'id_request'=>$this->id,
                    'user'=>$this->formatNurse($this->user_nurse),
                    'created'=>strtotime($this->created_at)
                ];
            default:
                # code...
                break;
        }
    }
    protected function formatPatient($patient){

        return [
            'id'=>$patient->id,
            'name'=>$patient->name,
            'avatar'=>$patient->avatar,

        ];
    }
    protected function formatNurse($nurse_id){
        $user = User::select('id','phone','email','name','avatar')->find($nurse_id);
        return [
            'id'=>$user->id,
            'name'=>$user->name,
            'avatar'=>$user->avatar,
        ];
    }
}