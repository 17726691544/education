<?php


namespace app\api\controller;

use app\common\controller\Base;
use app\common\validate\PageV;
use app\common\model\Team as TeamModel;
use app\common\validate\IdV;

class Team extends Base
{
    /**
     * 分页获取团队列表
     */
    public function getTeamList()
    {
        $params = $this->getParams(['page', 'pageNum']);
        (new PageV())->goChick($params);
        $teamList = TeamModel::getTeamList($params['page'] ?? 1, $params['pageNum'] ?? 5);
        return $this->jsonBack(0, '成功', $teamList);
    }

    /**
     * 获取团队详情
     * @return \think\response\Json
     * @throws \app\common\exception\BusinessBaseException
     */
    public function getTeamDetail()
    {
        $params = $this->getParams(['id']);
        (new IdV())->goChick($params);
        $teamDetail = TeamModel::getTeamDetail($params['id']);
        return $this->jsonBack(0, '成功', $teamDetail);
    }
}