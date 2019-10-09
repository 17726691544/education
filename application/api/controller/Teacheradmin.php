<?php


namespace app\api\controller;

use app\api\service\TeacherAdminService;
use app\common\controller\Base;
use app\common\exception\BusinessBaseException;
use app\common\validate\PageV;
use app\common\validate\TeacherAdminV;

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
        if ($teachCenterList) {
            $toArray = $teachCenterList->toArray();
            if (!empty($toArray['data'])) {
                $dataArr = ($toArray['data'])[0];
                if (array_key_exists('teach_centers', $dataArr)) {
                    $data = $dataArr['teach_centers'];
                }

                $toArray['data'] = $data;
            }
        }
        return $this->jsonBack(0, '成功', $toArray ?? null);
    }

    /**
     * 获取学生列表
     */
    public function getStudentList()
    {
        $params = $this->getParams(['id', 'page', 'pageNum']);
        (new PageV())->tokenChick()->goChick($params);
        $uid = $this->getUid();

        $studentList = (new TeacherAdminService())->getStudentList($uid, $params['id'], $params['page'], $params['pageNum']);
        return $this->jsonBack(0, '成功', $studentList);
    }

    /**
     * 确认上课
     */
    public function confirm()
    {
        $params = $this->getParams(['id', 'center_id', 'status']);
        (new TeacherAdminV())->tokenChick()->goChick($params);
        $uid = $this->getUid();

        $confirm = (new TeacherAdminService())->confirm($uid, $params['id'], $params['center_id'], $params['status']);
        if (!$confirm) {
            throw new BusinessBaseException('确认失败');
        }
        return $this->jsonBack(0, '成功');
    }

}