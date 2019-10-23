<?php


namespace app\api\controller;


use app\api\service\OrdersOtherService;
use app\common\controller\Base;
use app\common\exception\BusinessBaseException;
use app\common\validate\IdV;
use app\common\validate\PageV;
use app\common\model\OrdersOther as OrdersOtherModel;

class Ordersother extends Base
{

    /**
     * 分页获取购买耳机记录
     */
    public function getBuyOtherRecordList()
    {
        $params = $this->getParams(['page', 'pageNum']);
        (new PageV())->tokenChick()->goChick($params);
        $uid = $this->getUid();

        $buyRecordList = (new OrdersOtherModel())->getBuyOtherRecordList($uid, $params['page'] ?? 1, $params['pageNum'] ?? 5);
        return $this->jsonBack(0, '成功', $buyRecordList);
    }

    /**
     * 确认收货
     */
    public function confirmSign()
    {
        $params = $this->getParams(['id']);
        (new IdV())->tokenChick()->goChick($params);
        $uid = $this->getUid();

        $result = (new OrdersOtherService())->confirmSign($uid, $params['id']);
        if(!$result){
            throw new BusinessBaseException('确认收货失败');
        }
        return $this->jsonBack(0,'确认收货成功');
    }



}