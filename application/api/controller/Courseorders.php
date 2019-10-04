<?php


namespace app\api\controller;

use app\api\service\CourseordersService;
use app\common\controller\Base;
use app\common\model\Course as CourseModel;
use app\common\model\Orders;
use app\common\validate\IdV;
use app\common\validate\PageV;

/**
 * 课程订单 controller
 * Class Courseorders
 * @package app\api\controller
 */
class Courseorders extends Base
{
    /**
     * 分页获取课程中心列表
     * @return \think\response\Json
     * @throws \app\common\exception\BusinessBaseException
     */
    public function getCourseCenterList()
    {
        $params = $this->getParams(['page', 'pageNum']);
        (new PageV())->tokenChick()->goChick($params);
        $uid = $this->getUid();
        $courseOrderList = (new Orders())->getCourseOrderList($uid, $params['page'] ?? 1, $params['pageNum'] ?? 5);
        return $this->jsonBack('0', '成功', $courseOrderList);
    }

    /**
     * 获取购买中心课程详情
     * @throws \app\common\exception\BusinessBaseException
     */
    public function getCourseCentorDetail()
    {
        $params = $this->getParams(['id']);
        (new IdV())->tokenChick()->goChick($params);
        $uid = $this->getUid();
        $courseCentorDetail = (new CourseordersService())->getCourseCentorDetail($uid, $params['id']);
        return $this->jsonBack(0,'成功',$courseCentorDetail);

    }

}