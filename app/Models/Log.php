<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $fillable = ['id','user_login','device_name','version','created_at'];
}