<?php


namespace App\Jobs;


use App\Models\Care;
use App\Models\Notification;
use App\Models\NurseInterest;

class DeletePatientJob extends Job
{
    protected $idPatient;
    public function __construct($idPatient)
    {
        $this->idPatient = $idPatient;
    }
    public function handle()
    {
        Notification::where('user_patient',$this->idPatient)->delete();
        NurseInterest::where('user_patient',$this->idPatient)->delete();
        Care::where('user_patient',$this->idPatient)->delete();
    }
}