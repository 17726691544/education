<?php


namespace app\api\controller;

use app\common\controller\Base;
use app\common\validate\BaseValidate;
use app\common\model\Config as ConfigModel;
class Config extends Base
{

    /**
     *获取配置信息
     * @return mixed
     * @throws \app\common\exception\BusinessBaseException
     */
    public function getConfig()
    {
        (new BaseValidate())->tokenChick();
        return ConfigModel::get(1);
    }
}