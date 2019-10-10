<?php


namespace app\api\service;


use app\common\exception\BusinessBaseException;
use app\common\model\Bankcard;
use app\common\model\Config;
use app\common\model\User;
use app\common\model\WithdrawLogs;
use think\Db;

class UserWallerService
{

    /**
     * 提现
     * @param $uid
     * @param $bankCard_id
     * @param $moneyNum
     */
    public function withdraw($uid, $bankCard_id, $moneyNum,$safePass)
    {
        //获取用户信息
        $user = User::get($uid);
        if (!$user) {
            throw  new BusinessBaseException('用户不存在');
        }
        //判断安全密码是否正确
        $md5Pass = md5($user->getData('reg_at') . $safePass);
        if ($md5Pass !== $user->safe_pass) {
            throw new BusinessBaseException('安全密码错误');
        }


        $newMoneyNum = floor($moneyNum * 100) / 100;
        //从配置表中获取最小提现金额
        $config = Config::where('id', 1)->field('tixian_less')->find();
        if ($newMoneyNum < ($config->tixian_less)) {
            throw new BusinessBaseException('最小提现金额为：' + $config->tixian_less);
        }
        //判断用户的可用余额是否充足
        if ($user->balance < $newMoneyNum) {
            throw  new BusinessBaseException('余额不足');
        }
        //获取用户银行卡信息
        $bankCard = Bankcard::where('id', $bankCard_id)
            ->where('user_id', $uid)->find();
        if (!$bankCard) {
            throw new BusinessBaseException('没找到银行卡信息');
        }
        Db::startTrans();
        try {
            //减少用户的可用余额
            $userResult = Db::table('user')
                ->where('id', $uid)
                ->where('balance', '>=', $newMoneyNum)
                ->dec('balance', $newMoneyNum)
                ->update();
            if ($userResult !== 1) {
                throw new BusinessBaseException('提交失败');
            }
            //插入流失记录
            (new WithdrawLogs())->save([
                'user_id' => $uid,
                'num' => $newMoneyNum,
                'bank' => $bankCard->bank,
                'name' => $bankCard->name,
                'card' => $bankCard->card,
                'create_at' => time()
            ]);

            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            throw $e;
        }
        return true;
    }
}