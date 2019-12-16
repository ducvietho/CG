<?php

namespace App\Models;

use App\Traits\FullTextSearch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Patient extends Model
{
    use FullTextSearch;

    protected $table = 'patients';
    protected $fillable = ['relationship', 'user_login', 'name', 'gender', 'birthday', 'code_add', 'start_date', 'end_date', 'start_time', 'end_time', 'address', 'note', 'is_certificate'];

    protected $searchable = [
        'code_add'
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
    /**Searching by name */
    public function scopeName($query,$name)
    {
        return $query->where('name', 'like', '%'.$name.'%');
    }
    /**
     * Searching by user name
     */
    public function scopeGender($query, $gender){
        return $query->where('gender', $gender);
    }
    /**
     * Searching by date time
     */
    public function scopeDate($query, $start_date, $end_date){
        
    }
}
