<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nurse extends Model
{
    protected $table = 'patients';
    protected $fillable = ['relationship', 'user_login', 'name','gender','birthday','code_add','start_date','end_date','start_time','end_time','address','note'];

    /**
     * Relationship table
     */

    /**
     * End relationship
     */
}
