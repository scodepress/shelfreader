<?php

namespace App\Models;

use App\Notifications\MailResetPasswordNotification;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable implements CanResetPassword
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    

    protected $fillable = [
        'name', 'email', 'institution', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function sendPasswordResetNotification($token)
    {   
        $this->notify(new MailResetPasswordNotification($token));
       
    }

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
    * undocumented function
    *
    * @return void
    */
    public function isAdmin()
    {
    	return null !== User::where('id', Auth::user()->id)->where('privs',1)->first();
    }
    
}

