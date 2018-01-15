<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Attachment
 *
 * @property integer $id
 * @property integer $protocol_id
 * @property string $name
 * @property string $url
 * @property string $keep
 * @property string $flag
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Attachment whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Attachment whereProtocolId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Attachment whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Attachment whereUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Attachment whereKeep($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Attachment whereFlag($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Attachment whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Attachment whereUpdatedAt($value)
 */


class Attachment extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'protocol_id',
        'protocolnum',
        'name', 
        'savedPath',
        'mimeType',
        'keep',
        'expires',
        'flag'
    ];

    public function protocol(){
        return $this->belongsTo('App\Protocol');
    }
}
