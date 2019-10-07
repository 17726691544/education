<?php


namespace app\api\controller;

use app\common\controller\Base;
use app\common\model\TeachCenter as TeachCenterModel;

/**
 * 教学中心controller
 * Class Teachcenter
 * @package app\api\controller
 */
class Teachcenter extends Base
{

    /**
     * 获取教育中心
     */
    public function getTeachCenterList()
    {
        $teachCenterList = TeachCenterModel::getTeachCenterList();
        return $this->jsonBack(0, '成功', $teachCenterList);
    }
}