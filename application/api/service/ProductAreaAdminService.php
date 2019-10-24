<?php


namespace app\api\service;


use app\common\exception\BusinessBaseException;
use app\common\model\AgentOther;
use app\common\model\OrdersOther;
use app\common\model\User;

class ProductAreaAdminService
{
    public function productStatistics($uid)
    {
        //鉴权
        $this->hasPermission($uid);

        //获取区域代理信息
        $agentOhter = AgentOther::where('user_id', $uid)
            ->field(['province_id', 'city_id', 'country_id', 'province', 'city', 'country'])
            ->find();
        if (!$agentOhter) {
            throw new BusinessBaseException('没找到区域代理信息');
        }

        //统计用户区域销量信息
        $where = [
            'province_id' => $agentOhter->province_id,
            'city_id' => $agentOhter->city_id,
            'country_id' => $agentOhter->country_id

        ];
        $ordersOther = OrdersOther::where($where)
            ->where('status', '<>', 0)
            ->field(['province_id', 'city_id', 'country_id',
                'sum(num) as totalNum', 'sum(total_price) as totalPrice'])
            ->group(['province_id', 'city_id', 'country_id'])
            ->find();
        if ($agentOhter && $ordersOther) {
            $agentOhter = $agentOhter->toArray();
            $agentOhter['totalNum'] = $ordersOther->totalNum;
            $agentOhter['totalPrice'] = $ordersOther->totalPrice;
        }

        return $agentOhter;
    }

    /**
     * 判断权限
     * @param $uid
     * @return mixed
     * @throws BusinessBaseException
     */
    private function hasPermission($uid)
    {
        $user = User::get($uid);
        if (!$user) {
            throw new BusinessBaseException('错误的操作');
        }
        if ($user->is_ej_qd !== 1) {
            throw new BusinessBaseException('你还不是耳机区域代理');
        }
        return $user;
    }
}