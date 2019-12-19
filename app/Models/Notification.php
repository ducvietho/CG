<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';
    protected $fillable = ['user_to', 'user_from', 'user_patient','type','unread','unwatch','request_id'];

    /**
     * Relationship table
     */

    /**
     * End relationship
     */
}
