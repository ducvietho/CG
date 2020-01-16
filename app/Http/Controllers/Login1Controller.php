<?php
namespace App\Http\Controllers;


use App\Models\Log;
use App\User;
use App\MyConst;
use Tymon\JWTAuth\JWTAuth;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;

class Login1Controller extends Controller
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

    public function login(Request $request)
    {
        $user = User::where('user_id', $request->user_id)->firstorFail();
        $token = $this->jwt->attempt($request->only('user_id', 'password'));
        if ($token == false) {
            return $this->successResponseMessage(new \stdClass(), 413, "Password inccorect");
        } else {
            if (isset($request->fcm_token)) {
                $user->update([
                    'fcm_token' => $request->fcm_token,
                ]);
            }
            $data['token'] = $token;
            $data['user'] = new UserResource($user);
            if($user->role == MyConst::ADMIN){
                return $this->successResponseMessage($data, 200, "Login success");
            }
            $request->request->set('user_login',$user->id);
            Log::create($request->all());
            if ($user->is_register == 0) {
                return $this->successResponseMessage($data, 412, "You need to register a profile");
            }
            if($user->type == MyConst::NURSE && $user->is_sign ==0){
                return $this->successResponseMessage($data, 416, "The nurse needs to sign the form");
            }
            return $this->successResponseMessage($data, 200, "Login success");
        }
  
    }

    public function loginSocial(Request $request)
    {
        $user = User::where('provide_id', $request->provide_id)->where('type_account', $request->type_account)->firstorFail();
       
        $token = $this->jwt->fromUser($user);
        if (isset($request->fcm_token)) {
            $user->update([
                'fcm_token' => $request->fcm_token,
            ]);

        }
        $data['token'] = $token;
        $data['user'] = new UserResource($user);
        $request->request->set('user_login',$user->id);
        Log::create($request->all());
        if ($user->is_register == 0) {
            return $this->successResponseMessage($data, 412, "You need to register a profile");
        } 
        if($user->type == MyConst::NURSE && $user->is_sign ==0){
            return $this->successResponseMessage($data, 416, "The nurse needs to sign the form");
        }

        return $this->successResponseMessage($data, 200, "Login success");

    }

}