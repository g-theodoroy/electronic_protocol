<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use App\Notifications\ResetPasswordNotification; 

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'username', 'email', 'role_id', 'password', 'active',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function role(){
        return $this->belongsTo('App\Role');
    }


    public static function my_active_users(){
        // return User::where('role_id', '!=',   Role::whereRole('Αναγνώστης')->first()->id)->wherenotNull('active')->orderby('name')->get();
        return User::wherenotNull('active')->orderby('name')->get();
    }

    public static function my_users(){
        // return User::where('role_id', '!=',   Role::whereRole('Αναγνώστης')->first()->id)->orderby('name')->get();
        return User::orderby('name')->get();
    }

    public static function diekperaiosi_name($diekperaiosi){
        $str = 'd';
        foreach(explode(',',$diekperaiosi) as $d){
            if($d && strpos($str,substr($d,0,1))!==false){
                return User::find(ltrim($d, $str))->name;
                break;
            }
        }
        return null;
    }

    /**
     * Βρίσκω τον αριθμό των διαχειριστών
     * αφου βρω τον id του Διαχειριστή
     * @var array
     */
    public function get_num_of_admins(){
        return User::whereRoleId(Role::whereRole('Διαχειριστής')->first()->id)->wherenotNull('active')->count();
    }

    public static function get_writers_and_admins(){
        // return User::where('role_id', '!=',    Role::whereRole('Αναγνώστης')->first()->id)->wherenotNull('active')->orderby('name')->get();
        return User::wherenotNull('active')->orderby('name')->get();
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
