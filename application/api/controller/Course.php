<?php


namespace app\api\controller;

use app\common\controller\Base;
use app\common\model\Agent;
use app\common\validate\BaseValidate;
use app\common\validate\CourseV;
use app\common\validate\IdV;
use app\common\validate\PageV;
use app\common\model\Course as CourseModel;
use app\common\model\User;

/**
 * 产品controller
 * Class Course
 * @package app\api\controller
 */
class Course extends Base
{

    /**
     * 获取课程列表
     * @return \think\response\Json
     * @throws \app\common\exception\BusinessBaseException
     */
    public function getCourseList()
    {
        $params = $this->getParams(['page', 'pageNum']);
        (new PageV())->goChick($params);
        $courseList = CourseModel::getCourseList($params['page'] ?? 1, $params['pageNum'] ?? 5);
        return $this->jsonBack(0, '成功', $courseList);
    }

    /**
     * 课程详情
     * @return \think\response\Json
     * @throws \app\common\exception\BusinessBaseException
     */
    public function getCourseDetail()
    {
        $params = $this->getParams(['id']);
        (new IdV())->goChick($params);
        $courseDetail = (new CourseModel())->getCourseDetail($params['id']);
        return $this->jsonBack(0, '成功', $courseDetail);

    }


    /**
     * 判断区域是否已经代理了
     */
    public function hasAgent()
    {
        $params = $this->getParams(['province_id', 'city_id', 'country_id']);
        (new CourseV())->goChick($params);

        $where = [
            'province_id' => $params['province_id'],
            'city_id' => $params['city_id'],
            'country_id' => $params['country_id']
        ];
        $result = Agent::where($where)->select();

        if (empty($result->toArray())) {
            return $this->jsonBack(0, '成功', true);
        }
        return $this->jsonBack(0, '成功', false);

    }


    /**
     * 获取用户身份证信息
     * @return array|\PDOStatement|string|\think\Model|null
     */
    public function getIdCardInfo()
    {
        (new BaseValidate())->tokenChick();
        $uid = $this->getUid();
        $idCardInfo = User::where('id', $uid)->field(['id', 'id_card', 'real_name'])->find();
        return $this->jsonBack(0, '成功', $idCardInfo);
    }

}