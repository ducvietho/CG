<?php


namespace App\Http\Controllers;


use App\Http\Resources\NurseHomeCollection;
use App\Http\Resources\PatientCollection;
use App\Http\Resources\PatientListCollection;
use App\Http\Resources\PatientResource;
use App\Models\NurseProfile;
use App\Models\Patient;
use App\Models\PatientInterest;
use App\Traits\ApiResponser;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class PatientController extends Controller
{
    use ApiResponser;

    public function create(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'relationship' => 'required',
            'gender' => 'required',
            'birthday' => 'required',
            'code_add' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'end_time' => 'required',
            'start_time' => 'required',
            'address' => 'required',
            'is_certificate' => 'required|min:0|max:1',
        ]);
        $patient = Patient::createPatient($request->all());
        $user = User::find(Auth::id());
        $user->is_register = 1;
        $user->save();
        return $this->successResponseMessage(new PatientResource($patient), 200, 'Create patient success');
    }

    public function update(Request $request)
    {

    }

    public function delete(Request $request)
    {
        $patient = Patient::find($request->id);
        $patient->delete();
        return $this->successResponseMessage(new \stdClass(), 200, 'Delete patient success');
    }

    public function getList(Request $request)
    {
        $patients = Patient::where('user_login', Auth::id())->paginate();
        return $this->successResponseMessage(new PatientListCollection($patients), 200, 'Delete patient success');
    }

    public function homePatient(Request $request)
    {
        $listNurse = NurseProfile::orderBy('rate', 'DESC')->orderBy('created_at', 'DESC')->paginate();
        return $this->successResponseMessage(new NurseHomeCollection($listNurse), 200, 'Patient home success');
    }

    public function interest(Request $request)
    {
        $nurseId = PatientInterest::where('user_login', Auth::id())->pluck('user_nurse')->toArray();
        $listNurse = NurseProfile::whereIn('id', $nurseId)->orderBy('rate', 'DESC')->orderBy('created_at', 'DESC')->paginate();
        return $this->successResponseMessage(new NurseHomeCollection($listNurse), 200, 'Get  nurse interest success');
    }

    public function interestAction(Request $request)
    {
        $id_nurse = $request->id;
        $is_interest = isset($request->is_interest) ? $request->is_interest : 0;
        if ($is_interest == Config::get('contants.interest.interested')) {
            PatientInterest::where([
                'user_login' => Auth::id(),
                'user_nurse' => $id_nurse
            ])->delete();
            return $this->successResponseMessage(new \stdClass(), 200, 'Skip Interested nurse success');
        } else {
            PatientInterest::create([
                'user_login' => Auth::id(),
                'user_nurse' => $id_nurse
            ]);
            return $this->successResponseMessage(new \stdClass(), 200, 'Interest nurse success');
        }

    }
    /**
     * Function get detail patient
     */
    public function detail(Request $request){
        $patient = Patient::find($request->id);
        return $this->successResponseMessage(new PatientResource($patient),200,'Get patient detail success');

    }
}