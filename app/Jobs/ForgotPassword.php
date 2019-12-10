<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Mail\MailForgotPassword;
use Illuminate\Support\Facades\Mail;

class ForgotPassword extends Job
{

    protected $password;
    protected $email;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($password, $email)
    {
        $this->password = $password;
        $this->email = $email;
    }

    /**
     * Execute the job.cl
     *
     * @return void
     */
    public function handle()
    {
        $sendemail = new MailForgotPassword($this->password, $this->email);
        Mail::to($this->email)->send($sendemail);
    }
}
