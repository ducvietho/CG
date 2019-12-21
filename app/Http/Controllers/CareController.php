<?php

namespace App\Http\Controllers;

use Auth;
use App\MyConst;
use App\Models\Care;
use App\Jobs\RequestJob;
use App\Jobs\AcceptedJob;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use App\Http\Resources\CareDetailResource;

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
     * Send Nurse request care
     */
    public function requestCare(Request $request){
        $this->validate($request,[
            'user_patient'=>'required',
            'start_date'=>'required',
            'end_date'=>'required',
            'start_time'=>'required',
            'end_time'=>'required'
        ]);
        $type_user = Auth::user()->type;
        // Nurse sending request care patient
        //Gan user_request = user_patient
        $care = Care::firstorCreate([
            'user_nurse'=>Auth::id(),
            'user_patient'=>$request->user_patient,
            'user_login'=>Auth::id(),
            'type'=>MyConst::NURSER_REQUEST,
            'status'=>MyConst::REQUESTING,
            'start_date'=>$request->start_date,
            'end_date'=>$request->end_date,
            'start_time'=>$request->start_time,
            'end_time'=>$request->end_time,
            'rate'=>0
        ]);
        //Sending noti nurse request care
        dispatch(new RequestJob(Auth::id(),0,$request->user_patient,MyConst::NOTI_NURSE_REQUEST,$care->id));
        return $this->successResponseMessage(new CareDetailResource($care,$type_user), 200, "Request success");       
    }
    /**
     * Nurse accept request
     */
    public function nurseAcept(Request $request){
        $this->validate($request,[
            'id_request'=>'required'
        ]);
        $type_user = Auth::user()->type;
        if($type_user != MyConst::NURSE){
            return $this->successResponseMessage(new \stdClass(), 418, "Permision denied"); 
        }

        $care = Care::where('id',$request->id_request)
                    ->where('type',MyConst::PATIENT_REQUEST)
                    ->firstorFail();
        $care->status = MyConst::ACCEPTED;
        $care->save();
        dispatch(new AcceptedJob(Auth::id(),0,MyConst::NOTI_NURSE_ACCEPT,$care->id, $care->user_patient));
        return $this->successResponseMessage(new CareDetailResource($care,$type_user), 200, "Request success"); 
    }
    /**
     * Patient request care
     */
    public function patientRequest(Request $request){
        $this->validate($request,[
            'user_nurse'=>'required',
            'user_patient'=>'required',
            'start_date'=>'required',
            'end_date'=>'required',
            'start_time'=>'required',
            'end_time'=>'required'
        ]);
        $type_user = Auth::user()->type;
        // Nurse sending request care patient
        //Gan user_request = user_patient
        $care = Care::firstorCreate([
            'user_nurse'=>$request->user_nurse,
            'user_patient'=>$request->user_patient,
            'user_login'=>Auth::id(),
            'type'=>MyConst::PATIENT_REQUEST,
            'status'=>MyConst::REQUESTING,
            'start_date'=>$request->start_date,
            'end_date'=>$request->end_date,
            'start_time'=>$request->start_time,
            'end_time'=>$request->end_time,
            'rate'=>0
        ]);
        //Sending noti nurse request care
        dispatch(new RequestJob(Auth::id(),$request->user_nurse,$request->user_patient,MyConst::NOTI_PATIENT_REQUEST,$care->id));
        return $this->successResponseMessage(new CareDetailResource($care,$type_user), 200, "Request success"); 
    }
    /**
     * Patient accepted request
     */
    public function patientAcept(Request $request){
        $this->validate($request,[
            'id_request'=>'required'
        ]);
        $type_user = Auth::user()->type;
        if($type_user != MyConst::PATIENT){
            return $this->successResponseMessage(new \stdClass(), 418, "Permision denied"); 
        }

        $care = Care::where('id',$request->id_request)
                    ->where('type',MyConst::NURSER_REQUEST)
                    ->firstorFail();
        $care->status = MyConst::ACCEPTED;
        $care->save();
        dispatch(new AcceptedJob(Auth::id(),$care->user_nurse,MyConst::NOTI_PATIENT_ACCEPT,$care->id, $care->user_patient));
        return $this->successResponseMessage(new CareDetailResource($care,$type_user), 200, "Request success"); 
    }

    /*
     * Detail Care
     */
    public function detail(Request $request){
        $idCare = $request->id_request;
        $care = Care::findOrFail($idCare);
        $type_user = Auth::user()->type;
        return $this->successResponseMessage(new CareDetailResource($care,$type_user), 200, "Detail request success");
    }
}
