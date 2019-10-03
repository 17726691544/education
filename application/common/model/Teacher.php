<?php


namespace app\common\model;


use think\Model;

class Teacher extends Model
{
    protected $table = 'teacher';

    public static function getTeacherList($page, $pageNum)
    {
        return self::paginate($pageNum, false, [
            'page' => $page
        ])->hidden(['user_id','ability','create_at','cover']);
    }

    public static function getTeacherDetail($teacherId){
        return self::get($teacherId);
    }
}