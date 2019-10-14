<?php


namespace app\common\model;


use think\Model;

class TeachCenter extends Model
{
    protected $table = 'teach_center';

    public static function  getTeachCenterList()
    {
        return self::where('status',1)
            ->field(['id','name','province_id','province_id','city_id','country_id','province', 'city', 'country','area'])//
            ->select();

    }

}