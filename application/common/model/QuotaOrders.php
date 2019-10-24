<?php


namespace app\common\model;


use think\Model;

class QuotaOrders extends Model
{
    const START_TRADE_NUM = 1470178326;
    protected $table = 'quota_orders';

    /**
     * order_number
     * @param $value
     * @param $data
     * @return int
     */
    public function getOrderNumberAttr($value,$data) {
        return 'Q' . ($data['id'] + self::START_TRADE_NUM);
    }

    /**
     * 订单编号转ID
     * @param $orderNo
     * @return bool|int|string
     */
    public static function orderNo2Id($orderNo) {
        $pattern = '/^Q\d{10,}$/';
        if (!preg_match($pattern,$orderNo)) return false;
        $number = substr($orderNo,1);
        $id = $number - self::START_TRADE_NUM;
        if ($id <= 0) return false;
        return $id;
    }
}