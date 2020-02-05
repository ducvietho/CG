<?php


namespace App\Http\Controllers\CMS;


use App\Http\Controllers\Controller;
use App\Http\Resources\CMS\PatientCMSCollection;
use App\Http\Resources\CMS\PatientHistoryCareCollection;
use App\Models\Care;
use App\Models\Patient;
use App\MyConst;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;


class PatientController extends Controller
{
  use ApiResponser;
  public function getPatients(Request $request){
      $patient = Patient::query();
      if (isset($request->name)) {
          if ($request->name != "") {
              $patient = $patient->search($request->name);
          }
      }
      $patient->date($request)
          ->location($request);
      $patient = $patient ->withCount(['getLikes'])
            ->orderBy('created_at','desc')
            ->orderBy('get_likes_count', 'desc')
            ->paginate(MyConst::PAGINATE);
      return $this->successResponseMessage(new PatientCMSCollection($patient),200,'Get list patient success');
  }

  public function getHistoryRequest(Request $request){
      $idPatient = $request->id_patient;
      $status = [1,3];
      $cares = Care::where('user_patient',$idPatient)->whereIn('status',$status)->orderBy('created_at','DESC')->paginate(MyConst::PAGINATE);
      return $this->successResponseMessage(new PatientHistoryCareCollection($cares),200,'Get patient history success');

  }

}