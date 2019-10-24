<?php


namespace app\common\model;


use think\Model;

class OrdersOther extends Model
{
    const START_TRADE_NUM = 1370178326;
    protected $table = 'orders_other';

    protected $type = [
        'create_at' => 'timestamp',
        'pay_at' => 'timestamp'
    ];

    public function getStatusDescAttr($value, $data)
    {
        $statusDescArr = [
            0 => '待支付',
            1 => '已支付',
            2 => '已发货',
            3 => '确认收货 '
        ];
        return $statusDescArr[$data['status']];
    }

    public function course()
    {
        return $this->belongsTo('Course', 'course_id', 'id');
    }

    /**
     * order_number
     * @param $value
     * @param $data
     * @return int
     */
    public function getOrderNumberAttr($value, $data)
    {
        return 'E' . ($data['id'] + self::START_TRADE_NUM);
    }

    /**
     * 订单编号转ID
     * @param $orderNo
     * @return bool|int|string
     */
    public static function orderNo2Id($orderNo)
    {
        $pattern = '/^E\d{10,}$/';
        if (!preg_match($pattern, $orderNo)) return false;
        $number = substr($orderNo, 1);
        $id = $number - self::START_TRADE_NUM;
        if ($id <= 0) return false;
        return $id;
    }

    public function getBuyOtherRecordList($uid, $page, $pageNum)
    {
        return self::with(['course' => function ($query) {
            $query->field(['id', 'title', 'cover']);
        }])
            ->where('user_id', $uid)//
            ->where('status', '<>',0)
            ->field(['id', 'course_id', 'total_price', 'status', 'pay_at'])
            //  ->visible(['id', 'total_price', 'status','pay_at'])//
            ->hidden(['course_id'])
            ->order('id', 'desc')
            ->append(['status_desc'])
            ->paginate($pageNum, false, [
                'page' => $page
            ]);
    }

    public function getBuyOtherRecordDetail($uid, $orderId)
    {
        return self::with(['course' => function ($query) {
            $query->field(['id', 'title', 'cover']);
        }])
            ->where('id', $orderId)//
            ->where('user_id', $uid)//
            ->field(['id', 'course_id', 'name', 'tel', 'price', 'total_price'
                , 'num', 'pay_type', 'status', 'address', 'pay_at', 'create_at'])
            ->append(['status_desc', 'order_number'])
            ->find();
    }
}