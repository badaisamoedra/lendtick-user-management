<?php

namespace App\Helpers;

Class Api {
  public static function response($s=true,$m=null,$d=[]){
    return ['status'=>is_bool($s)?$s:false,'message'=>is_string($m)?$m:null,'data'=>$d];
  }
}

