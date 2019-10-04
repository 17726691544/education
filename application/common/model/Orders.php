<?php


namespace app\common\model;


use think\Model;

class Orders extends Model
{
    protected $table = 'orders';

    public function course()
    {
        return $this->belongsTo('Course', 'course_id', 'id');
    }

    public function getCourseOrderList($uid, $page, $pageNum)
    {
        return self::with('course')
            ->where('user_id', $uid)//
            ->visible(['course' => ['id', 'title', 'cover'],''])//
            ->where('status', 1)//
            ->paginate($pageNum,false,[
                'page'=>$page
            ]);
    }
}