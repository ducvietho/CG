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

class NurseCareCollection extends ResourceCollection
{
    public function toArray($request)
    {
        $next_page = $this->currentPage() + 1;
        if ($next_page > $this->lastPage() ){
            $next_page = 0;
        }
        $number_request = 0;
        if($this->currentPage() == 1 && $request->status == 1){
            $number_request = Care::where('type',1)->where('user_login',Auth::id())->where('status',0)->count();
        }
        return [
            'datas' => NurseCareResource::collection($this->collection),
            'next_page' => $next_page,
            'number_request' => $number_request
        ];
    }
}