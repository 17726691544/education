<?php


namespace app\common\model;


use think\Model;

class Config extends Model
{

    protected $table = 'config';

    public function getGdRuleAttr($value)
    {
        if(!empty($value)){
            return json_decode($value);
        }
    }
    public function getQdRuleAttr($value)
    {
        if(!empty($value)){
            return json_decode($value);
        }
    }

}