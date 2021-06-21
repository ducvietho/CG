<?php

namespace App\Http\Controllers;

use App\User;
use App\Traits\MediaClass;
use Tymon\JWTAuth\JWTAuth;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;

class RegisterController extends Controller
{
    use MediaClass;
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

    //register normal
    public function register(Request $request){
        $this->validate($request, [
            'email' => 'required|string|email|max:255|unique:users',
            'user_id' =>'required|min:6|unique:users',
            'name' => 'required',
            'password' => 'required|min:6',
            'gender' => 'required|numeric',
            'birthday' => 'required|numeric',
            'phone' => 'required|min:10',
        ]);
        $input = $request->only(['email','user_id','name','password','gender','birthday','phone','city_code','district_code','address','type','type_account']);
        if($request->get('avatar') != null){
            $avatar = $this->upload(0,$request->get('avatar'),0);
            $input['avatar'] = $avatar;
        }
        $user = User::createNew($input);
        $data['token'] = $this->jwt->fromUser($user);
        $data['user'] = new UserResource($user);
        return $this->successResponseMessage($data,200,'Register success');
    }

    public function registerSocial(Request $request){
        $this->validate($request, [
            'email' => 'required|string|email|max:255|unique:users',
            'provide_id' => 'required',
            'name' => 'required',
            'gender' => 'required',
            'birthday' => 'required',
            'phone' => 'required|min:10',
        ]);
        $input = $request->only(['provide_id','email','user_id','name','password','gender','birthday','phone','city_code','district_code','address','type','type_account']);
        if($request->get('avatar') != null){
            $avatar = $this->upload(0,$request->get('avatar'),0);
            $input['avatar'] = $avatar;
        }
        $user = User::createNew($input);
        $data['token'] = $this->jwt->fromUser($user);
        $data['user'] = new UserResource($user);
        return $this->successResponseMessage($data,200,'Register social success');
    }

    /*
     * Check User ID
     */
    public function checkID(Request $request){
        $userId = $request->user_id;
        $user = User::where('user_id',$userId)->first();
        $data['check'] = 0;
        if($user != null){
            $data['check'] = 1;
        }
        return $this->successResponseMessage($data,200,'Check user ID success');
    }

    /*
     * Check Email
     */
    public function checkEmail(Request $request){
        $email = $request->email;
        $user = User::where('email',$email)->first();
        $data['check'] = 0;
        if($user != null){
            $data['check'] = 1;
        }
        return $this->successResponseMessage($data,200,'Check Email success');
    }
}
