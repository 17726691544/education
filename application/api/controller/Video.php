<?php


namespace app\api\controller;

use app\common\controller\Base;
use app\common\validate\PageV;
use app\common\model\Video as VideoModel;

class Video extends Base
{

    /**
     * 获取课程视频列表
     * @return \think\response\Json
     * @throws \app\common\exception\BusinessBaseException
     */
    public function getVideoList()
    {
        $params = $this->getParams(['page', 'pageNum']);
        (new PageV())->goChick($params);
        $videoList = VideoModel::getVideoList($params['page'] ?? 1, $params['pageNum'] ?? 5);
        return $this->jsonBack(0, '成功', $videoList);

    }
}