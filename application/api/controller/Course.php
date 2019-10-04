<?php


namespace app\api\controller;

use app\common\controller\Base;
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



}