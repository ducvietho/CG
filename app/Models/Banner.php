<?php


namespace App\Models;


use App\User;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = ['id','image','active','user_login','name'];
    public function user(){
        return $this->belongsTo(User::class,'user_login','id');
    }
}