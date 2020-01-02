<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $table = 'cities';
    protected $fillable = ['id', 'original_name', 'code','show_name','score'];

    /**
     * Relationship table
     */

    /**
     * End relationship
     */
}
