<?php


namespace app\common\model;


use think\Model;

class Video extends Model
{
    protected $table = 'video';

    public static function getVideoList($page, $pageNum)
    {
        return self::where('status', 0)->hidden(['create_at'])->paginate($pageNum,false, [
            'page' => $page
        ]);

    }
}