<?php


namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class BannerResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'=>$this->id,
            'image'=>$this->image,
            'name'=> $this->name,
        ];
    }
}