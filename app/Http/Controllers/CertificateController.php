<?php


namespace App\Http\Controllers;

use Auth;
use App\User;
use App\MyConst;
use App\Traits\MediaClass;
use App\Models\Certificate;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;

class CertificateController extends Controller
{
    use MediaClass;
    use ApiResponser;
    protected $jwt;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    
    public function signCertificate(Request $request){
        $this->validate($request,[
            'image'=>'required'
        ]);
        if(Auth::user()->type != MyConst::NURSE){
            return $this->successResponseMessage($data, 418, "Permision denined");
        }
        if($request->image != null){
            $sign_certificate = $this->upload(MyConst::CERTIFICATE,$request->image,Auth::id());
            $certificate = Certificate::where('user_login',Auth::id())->first();
            if($certificate != null){
                $certificate->image = $sign_certificate;
                $certificate->save();
            }else{
                $certificate = Certificate::create([
                    'image'=>$sign_certificate,
                    'user_login'=>Auth::id(),
                    'status'=>1,
                    'type'=>1
                ]);
            }
            
            Auth::user()->is_sign =1;
            Auth::user()->save();
        }
        return $this->successResponseMessage(new UserResource(Auth::user()), 200, "Sign certificate success");
    }
    public function getCertificate(Request $request){
        if(Auth::user()->type != MyConst::NURSE){
            return $this->successResponseMessage($data, 418, "Permision denined");
        }
        $certificate = Certificate::where('user_login',Auth::id())->first();
        $data = [
            'name'=>Auth::user()->name,
            'birthday'=>Auth::user()->birthday,
            'gender'=>Auth::user()->gender,
            'phone'=>Auth::user()->phone,
            'sign'=>$certificate->image,
            'created'=>strtotime($certificate->created_at)
        ];
        return $this->successResponseMessage($data, 200, "Get certificate success");
    }
    

}