<?php


namespace app\api\controller;

use app\common\controller\Base;
use app\common\validate\IdV;
use app\common\validate\PageV;
use app\common\model\Course as CourseModel;
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



}