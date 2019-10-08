<?php


namespace app\api\controller;

use app\api\service\UserWallerService;
use app\common\controller\Base;
use app\common\exception\BusinessBaseException;
use app\common\model\UserLogs;
use app\common\model\WithdrawLogs;
use app\common\validate\BaseValidate;
use app\common\model\User;
use app\common\validate\PageV;
use app\common\model\BankCard;
use app\common\validate\UserWallerV;
use http\Params;

class Userwaller extends Base
{

    /**
     * 获取资产
     */
    public function getAssets()
    {
        (new BaseValidate())->tokenChick();
        $uid = $this->getUid();
        $user = User::where('id', $uid)->field(['balance', 'lock_balance'])->find();
        if ($user) {
            $newUser = $user->toArray();
            $newUser['totalBalance'] = $newUser['balance'] + $newUser['lock_balance'];
            return $this->jsonBack(0, '成功', $newUser);
        } else {
            throw new BusinessBaseException('获取失败');
        }
    }

    /**
     * 分页获取收支明细
     */
    public function getBalanceDetail()
    {
        $params = $this->getParams(['page', 'pageNum']);
        (new PageV())->tokenChick()->goChick($params);
        $uid = $this->getUid();

        $balanceDetail = UserLogs::getBalanceDetail($uid, $params['page'] ?? 1, $params['pageNum'] ?? 5);
        return $this->jsonBack(0, '成功', $balanceDetail);

    }

    /**
     * 提现获取银行卡信息
     */
    public function getBankInfo()
    {
        (new BaseValidate())->tokenChick();
        $uid = $this->getUid();
        $bankInfo = BankCard::getBankInfo($uid);

        return $this->jsonBack(0, '成功', $bankInfo);
    }

    /**
     * 提现
     */
    public function withdraw()
    {
        $params = $this->getParams(['bankCard_id', 'moneyNum']);
        (new UserWallerV())->tokenChick()->goChick($params);
        $uid = $this->getUid();

       $result =  (new UserWallerService())->withdraw($uid,$params['bankCard_id'],$params['moneyNum']);
       if(!$result){
           throw new BusinessBaseException('提交失败');
       }
        return $this->jsonBack(0,'提交成功');
        
    }
    
    /**
     * 分页获取提现记录
     */
    public function getWithdrawList()
    {
        $params = $this->getParams(['page', 'pageNum']);
        (new PageV())->tokenChick()->goChick($params);
        $uid = $this->getUid();

        $WithdrawList = WithdrawLogs::getWithdrawList($uid, $params['page'] ?? 1, $params['pageNum'] ?? 5);
        return $this->jsonBack(0, '成功', $WithdrawList);
    }

}