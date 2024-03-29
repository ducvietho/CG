<?php

namespace App\Models;

use App\Traits\FullTextSearch;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Patient extends Model
{
    use FullTextSearch;

    protected $table = 'patients';
    protected $fillable = ['relationship', 'user_login', 'name', 'gender', 'birthday', 'code_add', 'start_date', 'end_date', 'start_time', 'end_time', 'address', 'note', 'is_certificate', 'end_time_1', 'start_time_1', 'avatar', 'nationality', 'salary', 'type_salary'];

    protected $searchable = [
        'name'
    ];

    public static function createPatient($data)
    {
        $patient = Patient::create([
            'name' => $data['name'],
            'user_login' => Auth::id(),
            'relationship' => $data['relationship'],
            'gender' => $data['gender'],
            'birthday' => $data['birthday'],
            'code_add' => $data['code_add'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'end_time_1' => isset($data['end_time_1']) ? $data['end_time_1'] : 0,
            'address' => $data['address'],
            'is_certificate' => $data['is_certificate'],
            'salary' => $data['salary'],
            'type_salary' => $data['type_salary'],
            'nationality' => $data['nationality'],
            'note' => isset($data['note']) ? $data['note'] : '',
            'avatar' => ($data['avatar'] == "") ? env('AVATAR_DEFAULT') : $data['avatar']
        ]);
        return $patient;
    }

    public static function updatePatient($id, $name, $relationship, $gender, $birthay, $code_add, $start_date, $end_date, $start_time, $end_time, $address, $is_certificate, $note, $avatar, $nationality, $end_time_1, $salary, $type_salary)
    {
        $patient = Patient::findOrFail($id);
        $patient->name = $name;
        $patient->relationship = $relationship;
        $patient->gender = $gender;
        $patient->birthday = $birthay;
        $patient->code_add = $code_add;
        $patient->start_date = $start_date;
        $patient->end_date = $end_date;
        $patient->start_time = $start_time;
        $patient->end_time = $end_time;
        $patient->address = $address;
        $patient->is_certificate = $is_certificate;
        $patient->note = $note;
        $patient->avatar = $avatar;
        $patient->nationality = $nationality;
        $patient->end_time_1 = $end_time_1;
        $patient->salary = $salary;
        $patient->type_salary = $type_salary;
        $patient->save();
        return $patient;
    }

    /**
     * Relationship table
     */
    public function getLikes()
    {
        return $this->hasMany(NurseInterest::class, 'user_patient', 'id');
    }

    /**
     * End relationship
     */
    public function scopeGender($query, $request)
    {
        if (isset($request->gender)) {
            $gender = json_decode($request->gender);
            $count = count($gender);
            if ($count == 1) {
                return $query->where('gender', $gender[0]);
            }
        }
    }

    /**
     * Searching by date time
     */
    public function scopeDate($query, $request)
    {
        if (isset($request->start_date)) {
            if ($request->start_date > 0) {
                $query = $query->where('start_date', '>=', $request->start_date);
            }
        }
        if (isset($request->end_date) && $request->end_date > 0) {
            $query = $query->where('end_date', '<=', $request->end_date);
        }
        return $query;
    }

    /**
     * Searching by time
     */
    public function scopeTime($query, $request)
    {
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
                return $query;
            }
        }
    }

    /**
     * Searching by age
     */
    public function scopeAge($query, $request)
    {
        if (isset($request->age)) {
            $age = json_decode($request->age);
            $count = sizeof($age);
            if ($count == 1) {
                $birthday = strtotime(date("Y") - $age[0] . '-1-1') / (24 * 60 * 60);
                return $query->whereRaw("birthday >=" . $birthday);
            }
            if ($count > 1) {
                $age = array_reverse($age);
                $start_age = strtotime(date("Y") - $age[0] . '-1-1') / (24 * 60 * 60);
                $end_age = strtotime(date("Y") - $age[$count - 1] . '-12-31') / (24 * 60 * 60);
                $age_range = [$start_age, $end_age];
                return $query->whereBetween('birthday', $age_range);
            }
        }
    }

    /**
     * Searching by location
     */
    public function scopeLocation($query, $request)
    {
        if (isset($request->city_code) && isset($request->district_code)) {
            $district_code = $request->district_code;
            $city_code = $request->city_code;
            if (($district_code != 0 || $district_code != "") && $city_code != "") {
                return $query->where('code_add', $district_code)->orwhereRaw("SUBSTRING(code_add,1,2) = ?", $city_code)
                    ->orderByRaw("(abs(code_add - $district_code)) asc");
            }
            if ($city_code != "" && $district_code == "") {
                return $query->whereRaw("SUBSTRING(code_add,1,2) = ?", $city_code);
            }
        }
        if (isset($request->city_code) && !isset($request->district_code)) {
            $city_code = $request->city_code;
            if ($city_code != null) {
                return $query->whereRaw("SUBSTRING(code_add,1,2) = ?", $city_code);
            }
        }
    }

    /**
     * Searching by address care
     */
    public function scopeAddress($query, $request)
    {
        if (isset($request->address)) {
            $arrayAdd = json_decode($request->address);
            $count = sizeof($arrayAdd);
            if ($count > 0) {
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
                return $query;
            }
        }

    }

    /**
     * Searching by certificate
     */
    public function scopeCertificate($query, $request)
    {
        if (isset($request->is_certificate)) {
            $is_certificate = json_decode($request->is_certificate);
            $count = count($is_certificate);
            if ($count == 1) {
                return $query->where('is_certificate', $is_certificate[0]);
            }
        }
    }

    /**
     * Searching by nationality
     */
    public function scopeNationality($query, $request)
    {
        if (isset($request->nationality)) {
            $nationality = json_decode($request->nationality);
            if (sizeof($nationality) > 0) {
                $query = $query->whereIn('nationality', $nationality);
            }
        }
        return $query;
    }
}
