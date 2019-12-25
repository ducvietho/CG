<?php


namespace App\Http\Controllers;

use App\Traits\MediaClass;
use Auth;
use App\User;
use App\MyConst;
use App\Models\Care;
use App\Models\Patient;
use App\Models\NurseProfile;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use App\Models\PatientInterest;
use App\Http\Resources\PatientResource;
use App\Http\Resources\PatientCollection;
use App\Http\Resources\NurseCareCollection;
use App\Http\Resources\NurseHomeCollection;
use App\Http\Resources\PatientListCollection;

class PatientController extends Controller
{
    use ApiResponser;
    use MediaClass;

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
            'avatar' => 'string'
        ]);
        if ($request->start_time > $request->end_time) {
            $end_time = $request->end_time;
            $request->request->set('end_time', 1440);
            $request->request->set('end_time_1', $end_time);
        }
        if (isset($request->avatar)) {
            if ($request->avatar != null) {
                $avatar = $this->upload(MyConst::AVATAR, $request->avatar, Auth::id());
                $request->request->set('avatar', $avatar);
            }
        } else {
            $request->request->set('avatar', env('AVATAR_DEFAULT'));
        }

        $patient = Patient::createPatient($request->all());
        $user = User::find(Auth::id());
        $user->is_register = 1;
        $user->save();
        return $this->successResponseMessage(new PatientResource($patient), 200, 'Create patient success');
    }

    public function update(Request $request)
    {
        $patient = Patient::findOrFail($request->id);
        $name = isset($request->name) ? $request->name : $patient->name;
        $relationship = isset($request->relationship) ? $request->relationship : $patient->relationship;
        $gender = isset($request->gender) ? $request->gender : $patient->gender;
        $birthday = isset($request->birthday) ? $request->birthday : $patient->birthday;
        $code_add = isset($request->code_add) ? $request->code_add : $patient->code_add;
        $start_date = isset($request->start_date) ? $request->start_date : $patient->start_date;
        $end_date = isset($request->end_date) ? $request->end_date : $patient->end_date;
        $end_time = isset($request->end_time) ? $request->end_time : $patient->end_time;
        $start_time = isset($request->start_time) ? $request->start_time : $patient->start_time;
        $address = isset($request->address) ? $request->address : $patient->address;
        $is_certificate = isset($request->is_certificate) ? $request->is_certificate : $patient->is_certificate;
        $note = isset($request->note) ? $request->note : $patient->note;
        $nationality = isset($request->nationality) ? $request->nationality : $patient->nationality;
        $avatar = $patient->avatar;
        if (isset($request->avatar)) {
            if ($request->avatar != null) {
                $avatar = $this->upload(MyConst::AVATAR, $request->avatar, Auth::id());
            }
        }
        $patient = Patient::updatePatient($request->id, $name, $relationship, $gender, $birthday, $code_add, $start_date, $end_date, $start_time, $end_time, $address, $is_certificate, $note, $avatar,$nationality);
        return $this->successResponseMessage(new PatientResource($patient), 200, 'Update patient success');

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
        $listNurse = NurseProfile::whereIn('user_login', $nurseId)->orderBy('rate', 'DESC')->orderBy('created_at', 'DESC')->paginate();
        return $this->successResponseMessage(new NurseHomeCollection($listNurse), 200, 'Get  nurse interest success');
    }

    public function interestAction(Request $request)
    {
        $id_nurse = $request->id;
        $is_interest = isset($request->is_interest) ? $request->is_interest : 0;
        if ($is_interest == MyConst::INTERESTED) {
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
    public function detail(Request $request)
    {
        $patient = Patient::find($request->id);
        return $this->successResponseMessage(new PatientResource($patient), 200, 'Get patient detail success');

    }

    public function suggest(Request $request)
    {
        $codeAdd = Auth::user()->district_code;
        if ($codeAdd == null) {
            $patient = Patient::select('code_add')->where('user_login', Auth::id())->first();
            $codeAdd = $patient->code_add;
        }
        $nurseIds = Care::where('user_login', Auth::id())->where('status', '!=', 0)->pluck('user_nurse')->toArray();
        $nurseSuggest = NurseProfile::whereNotIn('id', $nurseIds)->orderByRaw("(abs(code_add - $codeAdd)) asc")->orderBy('rate', 'DESC')->paginate();
        return $this->successResponseMessage(new NurseHomeCollection($nurseSuggest), 200, 'Get nurse suggest success');
    }

    public function manage(Request $request)
    {
        $status = isset($request->status) ? $request->status : 1;
        $patients = Patient::where('user_login', Auth::id())->pluck('id')->toArray();
        $nurseCare = Care::where('status', $status)->whereIn('user_patient', $patients)->paginate();
        return $this->successResponseMessage(new NurseCareCollection($nurseCare), 200, 'Get nurse care success');
    }
}