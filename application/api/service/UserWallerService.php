<?php


namespace app\api\service;


use app\common\exception\BusinessBaseException;
use app\common\model\Bankcard;
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
    public function withdraw($uid, $bankCard_id, $moneyNum)
    {
        //获取用户信息
        $user = User::get($uid);
        if (!$user) {
            throw  new BusinessBaseException('用户不存在');
        }
        $newMoneyNum = floor($moneyNum * 100) / 100;
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
                ->where('id',$uid)
                ->where('balance','>=',$newMoneyNum)
                ->dec('balance',$newMoneyNum)
                ->update();
           if($userResult !== 1){
               throw new BusinessBaseException('提交失败');
           }
           //查询流失记录
            (new WithdrawLogs())->save([
                'user_id'=>$uid,
                'num'=>$newMoneyNum,
                'bank'=>$bankCard->bank,
                'name'=>$bankCard->name,
                'card'=>$bankCard->card,
                'create_at'=>time()
            ]);

            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            throw $e;
        }
        return true;
    }
}