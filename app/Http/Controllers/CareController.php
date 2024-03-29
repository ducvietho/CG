<?php

namespace App\Http\Controllers;

use App\Http\Resources\RefuseRequestResource;
use App\Jobs\RefuseRequestJob;
use Auth;
use DateTime;
use App\MyConst;
use App\Models\Care;
use App\Models\Patient;
use App\Jobs\RequestJob;
use App\Jobs\ActionRequestJob;
use App\Jobs\UpdateRateJob;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use App\Jobs\DeleteRequestJob;
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
    public function requestCare(Request $request)
    {
        $this->validate($request, [
            'user_patient' => 'required|min:1',
            'start_date' => 'required',
            'end_date' => 'required',
            'start_time' => 'required',
            'end_time' => 'required'
        ]);
        $type_user = Auth::user()->type;
        // Nurse sending request care patient
        //Gan user_request = user_patient
        $data = [
            'user_patient' => $request->user_patient,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ];
        $care = Care::where($data)->where('status', 1)->first();
        if ($care != null) {
            return $this->successResponseMessage(new \stdClass(), 420, "Time has coincided");
        } else {
            $data_add = [
                'user_nurse' => Auth::id(),
                'user_login' => Auth::id(),
                'type' => MyConst::NURSER_REQUEST,
                'status' => MyConst::REQUESTING,
                'rate' => 0
            ];
            $data_merge = array_merge($data_add, $data);
            $care = Care::where($data_merge)->first();
            if ($care != null) {
                return $this->successResponseMessage(new \stdClass(), 420, "Time has coincided");
            } else {
                $care = Care::firstorCreate(array_merge($data_add, $data));
                //Sending noti nurse request care
                dispatch(new RequestJob(Auth::id(), 0, $request->user_patient, MyConst::NOTI_NURSE_REQUEST, $care->id));
                return $this->successResponseMessage(new CareDetailResource($care, $type_user), 200, "Request success");
            }
        }
    }

    /**
     * Nurse accept request
     */
    public function nurseAcept(Request $request)
    {
        $this->validate($request, [
            'id_request' => 'required'
        ]);
        $type_user = Auth::user()->type;
        if ($type_user != MyConst::NURSE) {
            return $this->successResponseMessage(new \stdClass(), 418, "Permision denied");
        }

        $care = Care::where('id', $request->id_request)
            ->where('type', MyConst::PATIENT_REQUEST)
            ->firstorFail();
        $care->status = MyConst::ACCEPTED;
        $care->save();
        dispatch(new ActionRequestJob(Auth::id(), 0, MyConst::NOTI_NURSE_ACCEPT, $care->id, $care->user_patient));
        return $this->successResponseMessage(new CareDetailResource($care, $type_user), 200, "Request success");
    }

    /**
     * Patient request care
     */
    public function patientRequest(Request $request)
    {
        $this->validate($request, [
            'user_nurse' => 'required|min:1',
            'user_patient' => 'required|min:1',
            'start_date' => 'required',
            'end_date' => 'required',
            'start_time' => 'required',
            'end_time' => 'required'
        ]);
        $type_user = Auth::user()->type;
        // Nurse sending request care patient
        //Gan user_request = user_patient
        $data = [
            'user_nurse' => $request->user_nurse,
            'user_patient' => $request->user_patient,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time
        ];
        $care = Care::where($data)->where('status', 1)->first();
        if ($care != null) {
            return $this->successResponseMessage(new \stdClass(), 420, "Time has coincided");
        } else {
            $data_add = [
                'user_login' => Auth::id(),
                'type' => MyConst::PATIENT_REQUEST,
                'status' => MyConst::REQUESTING
            ];
            $data_merge = array_merge($data_add, $data);
            $care = Care::where($data_merge)->first();
            if ($care != null) {
                return $this->successResponseMessage(new \stdClass(), 420, "Time has coincided");
            } else {
                $care = Care::firstorCreate(array_merge($data_add, $data));
                //Sending noti nurse request care
                dispatch(new RequestJob(Auth::id(), $request->user_nurse, $request->user_patient, MyConst::NOTI_PATIENT_REQUEST, $care->id));
                return $this->successResponseMessage(new CareDetailResource($care, $type_user), 200, "Request success");
            }
        }
    }

    /**
     * Patient accepted request
     */
    public function patientAcept(Request $request)
    {
        $this->validate($request, [
            'id_request' => 'required'
        ]);
        $type_user = Auth::user()->type;
        if ($type_user != MyConst::PATIENT) {
            return $this->successResponseMessage(new \stdClass(), 418, "Permision denied");
        }

        $care = Care::where('id', $request->id_request)
            ->where('type', MyConst::NURSER_REQUEST)
            ->firstorFail();
        $care->status = MyConst::ACCEPTED;
        $care->save();
        dispatch(new ActionRequestJob(Auth::id(), $care->user_nurse, MyConst::NOTI_PATIENT_ACCEPT, $care->id, $care->user_patient));
        return $this->successResponseMessage(new CareDetailResource($care, $type_user), 200, "Request success");
    }

    /*
     * Detail Care
     */
    public function detail(Request $request)
    {
        $idCare = $request->id_request;
        $care = Care::findOrFail($idCare);
        $type_user = Auth::user()->type;
        return $this->successResponseMessage(new CareDetailResource($care, $type_user), 200, "Detail request success");
    }

    /**
     * Nurse completed
     */
    public function nurseComplete(Request $request)
    {
        $this->validate($request, [
            'id_request' => 'required'
        ]);
        $type_user = Auth::user()->type;
        if ($type_user != MyConst::NURSE) {
            return $this->successResponseMessage(new \stdClass(), 418, "Permision denied");
        }
        $care = Care::where('id', $request->id_request)
            ->firstorFail();
        if (Auth::id() != $care->user_nurse) {
            return $this->successResponseMessage(new \stdClass(), 418, "Permision denied");
        }
        $care->status = MyConst::COMPLETED;
        $care->save();
        dispatch(new ActionRequestJob(Auth::id(), 0, MyConst::NOTI_COMPLETED, $care->id, $care->user_patient));
        return $this->successResponseMessage(new CareDetailResource($care, $type_user), 200, "Request completed");
    }

    /**
     * Patient rating
     */
    public function patientRating(Request $request)
    {
        $this->validate($request, [
            'id_request' => 'required',
            'rate' => 'required'
        ]);
        $type_user = Auth::user()->type;
        if ($type_user != MyConst::PATIENT) {
            return $this->successResponseMessage(new \stdClass(), 418, "Permision denied");
        }
        $care = Care::where('id', $request->id_request)
            ->firstorFail();
        if ($care->status != MyConst::COMPLETED) {
            return $this->successResponseMessage(new \stdClass(), 418, "Permision denied");
        }
        $patient = Patient::select('user_login')->findOrFail($care->user_patient);
        if (Auth::id() != $patient->user_login) {
            return $this->successResponseMessage(new \stdClass(), 418, "Permision denied");
        }
        if ($care->rate > 0) {
            return $this->successResponseMessage(new \stdClass(), 419, "You are only evaluated once");
        }
        $care->rate = $request->rate;
        $care->save();
        dispatch(new UpdateRateJob($care->user_nurse));
        return $this->successResponseMessage(new CareDetailResource($care, $type_user), 200, "Rate success");
    }

    /*
     * Delete request after 30 days
     */
    public function delete(Request $request)
    {
        $now = date('Y-m-d');
        $date = new DateTime($now);
        $days = 30;
        $requestID = Care::where('status', 2)->where('created_at', '<=', date_sub($date, date_interval_create_from_date_string($days . ' days')))->pluck('id')->toArray();
        dispatch(new DeleteRequestJob($requestID));
        Care::whereIn('id', $requestID)->delete();
        return $this->successResponseMessage(new \stdClass(), 200, "Delete request success");
    }


    /*
     * Nurse refuse
     */

    public function nurseRefuse(Request $request)
    {
        $idRequest = $request->id_request;
        $reason = $request->reason;
        $message = isset($request->message) ? $request->message : '';
        $care = Care::findOrFail($idRequest);
        $type_user = Auth::user()->type;
        if ($type_user != MyConst::NURSE) {
            return $this->successResponseMessage(new \stdClass(), 418, "Permission denied");
        } else {
            $care->reason = $reason;
            $care->message = $message;
            $care->status = MyConst::CANCEL;
            $care->save();
            $this->dispatch(new ActionRequestJob(Auth::id(), 0, MyConst::NOTI_NURSE_CANCEL, $idRequest, $care->user_patient));
            return $this->successResponseMessage(new CareDetailResource($care, $type_user), 200, "Refuse success");
        }


    }

    /*
     * Patient refuse
     */

    public function patientRefuse(Request $request)
    {
        $idRequest = $request->id_request;
        $reason = $request->reason;
        $message = isset($request->message) ? $request->message : '';
        $care = Care::findOrFail($idRequest);
        $type_user = Auth::user()->type;
        if ($type_user != MyConst::PATIENT) {
            return $this->successResponseMessage(new \stdClass(), 418, "Permission denied");
        } else {
            $care->reason = $reason;
            $care->message = $message;
            $care->status = MyConst::CANCEL;
            $care->save();
            $this->dispatch(new ActionRequestJob(Auth::id(), $care->user_nurse, MyConst::NOTI_PATIENT_CANCEL, $idRequest, $care->user_patient));
            return $this->successResponseMessage(new CareDetailResource($care, $type_user), 200, "Refuse success");
        }
    }

    /*
     * Get reason refuse
     */
    public function getReason(Request $request)
    {
        $care = Care::where('id', $request->id_request)->where('status', MyConst::CANCEL)->firstOrFail();
        return $this->successResponseMessage(new RefuseRequestResource($care), 200, 'Get reason refuse success');

    }

}
