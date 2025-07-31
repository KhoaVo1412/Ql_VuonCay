<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use OwenIt\Auditing\Contracts\Auditable;

class User extends Authenticatable implements JWTSubject, Auditable
{
    use HasFactory, Notifiable, HasApiTokens, HasRoles;
    use \OwenIt\Auditing\Auditable;

    public function auditExclude()
    {
        return ['email_verified_at', 'password', 'remember_token', 'reset_password_token', 'reset_password_expired_at']; // Bỏ qua các trường này khi audit
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        // 'farm_id',
    ];
    protected $auditExclude = [
        'email_verified_at',
        'password',
        'remember_token',
        'reset_password_token',
        'reset_password_expired_at',
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    public function decomposes()
    {
        return $this->hasMany(Decompose::class);
    }
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function customers()
    {
        return $this->hasOne(Customer::class, 'user_id', 'id');
    }
    // public function login_histories()
    // {
    //     return $this->hasMany(LoginHistory::class);
    // }
}
