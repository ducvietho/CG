<?php


namespace App\Http\Resources\CMS;


use Illuminate\Http\Resources\Json\ResourceCollection;

class PatientHistoryCareCollection extends ResourceCollection
{
    public function toArray($request)
    {

        return [
            'datas' => PatientHistoryCareResource::collection($this->collection),
            'total_page' => $this->lastPage()
        ];
    }
}