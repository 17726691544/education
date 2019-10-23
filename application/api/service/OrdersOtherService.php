<?php


namespace app\api\service;


use app\common\exception\BusinessBaseException;
use app\common\model\OrdersOther;
use app\common\model\UserBalanceOther;
use think\Db;

class OrdersOtherService
{

    public function confirmSign($uid, $orderId)
    {
        Db::startTrans();
        try {
            $ordersOther = OrdersOther::where('id', $orderId)->lock(true)->find();
            if (!$ordersOther || $ordersOther->user_id !== $uid || $ordersOther->status !== 2) {
                throw new BusinessBaseException('订单不存在或重复操作');
            }
            //修改订单状态
            OrdersOther::where('id',$orderId)->setField('status',3);
            //释放奖励
            $this->unfreezeBalance($orderId);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            throw  $e;
        }

    }

    /**
     * 解冻资金
     * @param $uid
     * @param $courseId
     */
    private function unfreezeBalance($orderOtherId)
    {
        //获取冻结执行信息
        $userBalanceOtherList = UserBalanceOther
            ::field(['id', 'user_id','sum(lock_balance)'=>'totalLockBalance'])
            ->where('order_other_id', $orderOtherId)
            ->where('status', 0)
            ->group(['id', 'user_id'])
            ->select();
        if (!$userBalanceOtherList) {
            return;
        }
        foreach ($userBalanceOtherList as $item) {
            //减少锁定余额 增加可用余额
            $changBalance = $item->totalLockBalance;
            $decResult = Db::name('user')
                ->where('id', $item->user_id)
                ->where('lock_balance', '>=', $changBalance)
                ->dec('lock_balance', $changBalance)
                ->inc('balance', $changBalance)
                ->update();
            if ($decResult !== 1) {
                throw new BusinessBaseException('解冻资金失败');
            }
            //更改冻结信息状态
            $ee = $item->id;
            $update = (new UserBalanceOther())->save(['status' => 1], ['id' => $item->id]);
            if (!$update) {
                throw new BusinessBaseException('解冻资金失败');
            }
        }
    }


}