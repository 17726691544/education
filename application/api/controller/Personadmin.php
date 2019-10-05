<?php


namespace app\api\controller;

use app\api\service\PersonAdminService;
use app\common\controller\Base;
use app\common\exception\BusinessBaseException;
use app\common\validate\BaseValidate;
use app\common\validate\PageV;
use app\common\validate\PersonAdminV;

class Personadmin extends Base
{

    /**
     * 获取直推个代列表
     * @return \think\response\Json
     * @throws \app\common\exception\BusinessBaseException
     */
    public function getDirectPersonList()
    {
        $params = $this->getParams(['page', 'pageNum']);
        (new PageV())->tokenChick()->goChick($params);
        $uid = $this->getUid();
        $directPersonList = (new PersonAdminService())->getDirectPersonList($uid, $params['page'] ?? 1, $params['pageNum'] ?? 5);
        return $this->jsonBack('0', '成功', $directPersonList);
    }

    /**
     * 获取vip会员列表
     * @throws \app\common\exception\BusinessBaseException
     */
    public function getVipUserList()
    {
        $params = $this->getParams(['page', 'pageNum']);
        (new PageV())->tokenChick()->goChick($params);
        $uid = $this->getUid();

        $vipUserList = (new PersonAdminService())->getVipUserList($uid, $params['page'] ?? 1, $params['pageNum'] ?? 5);
        return $this->jsonBack('0', '成功', $vipUserList);
    }

    /**
     * 直推个代详情列表
     */
    public function getDirectDetailList()
    {
        $params = $this->getParams(['id', 'page', 'pageNum']);
        (new PageV())->tokenChick()->goChick($params);
        $uid = $this->getUid();
        $directDetailList = (new PersonAdminService())->getDirectDetailList($uid, $params['id'], $params['page'] ?? 1, $params['pageNum'] ?? 5);
        return $this->jsonBack('0', '成功', $directDetailList);
    }

    /**
     * 获取名额
     * @return \think\response\Json
     * @throws BusinessBaseException
     */
    public function getQuota()
    {
        (new BaseValidate())->tokenChick();
        $uid = $this->getUid();
        $user = (new PersonAdminService())->getQuota($uid);
        return $this->jsonBack(0,'成功',$user->quota);
    }

    /**
     * 通过编号获取用户的基本信息
     * @throws BusinessBaseException
     */
    public function getUserInfo()
    {
        $params = $this->getParams(['invite_code']);
        (new PersonAdminV())->tokenChick()->goChick($params);
        $uid = $this->getUid();
        //TODO
        
    }



}