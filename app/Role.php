<?php

namespace App;

/**
 * App\Role
 *
 * @property integer $id
 * @property string $role
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $users
 * @method static \Illuminate\Database\Query\Builder|\App\Role whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Role whereRole($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Role whereUpdatedAt($value)
 */

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public function users(){
    	return $this->hasMany('App\User','role_id');
    }

        /**
     * Βρίσκω τον id του Διαχειριστή
     * 
     * @var array
     */
    public function get_admin_id(){
        return Role::where('role','Διαχειριστής')->first()->id;
    }

}
