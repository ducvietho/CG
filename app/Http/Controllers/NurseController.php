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
}
