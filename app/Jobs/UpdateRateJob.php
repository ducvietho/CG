<?php

namespace App\Jobs;

use App\Models\Care;
use App\Models\NurseProfile;

class UpdateRateJob extends Job
{
    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $user_nurse;
    public function __construct($user_nurse)
    {
        $this->user_nurse = $user_nurse;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $rate = Care::where('user_nurse',$this->user_nurse)
                    ->where('rate','>',0)
                    ->pluck('rate')->toArray();
        
        $nurse = NurseProfile::where('user_login',$this->user_nurse)->first();
        $nurse->rate = array_sum($rate)/count($rate);
        $nurse->save();
    }
}
