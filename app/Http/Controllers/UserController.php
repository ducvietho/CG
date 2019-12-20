<?php


namespace App\Http\Controllers;


use App\Http\Resources\NotificationCollection;
use App\Http\Resources\UserResource;
use App\Jobs\ForgotPassword;
use App\Models\Notification;
use App\Models\NurseProfile;
use App\Models\Patient;
use App\Traits\ApiResponser;
use App\Traits\MediaClass;
use App\User;
use Elasticsearch\ClientBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\JWTAuth;

class UserController extends Controller
{
    use ApiResponser;
    use MediaClass;
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
            Patient::where('user_login')->delete();
            $nurse = NurseProfile::where('user_login')->delete();
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

    public function logout(Request $request)
    {
        $user = User::find(Auth::id());
        $user->fcm_token = '';
        $user->save();
        Auth::logout();
        $this->jwt->invalidate();
        return $this->successResponseMessage(new \stdClass(), 200, "Logout success");
    }

    public function editProfile(Request $request)
    {
        $user = User::find(Auth::id());
        $avatar = $user->avatar;
        if (isset($request->avatar) && $request->avatar != null) {
            $avatar = $this->upload(0, $request->avatar, Auth::id());
        }
        $user->avatar = $avatar;
        $user->name = isset($request->name) ? $request->name : $user->name;
        $user->user_name = isset($request->user_name) ? $request->user_name : $user->user_name;
        $user->gender = isset($request->gender) ? $request->gender : $user->gender;
        $user->birthday = isset($request->birthday) ? $request->birthday : $user->birthday;
        $user->phone = isset($request->phone) ? $request->phone : $user->phone;
        $user->email = isset($request->email) ? $request->email : $user->email;
        $user->code_address = isset($request->city_code) ? $request->city_code : $user->code_address;
        $user->district_code = isset($request->district_code) ? $request->district_code : $user->district_code;
        $user->address_detail = isset($request->address) ? $request->address : $user->address_detail;
        $user->save();
        return $this->successResponseMessage(new UserResource($user), 200, 'Edit profile success');
    }
    /*
     * Turn on/off care
     */
    public function settingCare(Request $request)
    {
        $user = User::find(Auth::id());
        $settingCare = $request->setting_care;
        $settingCare = ($settingCare == 0) ? 1 : 0;
        $user->setting_care = $settingCare;
        $user->save();
        return $this->successResponseMessage(new UserResource($user), 200, 'Setting care success');
    }

    /*
     * List notification
     */

    public function getNoti(Request $request){
        $notis = Notification::where('user_to',Auth::id())->paginate();
        return $this->successResponseMessage(new NotificationCollection($notis),200,'Get notification success');

    }
}