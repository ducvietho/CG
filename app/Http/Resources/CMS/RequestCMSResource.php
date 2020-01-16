<?php


namespace App\Http\Resources\CMS;


use Illuminate\Http\Resources\Json\JsonResource;

class RequestCMSResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id_request' => $this->id,
            'created' => strtotime($this->created_at),
            'type' => $this->type

        ];
    }
}