<?php

namespace App\Models;

use App\Traits\FullTextSearch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Patient extends Model
{
    use FullTextSearch;

    protected $table = 'patients';
    protected $fillable = ['relationship', 'user_login', 'name', 'gender', 'birthday', 'code_add', 'start_date', 'end_date', 'start_time', 'end_time', 'address', 'note', 'is_certificate','end_time_1','start_time_1','avatar'];

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
            'end_time_1' => isset($data['end_time_1'])? $data['end_time_1'] : 0,
            'address' => $data['address'],
            'is_certificate' => $data['is_certificate'],
            'note' => isset($data['note']) ? $data['note'] : '',
            'avatar'=>($data['avatar'] == "")?env('AVATAR_DEFAULT'):$data['avatar']
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
    public function scopeGender($query, $request){
        if(isset($request->gender)){
            $gender = json_decode($request->gender);
            $count = count($gender);
            if($count ==1){
                return $query->where('gender', $gender[0]);
            }
        }    
    }
    /**
     * Searching by date time
     */
    public function scopeDate($query, $request){
        if(isset($request->start_date) && isset($request->end_date)){
            if($request->start_date !=0 && $request->end_date){
                return $query->whereBetween('start_date',[$request->start_date, $request->end_date])->whereBetween('end_date',[$request->start_date, $request->end_date]);
            }
        }     
    }
    /**
     * Searching by time
     */
    public function scopeTime($query, $request){
        if(isset($request->start_time) && isset($request->end_time)){
            $start_time= $request->start_time;
            $end_time = $request->end_time;
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
    }
    /**
     * Searching by age
     */
    public function scopeAge($query, $request){
        if(isset($request->age)){
            $age = json_decode($request->age);
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
    }
    /**
     * Searching by location
     */
    public function scopeLocation($query, $request){
        if(isset($request->city_code) && isset($request->district_code)){
            $district_code = $request->district_code;
            $city_code = $request->city_code;
            if(($district_code !=0 || $district_code !="") && $city_code!=""){
                return $query->where('code_add',$district_code)->orWhere('code_add','like',$city_code.'%')
                            ->orderByRaw("(abs(code_add - $district_code)) asc");
            }
            if($city_code !="" && $city_code==""){
                return $query->where('code_add','like',$city_code.'%');
            }
        }
    }
    /** 
     * Searching by address care
     */
    public function scopeAddress($query, $request){
        if(isset($request->address)){
            $address = json_decode($request->address);
            $count = sizeof($address);
            if($count >0){
                return $query->whereIn('address',$address);
            }
        }
        
    }
    /**
     * Searching by certificate
     */
    public function scopeCertificate($query, $request){
        if(isset($request->is_certificate)){
            $is_certificate = json_decode($request->is_certificate);
            $count = count($is_certificate);
            if($count ==1){
                return $query->where('is_certificate', $is_certificate[0]);
            }
        }   
    }
}
