<?php


namespace app\api\controller;

use app\api\service\AreaAdminService;
use app\common\controller\Base;
use app\common\exception\BusinessBaseException;
use app\common\model\Agent;
use app\common\validate\AreaAdminV;
use app\common\validate\BaseValidate;

/**
 * 区域管理 controller
 * Class Areaadmin
 * @package app\api\controller
 */
class Areaadmin extends Base
{

    /**
     * 获取教学中心区域地址
     */
    public function getTeachCenterArea()
    {
        (new BaseValidate())->tokenChick();
        $uid = $this->getUid();
        $teachCenterArea = (new AreaAdminService())->getTeachCenterArea($uid);
        return $this->jsonBack(0, '成功', $teachCenterArea);


    }

    /**
     * 申请教学中心
     */
    public function applyTeachCenter()
    {
        $params = $this->getParams(['name','area']);
        (new AreaAdminV())->tokenChick()->goChick($params);
        $uid = $this->getUid();

        $result = (new AreaAdminService())->applyTeachCenter($uid, $params['name'], $params['area']);
        if(!$result){
            throw new BusinessBaseException('申请失败');
        }
        $this->jsonBack(0,'申请成功');
    }
}