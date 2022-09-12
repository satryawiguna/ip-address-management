<?php

namespace App\Domain;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Core\Domain\BaseAuthEntity;
use App\Core\Domain\Contract\IAggregateRoot;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;


class User extends BaseAuthEntity implements IAggregateRoot, MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    const TABLE_NAME = 'users';
    const MORPH_NAME = 'users';

    protected $table = User::TABLE_NAME;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'full_name',
        'nick_name',
        'email',
        'password',
        'created_by',
        'updated_by'
    ];

    public $timestamps = false;

    protected $dates = [
        'deleted_at'
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

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function oAuth()
    {
        return $this->hasMany(OAuth::class, 'user_id');
    }
}
