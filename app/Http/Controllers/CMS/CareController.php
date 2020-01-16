<?php


namespace App\Http\Controllers\CMS;


use App\Http\Controllers\Controller;
use App\Http\Resources\CMS\RequestCMSCollection;
use App\Models\Care;
use App\MyConst;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class CareController extends Controller
{
    use ApiResponser;

    public function getRequests(Request $request)
    {
        $query = Care::query();
        if (isset($request->start_date) && $request->start_date != null) {
            $query = $query->whereDate('created_at', '>=', $request->start_date);
        }
        if (isset($request->end_date) && $request->end_date != null) {
            $query = $query->whereDate('created_at', '<=', $request->end_date);
        }
        if (isset($request->status) && $request->status >= 0){
            $query = $query->where('status',$request->status);
        }
            $collection = $query->orderBy('created_at', 'DESC')->paginate(MyConst::PAGINATE);
        return $this->successResponseMessage(new RequestCMSCollection($collection), 200, 'Get request success');


    }
}