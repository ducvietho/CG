<?php
/**
 * Created by PhpStorm.
 * User: ducvietho
 * Date: 12/15/2019
 * Time: 11:39
 */

namespace App\Http\Resources;


use App\Models\Care;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;

class CareCollection extends ResourceCollection
{
    public function toArray($request)
    {
        $next_page = $this->currentPage() + 1;
        if ($next_page > $this->lastPage() ){
            $next_page = 0;
        }
        return [
            'datas' => CareResource::collection($this->collection),
            'next_page' => $next_page,
        ];
    }
}