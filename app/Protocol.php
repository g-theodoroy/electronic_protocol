<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Protocol
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $protocolnum
 * @property integer $protocoldate
 * @property integer $etos
 * @property string $fakelos
 * @property string $thema
 * @property string $in_num
 * @property integer $in_date
 * @property string $in_topos_ekdosis
 * @property string $in_arxi_ekdosis
 * @property string $in_paraliptis
 * @property string $diekperaiosi
 * @property string $in_perilipsi
 * @property integer $out_date
 * @property string $out_to
 * @property string $out_cc
 * @property string $out_perilipsi
 * @property string $keywords
 * @property string $paratiriseis
 * @property string $flags
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Protocol whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Protocol whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Protocol whereProtocolnum($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Protocol whereProtocoldate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Protocol whereEtos($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Protocol whereFakelos($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Protocol whereThema($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Protocol whereInNum($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Protocol whereInDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Protocol whereInToposEkdosis($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Protocol whereInArxiEkdosis($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Protocol whereInParaliptis($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Protocol whereInResponsible($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Protocol whereInPerilipsi($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Protocol whereOutDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Protocol whereOutSintaktis($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Protocol whereOutTo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Protocol whereOutCc($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Protocol whereOutPerilipsi($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Protocol whereKeywords($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Protocol whereParatiriseis($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Protocol whereFlags($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Protocol whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Protocol whereUpdatedAt($value)
 */


class Protocol extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'protocolnum',
        'protocoldate', 
        'etos',
        'fakelos',
        'thema',
        'in_num',
        'in_date',
        'in_topos_ekdosis',
        'in_arxi_ekdosis',
        'in_paraliptis',
        'diekperaiosi',
        'in_perilipsi',
        'out_date',
        'diekp_date',
        'sxetiko',
        'out_to',
        'out_perilipsi',
        'keywords',
        'paratiriseis'
    ];

        public function attachments(){
    	return $this->hasMany('App\Attachment','protocol_id');
    }

}
