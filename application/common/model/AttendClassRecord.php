<?php


namespace app\common\model;


use think\Model;

class AttendClassRecord extends Model
{
    protected $table = 'attend_class_record';

    /**
     * 关联查询
     * @return \think\model\relation\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('User', 'user_id', 'id');

    }

    public function getUserListByCenterId($centerId,$page,$pageNum)
    {
        return self::with('user')
            ->visible(['id','center_id','course_title',
                'user' => ['id', 'nick', 'is_qd', 'is_gd', 'is_teacher', 'invite_code', 'head_url','id_card','real_name'],//
            ])//
            ->where('center_id', $centerId)//
            ->where('status', 1)
            ->paginate($pageNum, false, [
                'page' => $page
            ]);
    }
}