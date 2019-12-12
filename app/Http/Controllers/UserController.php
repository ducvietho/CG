<?php


namespace App\Http\Controllers;


use App\Http\Resources\UserResource;
use App\Jobs\ForgotPassword;
use App\Traits\ApiResponser;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\JWTAuth;

class UserController extends Controller
{
    use ApiResponser;
    protected $jwt;
    public function __construct(JWTAuth $jwt)
    {
        $this->jwt = $jwt;
    }

    public function findID(Request $request)
    {
        $email = $request->email;
        $user = User::where('email', $email)->first();
        if ($user == null) {
            return $this->successResponseMessage(new \stdClass(), 404, 'Not found user');
        } else {
            return $this->successResponseMessage(['user_id' => $user->user_id], 200, 'Find user success');
        }
    }

    public function delete(Request $request)
    {
        $userId = Auth::id();
        $user = User::find($userId);
        if ($user->email == $request->email) {
            $user->delete();
            return $this->successResponseMessage(new \stdClass(), 200, 'Delete user success');
        } else {
            return $this->successResponseMessage(new \stdClass(), 413, 'Email incorrect');
        }
    }

    public function forgotPass(Request $request)
    {
        $userId = $request->user_id;
        $email = $request->email;
        $user = User::where('user_id', $userId)->first();
        if ($user == null) {
            return $this->successResponseMessage(new \stdClass(), 404, 'Not found user');
        } else {
            $pass = str_random(6);
            $this->dispatch(new ForgotPassword($pass, $user->email));
            $password = Hash::make($pass);
            $user->password = $password;
            $user->save();
            return $this->successResponseMessage(new \stdClass(), 200, 'Get password success');
        }

    }

    public function changePass(Request $request)
    {
        $user_id = Auth::id();
        $old_password = $request->old_password;
        if (password_verify($old_password, Auth::user()->password)) {
            $this->validate($request, [
                'new_password' => 'required|min:6',
            ]);
            $password = Hash::make($request->new_password);
            User::where('id', $user_id)->update(['password' => $password]);
            $status = 200;
            $message = 'Change password successfull';
            $this->jwt->invalidate();
            $user = User::find($user_id);
            $this->jwt->invalidate();
            $data['token'] = $this->jwt->fromUser($user);
        } else {
            $data = new \stdClass();
            $status = 413;
            $message = "Old password incorrect";
        }
        return $this->successResponseMessage($data, $status, $message);
    }

    public function logout(Request $request){
        $user = User::find(Auth::id());
        $user->fcm_token = '';
        $user->save();
        Auth::logout();
        $this->jwt->invalidate();
        return $this->successResponseMessage(new \stdClass(), 200, "Logout success");
    }
}