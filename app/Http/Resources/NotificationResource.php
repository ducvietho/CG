<?php


namespace App\Http\Resources;


use App\User;
use App\Models\Patient;
use App\Models\Notification;
use App\Models\NurseProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'user'=>$this->formatUser($this->user_from),
            'patient'=>$this->formatPatient($this->user_patient),
            'type'=>$this->type,
            'id_request'=>$this->request_id,
            'created'=>strtotime($this->created_at)
        ];
    }
    protected function formatUser($id_user){
        $user = User::find($id_user);
        return ($user == null)? new \stdClass(): [
            'id'=>$user->id,
            'name'=>$user->name,
            'avatar'=>$user->avatar,
            'birthday'=>$user->birthday
        ];
    }
    protected function formatPatient($id_user){
        $patient = Patient::find($id_user);
        return ($patient == null)? new \stdClass(): [
            'id'=>$patient->id,
            'avatar'=>$patient->avatar,
            'name'=>$patient->name,
            'birthday'=> $patient->birthday
        ];
    }
}