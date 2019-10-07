<?php


namespace app\api\controller;

use app\api\service\AreaAdminService;
use app\common\controller\Base;
use app\common\exception\BusinessBaseException;
use app\common\validate\AreaAdminV;
use app\common\validate\BaseValidate;
use app\common\validate\PageV;

/**
 * 区域管理 controller
 * Class Areaadmin
 * @package app\api\controller
 */
class Areaadmin extends Base
{

    /**
     * 获取教学中心区域地址
     */
    public function getTeachCenterArea()
    {
        (new BaseValidate())->tokenChick();
        $uid = $this->getUid();
        $teachCenterArea = (new AreaAdminService())->getTeachCenterArea($uid);
        return $this->jsonBack(0, '成功', $teachCenterArea);
    }

    /**
     * 申请教学中心
     */
    public function applyTeachCenter()
    {
        $params = $this->getParams(['name', 'area']);
        (new AreaAdminV())->tokenChick()->goChick($params);
        $uid = $this->getUid();

        $result = (new AreaAdminService())->applyTeachCenter($uid, $params['name'], $params['area']);
        if (!$result) {
            throw new BusinessBaseException('申请失败');
        }
        return $this->jsonBack(0, '申请成功,等待审核');
    }

    /**
     * 获取收益记录列表
     */
    public function getEarningList()
    {
        $params = $this->getParams(['page', 'pageNum']);
        (new PageV())->tokenChick()->goChick($params);
        $uid = $this->getUid();
        $earningList = (new AreaAdminService())->getEarningList($uid, $params['page'] ?? 1, $params['pageNum'] ?? 5);
        return $this->jsonBack(0, '成功', $earningList);
    }


    /**
     * 分页获取教育中心列表
     */
    public function getTeachCenterList()
    {
        $params = $this->getParams(['page', 'pageNum']);
        (new PageV())->tokenChick()->goChick($params);
        $uid = $this->getUid();
        $teachCenterList = (new AreaAdminService())->getTeachCenterList($uid, $params['page'] ?? 1, $params['pageNum'] ?? 5);
        return $this->jsonBack(0, '成功', $teachCenterList);
    }

    /**
     * 获取学生列表
     */
    public function getStudentList()
    {
        $params = $this->getParams(['center_id', 'page', 'pageNum']);
        (new AreaAdminV())->tokenChick()->goChick($params);
        $uid = $this->getUid();
        $studentList = (new AreaAdminService())->getStudentList($uid, $params['center_id'], $params['page'] ?? 1, $params['pageNum'] ?? 5);
        return $this->jsonBack(0, '成功', $studentList);
    }

    /**
     * 确认提交
     */
    public function confirm()
    {
        $ids = $this->request->param('ids/a');
        $center_id = $this->request->param('center_id');
        $status = $this->request->param('status');

        (new AreaAdminV())->tokenChick()->goChick(['ids' => $ids, 'center_id' => $center_id, 'status' => $status]);
        $uid = $this->getUid();

        $confirm = (new AreaAdminService())->confirm($uid, $ids, $center_id, $status);
        if (!$confirm) {
            throw new BusinessBaseException('确认提交失败');
        }
        return $this->jsonBack(0, '成功');
    }
}

