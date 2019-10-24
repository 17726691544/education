<?php


namespace app\api\controller;


use app\api\service\ProductAreaAdminService;
use app\common\controller\Base;
use app\common\validate\BaseValidate;

/**
 * 产品管理controller
 * Class ProductAreaadmin
 * @package app\api\controller
 */
class Productareaadmin extends Base
{

    /**
     * 区域产品统计
     */
    public function productStatistics()
    {
        (new BaseValidate())->tokenChick();
        $uid = $this->getUid();
        $result = (new ProductAreaAdminService())->productStatistics($uid);
        return $this->jsonBack(0,'成功',$result);
        
    }

}