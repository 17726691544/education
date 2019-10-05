<?php


namespace app\common\model;


use think\Model;

class AttendClassRecord extends Model
{
    protected $table = 'attend_class_record';

    public function user()
    {
        return $this->belongsTo('User', 'user_id', 'id');

    }

    public function course()
    {
        return $this->belongsTo('Course', 'course_id', 'id');
    }

    public function getUserListByCenterId($centerId,$page,$pageNum)
    {
        return self::with(['user', 'course'])
            ->visible(['id','center_id',
                'user' => ['id', 'nick', 'is_qd', 'is_gd', 'is_teacher', 'invite_code', 'head_url'],//
                'course' => ['id','title']])//
            ->where('center_id', $centerId)//
            ->where('status', 0)
            ->paginate($pageNum, false, [
                'page' => $page
            ]);
    }
}