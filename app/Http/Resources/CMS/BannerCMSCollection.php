<?php


namespace App\Http\Resources\CMS;


use Illuminate\Http\Resources\Json\ResourceCollection;

class BannerCMSCollection extends ResourceCollection
{
    public function toArray($request)
    {

        return [
            'datas' => BannerCMSResource::collection($this->collection),
            'total_page' => $this->lastPage()
        ];
    }
}