<?php


namespace app\common\model;


use think\Model;

class Banner extends Model
{
    protected $table = 'banner';

    protected $hidden = ['create_at'];

    public static function getBannerList($num)
    {
        $c = $num;
        return self::order('sort', 'asc')->limit($num)->select();
    }
}