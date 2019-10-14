<?php


namespace app\api\controller;

use app\api\service\QuotaadminService;
use app\common\controller\Base;
use app\common\exception\BusinessBaseException;
use app\common\validate\BaseValidate;
use app\common\validate\PageV;
use app\common\validate\PersonAdminV;
use http\Params;

/**
 *
 * Class Quotaadmin
 * @package app\api\controller
 */
class Quotaadmin extends Base
{
    /**
     * 名额管理中获取名额
     * @return \think\response\Json
     * @throws BusinessBaseException
     */
    public function getQuota()
    {
        (new BaseValidate())->tokenChick();
        $uid = $this->getUid();
        $user = (new QuotaadminService())->getQuota($uid);
        return $this->jsonBack(0, '成功', $user->quota);
    }

    /**
     * 转账名额中通过编号获取用户的基本信息
     * @throws BusinessBaseException
     */
    public function getUserInfo()
    {
        $params = $this->getParams(['invite_code']);
        (new PersonAdminV())->tokenChick()->goChick($params);
        $uid = $this->getUid();
        $userInfo = (new QuotaadminService())->getUserInfo($uid, $params['invite_code']);
        return $this->jsonBack(0, '成功', $userInfo);
    }

    /**
     * 转出名额
     */
    public function transferQuota()
    {
        $params = $this->getParams(['id', 'num','safe_pass']);
        (new PersonAdminV())->tokenChick()->goChick($params);
        $uid = $this->getUid();
        $transferQuota = (new QuotaadminService())->transferQuota($uid, $params['id'], $params['num'],$params['safe_pass']);
        if (!$transferQuota) {
            throw  new BusinessBaseException('转出名额失败');
        }
        return $this->jsonBack(0, '转出成功');
    }

    /**
     * 分页获取转让记录列表
     */
    public function getTransferRecordList()
    {
        $params = $this->getParams(['page', 'pageNum']);
        (new PageV())->tokenChick()->goChick($params);
        $uid = $this->getUid();
        $transferRecordList = (new QuotaadminService())->getTransferRecordList($uid, $params['page'] ?? 1, $params['pageNum'] ?? 5);
        return $this->jsonBack(0, '成功', $transferRecordList);
    }

}