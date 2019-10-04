<?php


namespace app\common\model;


use think\Model;

class Course extends Model
{
    protected $table = 'course';

    public static function getCourseList($page, $pageNum)
    {
        return self::where('status', 0)//
        ->order('create_at', 'desc')//
        ->hidden(['create_at','status','qydl','qdtjr','jxzx','grdl','gdtjr','grades'])//
        ->paginate($pageNum, false, ['page' => $page]);

    }

}