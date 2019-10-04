<?php


namespace app\api\controller;

use app\common\controller\Base;
use app\common\model\Orders;
use app\common\validate\PageV;

/**
 * 课程订单 controller
 * Class Courseorders
 * @package app\api\controller
 */
class Courseorders extends Base
{
    public function getCourseOrderList()
    {
        $params = $this->getParams(['page', 'pageNum']);
        (new PageV())->tokenChick()->goChick($params);
        $uid = $this->getUid();
        $teacherList = Orders::getCourseOrderList($uid, $params['page'] ?? 1, $params['pageNum'] ?? 5);

    }

}