<?php

namespace app\common\model;

use think\Model;

class Bankcard extends Model
{
    protected $table = 'bank_card';
    /**
     * start_card
     * @param $value
     * @param $data
     * @return string
     */
    public function getStartCardAttr($value,$data) {
        return mb_substr($data['card'],0,4);
    }

    /**
     * end_card
     * @param $value
     * @param $data
     * @return string
     */
    public function getEndCardAttr($value,$data) {
        return mb_substr($data['card'],-4);
    }


    /**
     * hide_name
     * @param $value
     * @param $data
     * @return string
     */
    public function getHideNameAttr($value,$data) {
        if (mb_strlen($data['name']) > 2 ) {
            return '**' . mb_substr($data['name'],-1);
        }
        return '*' . mb_substr($data['name'],-1);
    }

    public static function getBankInfo($uid){
        return self::where('user_id',$uid)
            ->field(['id','bank','name','card'])
            ->select();
    }
}