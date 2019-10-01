<?php

namespace app\api\controller;

use app\api\validate\BannerV;
use app\common\controller\Base;

class Banner extends Base
{

    public function getBanner()
    {
        $params = $this->getParams(['bannerId', 'uId']);
        $bannerV = new BannerV();
        return 'success';

    }
}