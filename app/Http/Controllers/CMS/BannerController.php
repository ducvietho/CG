<?php


namespace App\Http\Controllers\CMS;


use App\Http\Controllers\Controller;
use App\Http\Resources\CMS\BannerCMSCollection;
use App\Http\Resources\CMS\BannerCMSResource;
use App\Models\Banner;
use App\MyConst;
use App\Traits\ApiResponser;
use App\Traits\MediaClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BannerController extends Controller
{
    use MediaClass;
    use ApiResponser;

    public function create(Request $request)
    {
        $image = $this->upload(MyConst::BANNER, $request->base64, Auth::id());
        $request->request->set('image', $image);
        $request->request->set('user_login',Auth::id());
        $banner = Banner::create($request->all());
        return $this->successResponseMessage(new BannerCMSResource($banner), 200, 'Create banner success');

    }

    public function getList(Request $request)
    {
        $collection = Banner::orderBy('active','DESC')->orderBy('created_at','DESC')->paginate(MyConst::PAGINATE);
        return $this->successResponseMessage(new BannerCMSCollection($collection), 200, 'Create banner success');

    }
    public function update(Request $request)
    {
        $banner = Banner::find($request->id);
        if(isset($request->base64) && $request->base64 != null){
            $image = $this->upload(MyConst::BANNER, $request->base64, Auth::id());
            $request->request->set('image', $image);
        }
        $banner->update($request->all());
        return $this->successResponseMessage(new BannerCMSResource($banner), 200, 'Update banner success');

    }
    public function delete(Request $request)
    {
        $banner = Banner::where('id',$request->id)->delete();
        return $this->successResponseMessage(new \stdClass(), 200, 'Delete banner success');

    }
}