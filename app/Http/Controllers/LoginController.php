<?php


namespace App\Http\Controllers;


use App\Http\Resources\UserResource;
use App\Traits\ApiResponser;
use App\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;

class LoginController extends Controller
{
    use ApiResponser;
    protected $jwt;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(JWTAuth $jwt)
    {
        $this->jwt = $jwt;
    }
    public function login(Request $request){
        $token = $this->jwt->attempt($request->only('user_id','password'));
        if ($token == false){
            return $this->successResponseMessage(new \stdClass(), 413, "Password inccorect");
        }else{
            $user = User::where('user_id',$request->user_id)->where('type_account',0)->first();
            if (isset($request->fcm_token)) {
                $user->update([
                    'fcm_token' => $request->fcm_token,
                ]);
                $request->request->set('user_id',$user->id);

            }
            $data['token'] = $token;
            $data['user'] = new UserResource($user);
            return $this->successResponseMessage($data, 200, "Login success");
        }
    }
}