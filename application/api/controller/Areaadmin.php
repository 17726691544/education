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
        //对获取到的结果进行处理
//        if ($studentList) {
//            $studentListArr = ($studentList->toArray())['data'];
//            foreach ($studentListArr as $key => $val) {
//                $user_id = $studentList[$key]['user_id'];
//                $id = $studentList[$key]['id'];
//                $resultArr[$user_id]['user'] = $studentList[$key]['user'];
//                $items['id'] = $id;
//                $items['center_id'] = $studentList[$key]['center_id'];
//                $items['course_title'] = $studentList[$key]['course_title'];
//                $resultArr[$user_id]['items'][] = $items;
//            }
//
//            if (!empty($resultArr)) {
//                $newResultArr['data'] = array_values($resultArr);
//            }
//        }


        return $this->jsonBack(0, '成功', $studentList);
    }
}

