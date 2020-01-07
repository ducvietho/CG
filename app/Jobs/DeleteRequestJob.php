<?php


namespace App\Jobs;


use App\Models\Notification;

class DeleteRequestJob extends Job
{
    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $requestId;
    public function __construct($requestId)
    {
        $this->requestId = $requestId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Notification::whereIn('request_id',$this->requestId)->delete();
    }
}