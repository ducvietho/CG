<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $table = 'districtes';
    protected $fillable = ['id', 'original_name', 'code','show_name','city_id'];

    /**
     * Relationship table
     */

    /**
     * End relationship
     */
}
