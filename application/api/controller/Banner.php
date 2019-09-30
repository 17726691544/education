<?php


namespace app\api\controller;

use app\common\controller\Base;

class Banner extends Base
{
    /**
     * 获取banner
     * @param int $bannerId
     * @return int
     */
    public function getBanner($bannerId = 1)
    {

        return $bannerId;
    }
}