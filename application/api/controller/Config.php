<?php


namespace app\api\controller;

use app\common\controller\Base;
use app\common\validate\BaseValidate;
use app\common\model\Config as ConfigModel;
class Config extends Base
{

    public function getConfig()
    {
        (new BaseValidate())->tokenChick();
        return ConfigModel::get(1);
    }
}