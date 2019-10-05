<?php


namespace app\api\controller;

use app\api\service\TeacherAdminService;
use app\common\controller\Base;
use app\common\validate\PageV;
use app\common\model\Teacher;

/**
 * 教师管理controller
 * Class Teacheradmin
 * @package app\api\controller
 */
class Teacheradmin extends Base
{
    /**
     * 获取我的教学中心列表
     */
    public function getTeachCenterList()
    {
        $params = $this->getParams(['page', 'pageNum']);
        (new PageV())->tokenChick()->goChick($params);
        $uid = $this->getUid();
        $teachCenterList = (new TeacherAdminService())->getTeachCenterList($uid, $params['page'] ?? 1, $params['pageNum'] ?? 5);
        return $this->jsonBack(0, '成功', $teachCenterList);
    }

    /**
     * 获取学生列表
     */
    public function getStudentList()
    {
        $params = $this->getParams(['id']);
        (new PageV())->tokenChick()->goChick($params);
        $uid = $this->getUid();

        $studentList = (new TeacherAdminService())->getStudentList($uid, $params['id']);
        return $this->jsonBack(0, '成功', $studentList);
    }

    /**
     * 确认上课
     */
    public function confirm()
    {
        
    }

}