<?php


namespace App\Http\Controllers;


use App\Http\Resources\BannerCollection;
use App\Models\Banner;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    use ApiResponser;
    public function get(Request $request){
        $banner = Banner::where('active',1)->orderBy('created_at','DESC')->limit(10)->get();
        if($banner != null){
            return $this->successResponseMessage(new BannerCollection($banner),200,'Get banner success');
        }else{
            return $this->successResponseMessage(new \stdClass(),200,'Get banner success');
        }

    }
}