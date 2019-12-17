<?php

namespace App\Models;

use App\User;
use Elasticquent\ElasticquentTrait;
use Illuminate\Database\Eloquent\Model;

class NurseProfile extends Model
{
    use ElasticquentTrait;
    protected $table = 'profile_nurse';
    protected $fillable = ['nationality', 'start_date', 'end_date','start_time','end_time','address','is_certificate','description','start_time','rate','user_login','code_add'];

    /**
     * Relationship table
     */

    /**
     * End relationship
     */
    public function user(){
        return $this->belongsTo(User::class,'user_login','id');
    }
}
