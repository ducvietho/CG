<?php


namespace App\Http\Controllers\CMS;


use App\Models\Log;
use App\MyConst;
use App\Traits\ApiResponser;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\LogLoginCollection;


class UserController extends Controller
{
    use ApiResponser;
    public function getLoginLogs(Request $request){
        $user_login = $request->user_login;
        $logs = Log::where('user_login',$user_login)->orderBy('created_at','DESC')->paginate(MyConst::PAGINATE);
        return $this->successResponseMessage(new LogLoginCollection($logs),200,'Get log login success');

    }

    public function getListUser(Request $request){
        $users = User::orderBy('created_at','DESC')->paginate(MyConst::PAGINATE);
        return $this->successResponseMessage(new UserCMSCollection($users),200,'Get list user success');

    }
}