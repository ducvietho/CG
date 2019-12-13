<?php

namespace App\Models;

use App\Traits\FullTextSearch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Patient extends Model
{
    use FullTextSearch;

    protected $table = 'patients';
    protected $fillable = ['relationship', 'user_login', 'name','gender','birthday','code_add','start_date','end_date','start_time','end_time','address','note','is_certificate'];

    protected $searchable = [
        'code_add'
    ];  

    public static function createPatient($data){
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
            'address' => $data['address'],
            'is_certificate' => $data['is_certificate'],
            'note' => isset($data['note']) ? $data['note'] : ''

        ]);
        return $patient;
    }

    /**
     * Relationship table
     */

    /**
     * End relationship
     */
    function getScoreAttribute()
    {
        $code_add = Auth::user()->district_code;
        $city_code = substr($code_add,0,2);
        if($this->code_add == $code_add){
            return 2;
        }else{
            if(substr($this->code_add,0,2) == $city_code){
                return 1;
            }else{
                return 0;
            }
        }
    }

}
