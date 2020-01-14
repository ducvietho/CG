<?php


namespace App\Http\Controllers\CMS;


use App\Http\Controllers\Controller;
use App\Http\Resources\CMS\PatientCMSCollection;
use App\Models\Patient;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;


class PatientController extends Controller
{
  use ApiResponser;
  public function getPatients(Request $request){
      $patient = Patient::query();
      $patient->date($request)
          ->location($request);
      $patient = $patient ->withCount(['getLikes'])
            ->orderBy('get_likes_count', 'desc')
            ->paginate();
      return $this->successResponseMessage(new PatientCMSCollection($patient),200,'Get list patient success');
  }

}