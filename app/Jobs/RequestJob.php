<?php

namespace App\Jobs;

use App\User;
use App\Models\Patient;
use App\Models\Notification;
use App\Traits\PushNotiController;
use App\Http\Resources\NotificationResource;

class RequestJob extends Job
{
    use PushNotiController;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $user_from;
    protected $user_to;
    protected $user_patient;
    protected $type;
    protected $request_id;
    public function __construct($user_from,$user_to,$user_patient,$type,$request_id)
    {
        $this->user_from = $user_from;
        $this->user_to = $user_to;
        $this->user_patient = $user_patient;
        $this->type = $type;
        $this->request_id = $request_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        if($this->user_to == 0){
            $user_id_to = Patient::select('user_login')->findorFail($this->user_patient)->user_login;
        }else{
            $user_id_to = $this->user_to;
        }
        $data = [
            'user_from'=>$this->user_from,
            'user_to'=>$user_id_to,
            'user_patient'=>$this->user_patient,
            'type'=>$this->type,
            'request_id'=>$this->request_id,
            'unread'=>0,
            'unwatch'=>0
        ];
        $request_exist = Notification::where('request_id',$this->request_id);
        if($request_exist){
            $request_exist->delete();
            $noti = Notification::create($data);
        }else{
            $noti = Notification::create($data);
        }
        $user_key = User::select('fcm_token')->findorFail($user_id_to);
        $this->pushNotification($user_key->fcm_token,json_encode(new NotificationResource($noti)));
        
    }
}
