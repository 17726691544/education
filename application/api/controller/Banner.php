<?php


namespace app\api\controller;
use app\common\model\Banner as BannerModel;
use app\common\controller\Base;
use app\common\validate\BannerV;
/**
 * banner controller
 * Class Banner
 * @package app\api\controller
 */
class Banner extends Base
{
    /**
     * 获取banner列表
     * @return \think\response\Json
     * @throws \app\common\exception\BusinessBaseException
     */
    public function getBannerList()
    {
        $params = $this->getParams(['num']);
        (new BannerV())->goChick($params);
        $banners = BannerModel::getBannerList($params['num'] ?? 5);

        return $this->jsonBack(0, '成功', $banners);
    }

    public function test()
    {
        echo 'dddd';
    }

    public function testaa() {
        return 'ssafsdfaasdfasdfsadfsadfasdfasdfasdfsadfsdf';
    }
}