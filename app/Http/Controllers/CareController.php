<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\Care;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use App\Http\Resources\CareResource;
use Illuminate\Support\Facades\Config;

class CareController extends Controller
{
    use ApiResponser;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Send request care
     */
    public function requestCare(Request $request){
        $this->validate([
            'user_id'=>'required',
            'start_date'=>'required',
            'end_date'=>'required',
            'start_time'=>'required',
            'end_time'=>'required'
        ]);
        $type_user = Auth::user()->type;
        
        switch ($type_user) {
            
            case Config::get('constants.type_user.nurse'):
                // Nurse sending request care patient
                //Gan user_request = user_patient
                $care = Care::create([
                    'user_nurse'=>Auth::id(),
                    'user_patient'=>$request->user_id,
                    'user_login'=>Auth::id(),
                    'type'=>Config::get('constants.type_request.nurse'),
                    'status'=>Config::get('constants.request_status.requesting'),
                    'start_date'=>$request->start_date,
                    'end_date'=>$request->end_date,
                    'start_time'=>$request->start_time,
                    'end_time'=>$request->end_time,
                    'rate'=>0
                ]);      
                return $this->successResponseMessage(new CareResource($care,$type_user), 200, "Request success");
            case Config::get('constants.type_user.patient'):
                // User login sending request nurse care
                return $this->successResponseMessage(new \stdClass(), 200, "Request success");
            default:
                return;
        }
    }
    
}
