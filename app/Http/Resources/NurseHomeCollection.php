<?php


namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\ResourceCollection;

class NurseHomeCollection extends ResourceCollection
{
 public function toArray($request)
 {
     $next_page = $this->currentPage() + 1;
     if ($next_page > $this->lastPage() ){
         $next_page = 0;
     }
     return [
         'datas' => NurseHomeResource::collection($this->collection),
         'next_page' => $next_page,
     ];
 }

}