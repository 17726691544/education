<?php

namespace app\common\model;

use think\Model;

class BankCard extends Model
{
    protected $table = 'bank_card';

    public static function getBankInfo($uid){
        return self::where('user_id',$uid)
            ->field(['id','bank','name','card'])
            ->select();
    }
}