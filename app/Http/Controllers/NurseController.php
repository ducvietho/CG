<?php

namespace App\Http\Controllers;

use App\Http\Resources\NurseHomeCollection;
use App\Http\Resources\NurseProfileDetailResource;
use App\Http\Resources\PatientCollection;
use App\Models\Care;
use App\Models\NurseInterest;
use App\Models\NurseProfile;
use App\Models\Patient;
use App\Traits\ApiResponser;
use App\Traits\FullTextSearch;
use Auth;
use Elasticquent\ElasticquentResultCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NurseController extends Controller
{
    use ApiResponser;
    use FullTextSearch;

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
    public function homePatient(Request $request)
    {
        $code_add = Auth::user()->district_code;
        $data = DB::table('patients')
            ->orderByRaw("(abs(code_add - $code_add)) asc")
            ->paginate();
        return $this->successResponseMessage(new PatientCollection($data), 200, "Get home success");
    }
    /**
     * Get patient detail
     */
    /**
     * Function register nurse
     */
    public function registerNurse(Request $request)
    {
        //validate input
        $this->validate($request, [
            'start_date' => 'required',
            'end_date' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'address' => 'required|min:1|max:3',
            'is_certificate' => 'required|min:0|max:1',
            'description' => 'string'
        ]);
        $request->request->add(['code_add' => Auth::user()->district_code]);
        $request->request->add(['user_login' => Auth::id()]);
        //Update status register user
        $user = Auth::user();
        $user->is_register = 1;
        $user->save();
        //end
        $nurse_profile = NurseProfile::firstOrCreate($request->all());
        return $this->successResponseMessage(new NurseProfileDetailResource($nurse_profile), 200, "Register nurse profile success");
    }

    /**
     * Tab suggest patient
     */
    public function suggest(Request $request)
    {
        // Lay patient da duoc cham soc
        $user_id = NurseInterest::pluck('user_patient');

        $code_add = Auth::user()->district_code;

        $data = Patient::search($code_add)->whereNotIn('id', $user_id)->paginate();
        return $this->successResponseMessage(new PatientCollection($data), 200, "Get tab suggest success");
    }

    /**
     * Tab interest
     */
    public function interest(Request $request)
    {
        $user_id = NurseInterest::where('user_nurse', Auth::id())->pluck('user_patient');
        $code_add = Auth::user()->district_code;
        $data = Patient::search($code_add)->whereIn('id', $user_id)->paginate();
        return $this->successResponseMessage(new PatientCollection($data), 200, "Get interest success");
    }

    public function manager(Request $request)
    {
        $number_request = Care::where('type', 2)
            ->where('user_nurse', Auth::id())
            ->where('user_login', Auth::id())
            ->count();
        $status = $request->status;
        $user_patient = Care::where('status', $status)
            ->where('user_nurse', Auth::id())
            ->where('user_login', Auth::id())
            ->pluck('user_patient');
        $code_add = Auth::user()->district_code;
        $data = Patient::search($code_add)->whereIn('id', $user_patient)->paginate();
        $result = [
            'number_request' => $number_request,
            'data' => new PatientCollection($data)
        ];
        return $this->successResponseMessage($result, 200, "Get home success");
    }

    public function nureInterestAction(Request $request)
    {
        $is_interest = $request->is_interest;
        $patient_id = $request->patient_id;

        $message = "";
        if ($is_interest == Config::get('constants.interest.interested')) {
            $check = NurseInterest::where('user_patient', $patient_id)->where('user_nurse', Auth::id())->delete();
            $messeage = "Un interested success";
        } else {
            $check = NurseInterest::firstOrCreate(['user_patient' => $patient_id, 'user_nurse' => Auth::id()]);
            $messeage = "interested success";
        }
        return $this->successResponseMessage(new \stdClass(), 200, $messeage);
    }

    /**
     * Function get detail nurse
     */
    public function detail(Request $request)
    {
        $nurseId = $request->id;
        $nurse = NurseProfile::find($nurseId);
        return $this->successResponseMessage(new NurseProfileDetailResource($nurse), 200, 'Get detail nurse success');
    }

    /*
     * Search nurse
     */
    public function searchNurse(Request $request)
    {

        $name = $request->name;
        $user_name = $request->user_name;
        $gender = $request->gender;
        $max_birthday = date("Y") - $request->min_age;
        if (isset($request->max_age) && $request->max_age > 0) {
            $min_birthay = date("Y") - $request->max_age;
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $start_time = $request->start_time;
        $end_time = $request->end_time;
        $adddress = $request->address;
        $is_certificate = $request->is_certificate;
        $query = NurseProfile::join('users', 'profile_nurse.user_login', 'users.id')
            ->select('profile_nurse.*', 'users.name', 'users.user_name', 'users.birthday', 'users.gender');
        if (isset($request->district_code)) {
            $query = $query->where('profile_nurse.code_add', $request->district_code);
            if(sizeof($query->get())== 0){
                $query = NurseProfile::join('users', 'profile_nurse.user_login', 'users.id')
                    ->select('profile_nurse.*', 'users.name', 'users.user_name', 'users.birthday', 'users.gender');
            }
        }
        if (isset($request->start_time)) {
            $query = $query->where('profile_nurse.start_time', '<=', $start_time);
        }
        if (isset($request->end_time)) {
            $query = $query->where('profile_nurse.end_time', '>=', $end_time);
        }
        if (isset($request->city_code)) {
            $query = $query->where('profile_nurse.code_add', 'like', $request->city_code . '%');
        }

        if (isset($request->address)) {
            $query = $query->whereIn('profile_nurse.address', json_decode($adddress));
        }

        if (isset($request->start_date)) {
            $query = $query->where('profile_nurse.start_date', '<=', $start_date);
        }
        if (isset($request->end_date)) {
            $query = $query->where('profile_nurse.end_date', '>=', $end_date);
        }

        if (isset($request->name)) {
            $query = $query->where('users.name', 'like', $this->fullText($name));
        }
        if (isset($request->user_name)) {
            $query = $query->where('users.user_name', 'like', $this->fullText($user_name));
        }
        if (isset($request->is_certificate)) {
            $query = $query->where('profile_nurse.is_certificate', $is_certificate);
        }
        if (isset($request->max_age)) {
            $query = $query->where('users.birthday', '>=', $min_birthay);
        }
        if (isset($request->min_age)) {
            $query = $query->where('users.birthday', '<=', $max_birthday);
        }
        if (isset($request->gender)) {
            $query = $query->where('users.gender', $gender);
        }
        $collection = $query->orderBy('profile_nurse.rate', 'DESC')->orderBy('profile_nurse.created_at', 'DESC')->paginate();
        return response()->json([
            'status' => 200,
            'action' => 'Search nurse success',
            'data' => new NurseHomeCollection($collection)

        ]);

    }

}
