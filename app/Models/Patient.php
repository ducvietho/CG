<?php

namespace App\Models;

use App\Traits\FullTextSearch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Patient extends Model
{
    use FullTextSearch;

    protected $table = 'patients';
    protected $fillable = ['relationship', 'user_login', 'name', 'gender', 'birthday', 'code_add', 'start_date', 'end_date', 'start_time', 'end_time', 'address', 'note', 'is_certificate','end_time_1','start_time_1'];

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
            'end_time_1' => $data['end_time_1'],
            'address' => $data['address'],
            'is_certificate' => $data['is_certificate'],
            'note' => isset($data['note']) ? $data['note'] : ''

        ]);
        return $patient;
    }

    public static function updatePatient($id, $name, $relationship, $gender, $birthay, $code_add, $start_date, $end_date, $start_time, $end_time, $address, $is_certificate, $note)
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
        $patient ->is_certificate = $is_certificate;
        $patient->note = $note;
        $patient->save();
        return $patient;
    }

    /**
     * Relationship table
     */

    /**
     * End relationship
     */
    public function scopeGender($query, $gender){
        $gender = json_decode($gender);
        $count = count($gender);
        if($count ==1){
            return $query->where('gender', $gender[0]);
        }
    }
    /**
     * Searching by date time
     */
    public function scopeDate($query, $start_date, $end_date){
        if($start_date !=0 && $end_date !=0){
            return $query->whereBetween('start_date',[$start_date, $end_date])->whereBetween('end_date',[$start_date, $end_date]);
        }
    }
    /**
     * Searching by time
     */
    public function scopeTime($query, $start_time, $end_time){

        if($start_time >=0 && $end_time >=0){
            if( $start_time > $end_time ){
                $end_time_1 = $end_time;
                $query = $query->where('start_time','<=',$start_time)->where('end_time_1','>=',$end_time_1);

            }else{
                $query = $query->where(function ($query) use ($start_time,$end_time){
                    $query->where(function ($query) use ($start_time,$end_time){
                        $query->where('start_time','<=',$start_time)->where('end_time','>=',$end_time);
                    })->orWhere(function ($query) use ($start_time,$end_time){
                        $query->where('start_time_1','<=',$start_time)->where('end_time_1','>=',$end_time);
                    });
                });
            }
            return $query;
        }
    }
    /**
     * Searching by age
     */
    public function scopeAge($query, $age){
        $age = json_decode($age);
        $count = sizeof($age);
        if($count ==1){
            $birthday =  date("Y") - $age[0];
            return $query->whereRaw("birthday >=", $birthday);
        }
        if($count >1){
            $start_age = date("Y") - $age[0];
            $end_age = date("Y") - $age[$count -1];
            $age_range = [$start_age, $end_age];
            return $query->whereBetween('birthday',$age_range);
        }
    }
    /**
     * Searching by location
     */
    public function scopeLocation($query, $city_code, $district_code){
        if($district_code !=0){
            return $query->where('code_add',$district_code)->orWhere('code_add','like',$city_code.'%')
                        ->orderByRaw("(abs(code_add - $district_code)) asc");
        }else{
            return $query->where('code_add','like',$city_code.'%');
        }
    }
    /** 
     * Searching by address care
     */
    public function scopeAddress($query, $address){
        $address = json_decode($address);
        $count = sizeof($address);
        if($count >0){
            return $query->whereIn('address',$address);
        }
    }
    /**
     * Searching by certificate
     */
    public function scopeCertificate($query, $is_certificate){
        $is_certificate = json_decode($is_certificate);
        $count = count($is_certificate);
        if($count ==1){
            return $query->where('is_certificate', $is_certificate[0]);
        }
    }
}
