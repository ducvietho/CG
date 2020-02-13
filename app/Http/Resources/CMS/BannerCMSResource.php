<?php


namespace App\Http\Resources\CMS;


use Illuminate\Http\Resources\Json\JsonResource;

class BannerCMSResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'=>$this->id,
            'image'=>$this->image,
            'active' => (int)$this->active,
            'name'=> $this->name,
            'link' => ($this->link != null) ? $this->link : '',
            'user' =>[
                'id' => $this->user->id,
                'name' =>$this->user->name
            ]
        ];
    }
}