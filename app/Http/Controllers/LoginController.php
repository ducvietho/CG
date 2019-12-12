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

    public function login(Request $request)
    {
        $user = User::where('user_id', $request->user_id)->first();
        if ($user == null) {
            return $this->successResponseMessage(new \stdClass(), 404, "User not found");
        } else {
            $token = $this->jwt->attempt($request->only('user_id', 'password'));
            if ($token == false) {
                return $this->successResponseMessage(new \stdClass(), 413, "Password inccorect");
            } else {
                if (isset($request->fcm_token)) {
                    $user->update([
                        'fcm_token' => $request->fcm_token,
                    ]);
                    $request->request->set('user_id', $user->id);

                }
                $data['token'] = $token;
                $data['user'] = new UserResource($user);
                if ($user->is_register == 0) {
                    $code = 412;
                } else {
                    $code = 200;
                }
                return $this->successResponseMessage($data, $code, "Login success");
            }

        }
    }

    public function loginSocial(Request $request)
    {
        $user = User::where('provide_id', $request->provide_id)->where('type_account', $request->type_account)->first();
        if ($user == null) {
            return $this->successResponseMessage(new \stdClass(), 404, "User not found");
        } else {
            $token = $this->jwt->fromUser($user);
            if (isset($request->fcm_token)) {
                $user->update([
                    'fcm_token' => $request->fcm_token,
                ]);
                $request->request->set('user_id', $user->id);

            }
            $data['token'] = $token;
            $data['user'] = new UserResource($user);
            if ($user->is_register == 0) {
                $code = 412;
            } else {
                $code = 200;
            }
            return $this->successResponseMessage($data, $code, "Login success");
        }

    }

}