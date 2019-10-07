<?php


namespace app\api\controller;

use app\common\controller\Base;
use app\common\exception\BusinessBaseException;
use app\common\model\UserLogsCopy;
use app\common\validate\BaseValidate;
use app\common\model\User;
use app\common\validate\PageV;

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

        $balanceDetail = UserLogsCopy::getBalanceDetail($uid, $params['page'] ?? 1, $params['pageNum'] ?? 5);
        return $this->jsonBack(0, '成功', $balanceDetail);

    }

}