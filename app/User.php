<?php

namespace App;

use App\Models\City;
use App\Models\District;
use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

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
        'user_name', 'password', 'name', 'email', 'phone', 'address_detail', 'avatar', 'type', 'block', 'code_address', 'gender', 'birthday', 'fcm_token', 'provide_id', 'provide_id', 'district_code', 'user_id', 'type_account', 'setting_care', 'is_register'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];
    protected $attributes = [
        'avatar'=>'',
        'type'=>1,
        'type_account'=>0,
        'fcm_token'=>'',
        'block'=>0,
        'is_sign'=>0,
        'role'=>0,
        'district_code'=>'',
        'provide_id'=>0,
        'code_address'=>'',
        'address_detail'=>'',
        'password'=>'',
        'user_id'=>'',
        'user_name'=>''
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
        if (isset($data['city_code']))
        {
            $data['code_address'] = $data['city_code'];
        }
        if (isset($data['address_detail']))
        {
            $data['address_detail'] = $data['address'];
        }
        if (isset($data['type']))
        {
            $data['is_register'] = ($data['type'] == 2) ? 1: 0;
        }
        return self::create($data);
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'code_address', 'code');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_code', 'code');
    }
}
