<?php


namespace app\api\controller;

use app\api\service\CourseCentorService;
use app\common\controller\Base;
use app\common\exception\BusinessBaseException;
use app\common\model\Orders;
use app\common\validate\CourseCentorV;
use app\common\validate\IdV;
use app\common\validate\PageV;

/**
 * 课程中心controller
 * Class Coursecentor
 * @package app\api\controller
 */
class Coursecentor extends Base
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
        return $this->jsonBack(0, '成功', $courseOrderList);
    }

    /**
     * 获取课程中心课程详情
     * @throws \app\common\exception\BusinessBaseException
     */
    public function getCourseCentorDetail()
    {
        $params = $this->getParams(['id']);
        (new IdV())->tokenChick()->goChick($params);
        $uid = $this->getUid();
        $courseCentorDetail = (new CourseCentorService())->getCourseCentorDetail($uid, $params['id']);
        return $this->jsonBack(0, '成功', $courseCentorDetail);

    }

    /**
     * 课程中心打卡
     * @throws \app\common\exception\BusinessBaseException
     */
    public function sinCourse()
    {
        $params = $this->getParams(['signId','order_id']);
        (new CourseCentorV())->tokenChick()->goChick($params);
        $uid = $this->getUid();

        $sinCourse = (new CourseCentorService())->sinCourse($uid,$params['signId'],$params['order_id']);
        if (!$sinCourse) {
            throw new BusinessBaseException('打卡失败');
        }
        return $this->jsonBack(0, '打卡成功');
    }
}