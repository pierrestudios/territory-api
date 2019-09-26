<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Languages extends Model
{
    protected $langPacks = null;

    public function __construct($langPacks = null, $lang = 'en')
    {
        $this->langPacks = json_decode($langPacks)->$lang;
    }

    public static function getTranslation($langPacks, $key, $defaulText = '')
    {
        return !empty($langPacks->$key) ? $langPacks->$key : $defaulText;
    }

    public function translate($key, $defaulText = '')
    {
        return static::getTranslation($this->langPacks, $key, $defaulText);
    }
}
