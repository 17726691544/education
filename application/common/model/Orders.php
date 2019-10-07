<?php


namespace app\common\model;


use think\Model;

class Orders extends Model
{
    const START_TRADE_NUM = 1370178326;
    protected $table = 'orders';

    public function course()
    {
        return $this->belongsTo('Course', 'course_id', 'id');
    }

    public function getCourseOrderList($uid, $page, $pageNum)
    {
        return self::with(['course'=>function($query){
                $query->field(['id', 'title', 'cover']);
        }])
            ->field(['course_id'])
            ->where('user_id', $uid)//
            ->visible([''])//
            ->where('status', 1)//
            ->paginate($pageNum, false, [
                'page' => $page
            ]);
    }

    public function getBuyRecordList($uid, $page, $pageNum)
    {
        return self::with(['course' => function ($Query) {
            $Query->field(['id', 'title', 'cover']);
        }])->where('user_id', $uid)
            ->field(['id', 'course_id', 'price', 'pay_type', 'status', 'pay_at', 'create_at'])
            ->paginate($pageNum, false, ['page' => $page]);
    }


}