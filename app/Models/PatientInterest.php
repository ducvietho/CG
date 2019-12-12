<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientInterest extends Model
{
    protected $table = 'interests';
    protected $fillable = ['user_login', 'user_nurse'];

    /**
     * Relationship table
     */

    /**
     * End relationship
     */
}
