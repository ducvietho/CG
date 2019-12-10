<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NurseInterest extends Model
{
    protected $table = 'nurse_interest_patient';
    protected $fillable = ['user_patient', 'user_nurse'];

    /**
     * Relationship table
     */

    /**
     * End relationship
     */
}
