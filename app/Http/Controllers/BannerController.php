<?php


namespace App\Http\Controllers;


use App\Http\Resources\BannerResource;
use App\Models\Banner;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    use ApiResponser;
    public function get(Request $request){
        $banner = Banner::where('active',1)->orderBy('created_at','DESC')->first();
        return $this->successResponseMessage(new BannerResource($banner),200,'Get banner success');
    }
}