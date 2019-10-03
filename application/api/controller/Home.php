<?php


namespace app\api\controller;

use app\common\controller\Base;
use app\common\model\Banner;
use app\common\model\Teacher;
use app\common\model\Team;
use app\common\validate\HomeV;
use app\common\validate\PageV;

/**
 * 首页controlle
 * Class Home
 * @package app\api\controller
 */
class Home extends Base
{

    /**
     * 获取banner列表
     * @return \think\response\Json
     * @throws \app\common\exception\BusinessBaseException
     */
    public function getBannerList()
    {
        $params = $this->getParams(['num']);
        (new HomeV())->goChick($params);
        $banners = Banner::getBannerList($params['num'] ?? 5);

        return $this->jsonBack(0, '成功', $banners);
    }

    /**
     * 分页获取团队列表
     */
    public function getTeamList()
    {
        $params = $this->getParams(['page', 'pageNum']);
        (new PageV())->goChick($params);
        $teamList = Team::getTeamList($params['page'] ?? 1, $params['pageNum'] ?? 5);
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
        (new HomeV())->goChick($params);
        $teamDetail = Team::getTeamDetail($params['id']);
        return $this->jsonBack(0, '成功', $teamDetail);
    }

    /**
     * 获取教师列表
     * @return \think\response\Json
     * @throws \app\common\exception\BusinessBaseException
     */
    public function getTeacherList()
    {
        $params = $this->getParams(['page', 'pageNum']);
        (new PageV())->goChick($params);
        $teacherList = Teacher::getTeacherList($params['page'] ?? 1, $params['pageNum'] ?? 5);
        return $this->jsonBack(0, '成功', $teacherList);
    }

    /**
     * 获取教师详情
     * @return \think\response\Json
     * @throws \app\common\exception\BusinessBaseException
     */
    public function getTeacherDetail()
    {
        $params = $this->getParams(['id']);
        (new HomeV())->goChick($params);
        $teacherDetail = Teacher::getTeacherDetail($params['id']);
        return $this->jsonBack(0, '成功', $teacherDetail);
    }
}