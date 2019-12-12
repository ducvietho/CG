<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NurseProfile extends Model
{

    protected $table = 'profile_nurse';
    protected $fillable = ['nationality', 'start_date', 'end_date','start_time','end_time','address','is_certificate','description','start_time','rate','user_login','code_add'];

    /**
     * Relationship table
     */

    /**
     * End relationship
     */
}
