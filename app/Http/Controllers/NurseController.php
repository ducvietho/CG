<?php

namespace App\Http\Controllers;

use App\Traits\ProcessTextSearch;
use Auth;
use App\MyConst;
use App\Models\Care;
use App\Models\Patient;
use App\Traits\MediaClass;
use App\Models\NurseProfile;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use App\Models\NurseInterest;
use App\Traits\FullTextSearch;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\CareCollection;
use App\Http\Resources\PatientCollection;
use App\Http\Resources\NurseHomeCollection;
use Elasticquent\ElasticquentResultCollection;
use App\Http\Resources\NurseProfileDetailResource;

class NurseController extends Controller
{
    use MediaClass;
    use ApiResponser;
    use FullTextSearch;
    use ProcessTextSearch;

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
        $code_add = NurseProfile::select('code_add')->where('user_login', Auth::id())->first();
        $date = date('Y-m-d');
        $currentDate = strtotime($date) / (24 * 60 * 60);
        $code_add = json_decode($code_add->code_add);
        $data = DB::table('patients')
            ->where('end_date', '>=', $currentDate)
            ->orderByRaw("(abs(code_add - $code_add[0])) asc")
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
            'address' => 'required',
            'is_certificate' => 'required',
            'description' => 'string',
            'code_add' => 'required',
            'nationality' => 'required'
        ]);
        $request->request->add(['user_login' => Auth::id()]);
        //Update status register user
        $user = Auth::user();
        $user->is_register = 1;
        $user->save();
        //end
        if ($request->start_time > $request->end_time) {
            $end_time = $request->end_time;
            $request->request->set('end_time', 1440);
            $request->request->set('end_time_1', $end_time);
        }
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

        $code_add = NurseProfile::select('code_add')->where('user_login', Auth::id())->first();

        $data = DB::table('patients')->whereNotIn('id', $user_id)
            ->orderByRaw("(abs(code_add - $code_add->code_add)) asc")
            ->paginate();
        return $this->successResponseMessage(new PatientCollection($data), 200, "Get tab suggest success");
    }

    /**
     * Tab interest
     */
    public function interest(Request $request)
    {
        $user_id = NurseInterest::where('user_nurse', Auth::id())->pluck('user_patient');

        $data = Patient::whereIn('id', $user_id)->paginate();
        return $this->successResponseMessage(new PatientCollection($data), 200, "Get interest success");
    }

    public function manager(Request $request)
    {

        $status = $request->status;
        $user_patient = Care::select('id', 'user_patient', 'status', 'user_nurse', 'user_login')
            ->where('status', $status)
            ->where('user_nurse', Auth::id())
            ->orderBy('created_at', 'DESC')
            ->paginate();

        return $this->successResponseMessage(new CareCollection($user_patient), 200, "Get home success");
    }

    public function nureInterestAction(Request $request)
    {
        $is_interest = $request->is_interest;
        $patient_id = $request->patient_id;

        $message = "";
        if ($is_interest == MyConst::INTERESTED) {
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
        $nurse = NurseProfile::where('user_login', $nurseId)->firstorFail();
        return $this->successResponseMessage(new NurseProfileDetailResource($nurse), 200, 'Get detail nurse success');
    }

    /**
     * Search patient
     */
    public function searchPatient(Request $request)
    {
        //Validate input searching
        $this->validate($request, [
            'name' => 'string',
        ]);
        $patient = Patient::query();
        if (isset($request->name)) {
            if ($request->name != "") {
                $patient = $patient->search($request->name);
            }
        }
        $patient->gender($request)
            ->date($request)
            ->time($request)
            ->age($request)
            ->address($request)
            ->certificate($request)
            ->location($request);
        $patient = $patient->paginate();
        return $this->successResponseMessage(new PatientCollection($patient), 200, 'Searching detail patient');
    }

    /*
     * Search nurse
     */
    public function searchNurse(Request $request)
    {

        $name = $request->name;
        $user_name = $request->user_name;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $adddress = $request->address;
        $is_certificate = isset($request->is_certificate) ? json_decode($request->is_certificate) : [];
        $query = NurseProfile::join('users', 'profile_nurse.user_login', 'users.id')
            ->select('profile_nurse.*', 'users.name', 'users.user_name', 'users.birthday', 'users.gender');
        if (isset($request->district_code) && $request->district_code != null) {
            $query = $query->where('profile_nurse.code_add', 'like', '%' . $request->district_code . '%');
            if (sizeof($query->get()) == 0) {
                $query = NurseProfile::join('users', 'profile_nurse.user_login', 'users.id')
                    ->select('profile_nurse.*', 'users.name', 'users.user_name', 'users.birthday', 'users.gender');
            }
        }
        if (isset($request->start_time) && isset($request->end_time)) {
            $start_time = $request->start_time;
            $end_time = $request->end_time;
            if ($start_time > 0 || $end_time > 0) {
                if ($start_time > $end_time) {
                    $end_time_1 = $end_time;
                    $query = $query->where('start_time', '<=', $start_time)->where('end_time_1', '>=', $end_time_1);
                } else {
                    $query = $query->where(function ($query) use ($start_time, $end_time) {
                        $query->where(function ($query) use ($start_time, $end_time) {
                            $query->where('start_time', '<=', $start_time)->where('end_time', '>=', $end_time);
                        })->orWhere(function ($query) use ($start_time, $end_time) {
                            $query->where('start_time_1', '<=', $start_time)->where('end_time_1', '>=', $end_time);
                        });
                    });
                }
            }


        }

        if (isset($request->city_code) && $request->city_code != null) {
            $query = $query->where('profile_nurse.code_add', 'like', '%' . $request->city_code . '%');
        }

        if (isset($request->address) && sizeof(json_decode($request->address)) > 0) {
            $arrayAdd = json_decode($request->address);
            $add1 = $arrayAdd[0];
            $add2 = 0;
            $add3 = 0;
            if (sizeof($arrayAdd) >= 2) {
                $add2 = $arrayAdd[1];
            }
            if (sizeof($arrayAdd) >= 3) {
                $add3 = $arrayAdd[2];
            }
            $query = $query->where(function ($query) use ($add1, $add2, $add3) {
                $query->where('address', 'like', '%' . $add1 . '%')
                    ->orWhere('address', 'like', '%' . $add2 . '%')
                    ->orWhere('address', 'like', '%' . $add3 . '%');
            });
        }

        if (isset($request->start_date) && $request->start_date > 0) {
            $query = $query->where('profile_nurse.start_date', '>=', $start_date);
        }
        if (isset($request->end_date) && $request->end_date > 0) {
            $query = $query->where('profile_nurse.end_date', '<=', $end_date);
        }

        if (isset($request->name)) {
            $query = $query->where('users.name', 'like', $this->fullText($name));
        }
        if (isset($request->user_name)) {
            $query = $query->where('users.user_name', 'like', $this->fullText($user_name));
        }
        if (isset($request->is_certificate) && sizeof($is_certificate) > 0) {
            $query = $query->whereIn('profile_nurse.is_certificate', $is_certificate);
        }
        if (isset($request->age)) {
            $age = json_decode($request->age);
            if (sizeof($age) > 0) {
                if (sizeof($age) == 1) {
                    $query = $query->where("users.birthday", '>=', strtotime(date("Y") - $age[0] . '-1-1') / (24 * 60 * 60));
                } else {
                    $age_range = [strtotime(date("Y") - end($age) . '-1-1') / (24 * 60 * 60), strtotime(date("Y") - $age[0] . '-12-31') / (24 * 60 * 60)];
                    $query = $query->whereBetween("users.birthday", $age_range);

                }
            }
        }

        if (isset($request->gender) && sizeof(json_decode($request->gender)) > 0) {
            $query = $query->whereIn('users.gender', json_decode($request->gender));
        }
        if (isset($request->nationality) && sizeof(json_decode($request->nationality)) > 0) {
            $query = $query->whereIn('profile_nurse.nationality', json_decode($request->nationality));
        }
        if (isset($request->salary) && $request->salary > 0) {
            $query = $query->where('profile_nurse.salary', '<=', $request->salary);
        }
        if (isset($request->type_salary) && $request->type_salary > 0) {
            $query = $query->where('profile_nurse.type_salary', $request->type_salary);
        }
        $collection = $query->orderBy('profile_nurse.rate', 'DESC')->orderBy('profile_nurse.created_at', 'DESC')->paginate();
        return response()->json([
            'status' => 200,
            'action' => 'Search nurse success',
            'data' => new NurseHomeCollection($collection)

        ]);

    }

    /**
     * Update profile nurse
     */
    public function updateProfile(Request $request)
    {
        $profile = NurseProfile::where('user_login', Auth::id())->first();
        if (isset($request->avatar)) {
            if ($request->avatar != null) {
                $avatar = $this->upload(MyConst::AVATAR, $request->avatar, Auth::id());
                $request->request->set('avatar', $avatar);
            }
        }
        if (isset($request->start_time) && isset($request->end_time)) {
            if ($request->start_time > $request->end_time) {
                $end_time = $request->end_time;
                $request->request->set('end_time', 1440);
                $request->request->set('end_time_1', $end_time);
            }
        }
        $profile->update($request->all());
        return $this->successResponseMessage(new NurseProfileDetailResource($profile), 200, "Update success");
    }
}
