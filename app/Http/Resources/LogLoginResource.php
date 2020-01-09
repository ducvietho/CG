<?php


namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class LogLoginResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'device_name' => $this->device_name,
            'version' => $this->version,
            'created' => strtotime($this->created_at)
        ];
    }
}