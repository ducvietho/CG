<?php
namespace App\Http\Controllers;

use Auth;
use App\User;
use App\Models\Patient;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use App\Http\Resources\PatientResource;
use App\Http\Resources\PatientCollection;
use App\Http\Resources\PatientListCollection;

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
            'is_certificate'=>'required|min:0|max:1',
        ]);
        $patient = Patient::createPatient($request->all());
        $user = User::find(Auth::id());
        $user->is_register = 1;
        $user->save();
        return $this->successResponseMessage(new PatientResource($patient),200, 'Create patient success');
    }

    public function update(Request $request)
    {

    }

    public function delete(Request $request)
    {
        $patient = Patient::find($request->id);
        $patient->delete();
        return $this->successResponseMessage(new \stdClass(),200, 'Delete patient success');
    }

    public function getList(Request $request)
    {
        $patients = Patient::where('user_login',Auth::id())->paginate();
        return $this->successResponseMessage(new PatientListCollection($patients),200, 'Delete patient success');
    }


}