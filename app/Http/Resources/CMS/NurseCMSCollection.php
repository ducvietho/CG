<?php


namespace App\Http\Resources\CMS;


use Illuminate\Http\Resources\Json\ResourceCollection;

class NurseCMSCollection extends ResourceCollection
{
    public function toArray($request)
    {
        $next_page = $this->currentPage() + 1;
        if ($next_page > $this->lastPage() ){
            $next_page = 0;
        }
        return [
            'datas' => NurseCMSResource::collection($this->collection),
            'total_page' => $this->lastPage()
        ];
    }
}