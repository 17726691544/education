<?php


namespace app\common\model;


use think\Model;

class TeachCenter extends Model
{
    protected $table = 'teach_center';

    public static function  getTeachCenterList()
    {
        return self::where('status',1)
            ->visible(['id', 'agent_id','name','province', 'city', 'country'])//
            ->select();
    }

}