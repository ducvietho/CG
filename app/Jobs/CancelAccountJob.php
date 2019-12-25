<?php

namespace App\Jobs;

use App\User;
use App\MyConst;
use App\Models\Care;
use App\Models\Patient;
use App\Models\Notification;
use App\Models\NurseProfile;
use App\Models\NurseInterest;
use App\Models\PatientInterest;

class CancelAccountJob extends Job
{
    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $user_id;
    protected $type_user;
    public function __construct($user_id,$type_user)
    {
        $this->user_id = $user_id;
        $this->type_user = $type_user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        switch ($this->type_user) {
            case MyConst::NURSE:
                //xoa table user
                $user = User::findorFail($this->user_id);
                $user->delete();
                //xoa table profile nurse
                $nurse = NurseProfile::where('user_login',$this->user_id)->first();
                if($nurse != null){
                    $nurse->delete();
                }
                //xoas table request
                $request = Care::where('user_nurse',$this->user_id);
                $request_id = $request->pluck('id')->toArray();
                $request->delete();
                //xoas table interest
                $patient_interest = PatientInterest::where('user_nurse',$this->user_id)->delete();
                //xoa table nurse_interest_patient
                $nurse_interest = NurseInterest::where('user_nurse',$this->user_id)->delete();
                //xoa table notification
                $notification = Notification::whereIn('request_id',$request_id)->delete();
                break;
            case MyConst::PATIENT:
                //xoa table user
                $user = User::findorFail($this->user_id);
                $user->delete();
                //xoa table patient
                $patient = Patient::where('user_login',$this->user_id);
                $patient_id = $patient->pluck('id')->toArray();
                $patient->delete();
                //xoas table request
                $request = Care::whereIn('user_patient',$patient_id);
                $request_id = $request->pluck('id')->toArray();
                $request->delete();
                //xoas table interest
                PatientInterest::where('user_login',$this->user_id)->delete();
                //xoa table nurse_interest_patient
                NurseInterest::whereIn('user_patient',$patient_id)->delete();
                //xoa table notification
                Notification::whereIn('request_id',$request_id)->delete();
                break;
            default:
                # code...
                break;
        }
    }
}
