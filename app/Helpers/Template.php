<?php

namespace App\Helpers;

Class Template {
  public static $lang = [
      'test_lang' => "Test language",
      'success' => "Berhasil"
  ];
  
  public static function lang($n=false){
    return self::_get('lang',$n);
  }

  private static function _get($t=false,$n=false){
    return (strtolower($t)=="lang")?(isset(self::$lang[$n])?self::$lang[$n]:$n):$n;
  }
}

