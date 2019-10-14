<?php


namespace app\common\model;


use think\Model;

class Banner extends Model
{
    protected $table = 'banner';

    public static function getBannerList($num)
    {
        return self::order('sort', 'asc')->hidden(['create_at'])->limit($num)->select();
    }
}