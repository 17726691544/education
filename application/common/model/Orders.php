<?php


namespace app\common\model;


use think\Model;

class Orders extends Model
{
    const START_TRADE_NUM = 1370178326;
    protected $table = 'orders';

    protected $type = [
        'create_at' => 'timestamp'
    ];

    public function course()
    {
        return $this->belongsTo('Course', 'course_id', 'id');
    }

    public function getCourseOrderList($uid, $page, $pageNum)
    {
        return self::with(['course' => function ($query) {
            $query->field(['id', 'title', 'cover']);
        }])
            ->field(['id', 'course_id'])
            ->where('user_id', $uid)//
            ->visible(['id'])//
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
            ->order('id','desc')
            ->paginate($pageNum, false, ['page' => $page]);
    }

    /**
     * order_number
     * @param $value
     * @param $data
     * @return int
     */
    public function getOrderNumberAttr($value, $data)
    {
        return $data['id'] + self::START_TRADE_NUM;
    }

    /**
     * 订单编号转ID
     * @param $orderNo
     * @return bool|int|string
     */
    public static function orderNo2Id($orderNo)
    {
        $pattern = '/^\d{10,}$/';
        if (!preg_match($pattern, $orderNo)) return false;
        $id = $orderNo - self::START_TRADE_NUM;
        if ($id <= 0) return false;
        return $id;
    }
}