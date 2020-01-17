<?php


namespace App\Http\Controllers\CMS;


use App\Http\Resources\CMS\LogLoginCollection;
use App\Http\Resources\CMS\UserCMSCollection;
use App\Models\Log;
use App\MyConst;
use App\Traits\ApiResponser;
use App\Traits\FullTextSearch;
use App\Traits\ProcessTextSearch;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    use ApiResponser;
    use ProcessTextSearch;
    public function getLoginLogs(Request $request){
        $user_login = $request->user_login;
        $logs = Log::where('user_login',$user_login)->orderBy('created_at','DESC')->paginate(MyConst::PAGINATE);
        return $this->successResponseMessage(new LogLoginCollection($logs),200,'Get log login success');

    }

    public function getListUser(Request $request){
        $query = User::query();
        if(isset($request->name) && $request->name != null){
            $query = $query->where('name','like',$this->fullText($request->name));
        }
        $users = $query->where('id','!=',Auth::id())->orderBy('created_at','DESC')->paginate(MyConst::PAGINATE);
        return $this->successResponseMessage(new UserCMSCollection($users),200,'Get list user success');

    }
}