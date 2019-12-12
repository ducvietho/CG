<?php

namespace App\Models;

use App\Traits\FullTextSearch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Patient extends Model
{
    use FullTextSearch;

    protected $table = 'patients';
    protected $fillable = ['relationship', 'user_login', 'name','gender','birthday','city_code','start_date','end_date','start_time','end_time','address','note','district_code'];

    protected $searchable = [
        'city_code'
    ];  

    public static function createPatient($data){
        $patient = Patient::create([
            'name' => $data['name'],
            'user_login' => Auth::id(),
            'relationship' => $data['relationship'],
            'gender' => $data['gender'],
            'birthday' => $data['birthday'],
            'city_code' => $data['city_code'],
            'district_code' => $data['district_code'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'address' => $data['address'],
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

    public function city(){
        return $this->belongsTo(City::class,'city_code','code');
    }

    public function district(){
        return $this->belongsTo(District::class,'district_code','code');
    }

}
