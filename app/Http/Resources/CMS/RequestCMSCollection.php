<?php


namespace App\Http\Resources\CMS;


use Illuminate\Http\Resources\Json\ResourceCollection;

class RequestCMSCollection extends ResourceCollection
{
    public function toArray($request)
    {

        return [
            'datas' => RequestCMSResource::collection($this->collection),
            'total_page' => $this->lastPage()
        ];
    }
}