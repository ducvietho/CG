<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Traits\ApiResponser;
use App\Traits\MediaClass;
use App\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;

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
}
