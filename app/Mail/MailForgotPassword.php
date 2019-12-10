<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MailForgotPassword extends Mailable{
    
    use Queueable, SerializesModels;

    public $password;
    public $email;

    public function __construct($password,$email){
        $this->password = $password;
        $this->email = $email;
    }
    public function build(){
        return $this->view('mailForgotPassword',compact('password','email'));
    }

}