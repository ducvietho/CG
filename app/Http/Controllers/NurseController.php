<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\Patient;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use App\Http\Resources\PatientCollection;

class NurseController extends Controller
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
     * Get list patient 
     * List orderby location code
     */
    public function homePatient(Request $request){
        $code_add = Auth::user()->district_code;
        $data = Patient::search('code_add',$code_add)->paginate();
        return $this->successResponseMessage(new PatientCollection($data), 200, "Get home success");
    }
    /**
     * Get patient detail
     */
    /** 
     * Function register nurse
    */
    public function registerNurse(Request $request){
        //validate input
        $this->validate($request,[
            'code_add'=>'required',
            'start_date'=>'requeired',
            'end_date'=>'required',
            'start_time'=>'required',
            'end_time'=>'required',
            'address'=>'required|min:1|max:3',
            'is_certificate'=>'required|min:0|max:1',
            'description'=>'string'
        ]);
    }
}
