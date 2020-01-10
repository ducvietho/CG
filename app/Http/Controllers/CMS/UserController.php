<?php


namespace App\Http\Controllers\CMS;


use App\Http\Controllers\Controller;
use App\Http\Resources\LogLoginCollection;
use App\Models\Log;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;


class UserController extends Controller
{
    use ApiResponser;
    public function getLoginLogs(Request $request){
        $user_login = $request->user_login;
        $logs = Log::where('user_login',$user_login)->orderBy('created_at','DESC')->paginate();
        return $this->successResponseMessage(new LogLoginCollection($logs),200,'Get log login success');

    }
}