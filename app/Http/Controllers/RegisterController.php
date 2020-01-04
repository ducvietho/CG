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
            'user_name' => 'required',
            'gender' => 'required',
            'birthday' => 'required',
            'phone' => 'required|min:10',
        ]);
        $user = User::createNew($request->all());
        if($request->avatar != null){
            $avatar = $this->upload(0,$request->avatar,$user->id);
            $user->avatar = $avatar;
            $user->save();
        }
        $data['token'] = $this->jwt->fromUser($user);
        $data['user'] = new UserResource($user);
        return $this->successResponseMessage($data,200,'Register success');
    }

    public function registerSocial(Request $request){
        $this->validate($request, [
            'email' => 'required|string|email|max:255|unique:users',
            'provide_id' => 'required',
            'name' => 'required',
            'user_name' => 'required',
            'gender' => 'required',
            'birthday' => 'required',
            'phone' => 'required|min:10',
        ]);
        $request->request->set('user_id','');
        $request->request->set('password','');
        $user = User::createNew($request->all());
        if($request->avatar != null){
            $avatar = $this->upload(0,$request->avatar,$user->id);
            $user->avatar = $avatar;
            $user->save();
        }
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
}
