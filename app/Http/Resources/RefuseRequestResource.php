<?php


namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class RefuseRequestResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'reason' => $this->reason,
            'message' => $this->message
        ];
    }

}