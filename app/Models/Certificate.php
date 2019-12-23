<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $table = 'certificates';
    protected $fillable = ['image', 'user_login','status','type'];

    /**
     * Relationship table
     */

    /**
     * End relationship
     */
}
