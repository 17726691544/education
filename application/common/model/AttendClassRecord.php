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

    public function attendClassRecords()
    {
        return $this->hasMany('AttendClassRecord', 'user_id', 'user_id');
    }

    public function getUserListByCenterId($centerId, $page, $pageNum)
    {
        return self::with('user')
            ->visible(['id', 'center_id', 'course_title', 'user_id',
                'user' => ['id', 'nick', 'is_qd', 'is_gd', 'is_teacher', 'invite_code', 'head_url', 'id_card', 'real_name'],//
            ])//
            ->where('center_id', $centerId)//
            ->where('status', 1)
            ->paginate($pageNum, false, [
                'page' => $page
            ]);
    }

    public function getUserItemListByCenterId($centerId, $page, $pageNum)
    {
        return self
            ::with(['user' => function ($query) {
                $query->field(['id', 'nick', 'is_qd', 'is_gd', 'is_teacher', 'invite_code', 'head_url', 'id_card', 'real_name']);
            }, 'attendClassRecords' => function ($query) {
                $query->field(['id', 'agent_id', 'center_id', 'user_id', 'course_id', 'course_title'])
                    ->where('status', 0);
            }])
            ->field(['user_id'])
            ->where('center_id', $centerId)//
            ->where('status', 0)
            ->group(['user_id'])
            ->paginate($pageNum, false, [
                'page' => $page
            ]);
    }
}