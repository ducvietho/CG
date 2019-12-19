<?php

namespace App;

use App\Models\City;
use App\Models\District;
use Illuminate\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
    use Authenticatable, Authorizable;
    private const TYPE_NURSE = 1;
    private const TYPE_PATIENT = 2;
    private const TYPE_ACCOUNT_NORMAL = 0;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'users';
    protected $fillable = [
        'user_name', 'password', 'name', 'email', 'phone', 'address_detail', 'avatar', 'type', 'block', 'code_address', 'gender', 'birthday', 'fcm_token', 'provide_id', 'provide_id', 'district_code', 'user_id','type_account','setting_care','is_register'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public static function createNew($data)
    {
        $user = User::create([
            'user_id' => $data['user_id'],
            'password' => Hash::make($data['password']),
            'name' => $data['name'],
            'user_name' => $data['user_name'],
            'gender' => $data['gender'],
            'birthday' => $data['birthday'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'code_address' => isset($data['city_code']) ? $data['city_code'] : '',
            'district_code' => isset($data['district_code']) ? $data['district_code'] : '',
            'address_detail' => isset($data['address']) ? $data['address'] : '',
            'avatar' =>  env('AVATAR_DEFAULT'),
            'type' => isset($data['type']) ? $data['type'] : 1,
            'fcm_token' => '',
            'provide_id' => isset($data['provide_id']) ? $data['provide_id'] : '',
            'type_account' => isset($data['type_account']) ? $data['type_account'] : 0,
            'setting_care' => 1,
            'is_register' => 0
        ]);
        return $user;
    }
    public function city(){
        return $this->belongsTo(City::class,'code_address','code');
    }

    public function district(){
        return $this->belongsTo(District::class,'district_code','code');
    }
}
