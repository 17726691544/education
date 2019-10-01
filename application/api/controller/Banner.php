<?php

namespace app\api\controller;

use app\common\validate\BannerV;
use app\common\controller\Base;

class Banner extends Base
{

    public function getBanner()
    {
        $params = $this->getParams(['bannerId', 'uId']);
        (new BannerV())->goChick($params,true);

        return 'success';

    }
}