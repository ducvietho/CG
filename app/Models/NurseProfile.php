<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class NurseProfile extends Model
{

    protected $table = 'profile_nurse';
    protected $fillable = ['nationality', 'start_date', 'end_date','start_time','end_time','address','is_certificate','description','start_time','rate','user_login','code_add','end_time_1','start_time_1'];

    /**
     * Relationship table
     */

    /**
     * End relationship
     */
    public function user(){
        return $this->belongsTo(User::class,'user_login','id');
    }
}
