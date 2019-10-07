<?php


namespace app\common\model;


use think\Model;

class Teacher extends Model
{
    protected $table = 'teacher';

    public function getTipsAttr($value)
    {
        if (!empty($value)) {
            return json_decode($value);
        }
    }

    public function teachCenters()
    {
        return $this->belongsToMany('teachCenter', 'teacher_center', 'center_id', 'teacher_id');
    }

    public static function getTeacherList($page, $pageNum)
    {
        return self::field(['id', 'name', 'education', 'position', 'tips', 'image'])->paginate($pageNum, false, [
            'page' => $page
        ]);
    }

    public static function getTeacherDetail($teacherId)
    {
        return self::get($teacherId);
    }


    public function getTeachCenterList($uid, $page, $pageNum)
    {
        return self::with(['teachCenters' => function ($query) {
            $query->where('status', 1)//
            ->hidden(['pivot', 'agent_id', 'agent_user_id', 'province_id', 'city_id', 'country_id', 'create_at', 'status']);
        }])
            ->where('user_id', $uid)//
            ->visible([''])
            ->paginate($pageNum, false, [
                'page' => $page
            ]);
    }
}