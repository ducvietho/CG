<?php


namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\ResourceCollection;

class LogLoginCollection extends ResourceCollection
{
    public function toArray($request)
    {
        $next_page = $this->currentPage() + 1;
        if ($next_page > $this->lastPage() ){
            $next_page = 0;
        }
        return [
            'datas' => LogLoginResource::collection($this->collection),
            'next_page' => $next_page,
            'total_page' => $this->lastPage()
        ];
    }
}