<?php


namespace app\common\model;


use think\Model;

class Video extends Model
{
    protected $table = 'video';

    public static function getVideoList($page, $pageNum)
    {
        return self::where('status', 0)//
        ->where('is_show', 1)
            ->hidden(['create_at'])//
            ->order('create_at', 'desc')//
            ->paginate($pageNum, false, ['page' => $page]);
    }

    public static function getVideoListByNum($ids)
    {
        return self::where('status', 0)//
            ->where(['id' => $ids])
            ->hidden(['create_at'])//
            ->order('create_at', 'desc')//
            ->select();
    }
}