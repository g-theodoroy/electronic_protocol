<?php

namespace App;

/**
 * App\Keepvalue
 *
 * @property integer $id
 * @property string $fakelos
 * @property integer $keep
 * @property string $keep_alt
 * @property string $describe
 * @property string $remarks
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $readNotifications
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $unreadNotifications
 * @method static \Illuminate\Database\Query\Builder|\App\Keepvalue whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Keepvalue whereFakelos($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Keepvalue whereKeep($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Keepvalue whereKeepAlt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Keepvalue whereDescribe($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Keepvalue whereRemarks($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Keepvalue whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Keepvalue whereUpdatedAt($value)
 */

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use App\Keepvalue;

class Keepvalue extends Model
{

    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fakelos', 'keep', 'keep_alt', 'describe', 'remarks',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */



}
