<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Care extends Model
{
    protected $table = 'requests';
    protected $fillable = ['user_nurse', 'user_patient', 'user_login','type','status'];

    /**
     * Relationship table
     */

    /**
     * End relationship
     */
}
