<?php


namespace app\api\controller;

use app\common\controller\Base;
use app\common\exception\BusinessBaseException;
use think\Exception;

class Ok extends Base
{
    /**
     * 获取banner
     * @param int $bannerId
     * @return int
     */
    public function getBanner($bannerId = 1)
    {
        return '123123123123123';
    }
}