<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    protected $fillable = ['key', 'value'];

    public function getConfigValueOf($key) {
        $c = Config::firstOrCreate(['key' => $key]);
        return $c->value;
    }

    public function setConfigValueOf($key, $value) {
    	Config::whereKey($key)->update(['value' => $value]);
    	return ;
    }

    public function getConfigValues() {
        $confs =  Config::all();
        $configs = [];
        foreach($confs as $conf){
            $configs[$conf->key] = $conf->value;
        }
        return $configs;
    }

}
