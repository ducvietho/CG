<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Care extends Model
{
    protected $table = 'requests';
    protected $fillable = ['user_nurse', 'user_patient', 'user_login','type','status','start_date','end_date','start_time','end_time','rate','reason','message'];

    /**
     * Relationship table
     */

    /**
     * End relationship
     */
    
}
