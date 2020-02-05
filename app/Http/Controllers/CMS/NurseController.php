<?php


namespace App\Http\Controllers\CMS;


use App\Http\Controllers\Controller;
use App\Http\Resources\CMS\NurseCMSCollection;
use App\Models\NurseProfile;
use App\MyConst;
use App\Traits\ProcessTextSearch;
use Illuminate\Http\Request;

class NurseController extends Controller
{
    use ProcessTextSearch;
    public function getNurses(Request $request)
    {
        $query = NurseProfile::join('users', 'profile_nurse.user_login', 'users.id')
            ->select('profile_nurse.*', 'users.name', 'users.user_name', 'users.birthday', 'users.gender');

        if (isset($request->district_code) && $request->district_code != null) {
            $query = $query->where('profile_nurse.code_add','like', '%'.$request->district_code.'%');
            if(sizeof($query->get())== 0){
                $query = NurseProfile::join('users', 'profile_nurse.user_login', 'users.id')
                    ->select('profile_nurse.*', 'users.name', 'users.user_name', 'users.birthday', 'users.gender');
            }
        }
        if (isset($request->city_code) && $request->city_code != null) {
            $query = $query->where('profile_nurse.code_add', 'like', '%'.$request->city_code . '%');
        }
        if (isset($request->name)) {
            if ($request->name != "") {
                $query = $query->where('users.name','like',$this->fullText($request->name));
            }
        }
        if (isset($request->start_date) && $request->start_date > 0) {
            $query = $query->where('profile_nurse.start_date', '>=', $request->start_date);
        }
        if (isset($request->end_date) && $request->end_date > 0) {
            $query = $query->where('profile_nurse.end_date', '<=', $request->end_date);
        }

        $collection = $query->withCount(['getLikes'])
            ->orderBy('profile_nurse.created_at', 'DESC')
            ->orderBy('get_likes_count', 'desc')
            ->orderBy('profile_nurse.rate', 'DESC')
            ->paginate(MyConst::PAGINATE);
        return response()->json([
            'status' => 200,
            'action' => 'Search nurse success',
            'data' => new NurseCMSCollection($collection)

        ]);
    }
}