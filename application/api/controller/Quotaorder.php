<?php

namespace app\api\controller;

use app\common\controller\Base;
use app\common\model\Agent;
use app\common\model\City;
use app\common\model\Config;
use app\common\model\Country;
use app\common\model\Province;
use app\common\model\QuotaOrders;
use app\common\service\WxService;
use app\common\validate\BaseValidate;
use app\common\model\User as UserModel;
use think\Db;

class Quotaorder extends Base
{

    /**
     * 区域代理订单
     * @return \think\response\Json
     * @throws \app\common\exception\BusinessBaseException
     */
    public function area() {
        (new BaseValidate())->tokenChick();
        $uid = $this->getUid();
        $params = $this->getParams(['province_id','city_id','country_id']);
        $rule = [
            'province_id' => 'require|integer|>:0',
            'city_id' => 'require|integer|>:0',
            'country_id' => 'require|integer|>:0'
        ];
        $msg = [
            'province_id' => '操作错误',
            'city_id' => '操作错误',
            'country_id' => '操作错误'
        ];

        $r = $this->validate($params,$rule,$msg);
        if (true !== $r) {
            return $this->jsonBack(1,$r);
        }

        try {
            $user = UserModel::find($uid);
            if ($user->is_qd === 1) throw $this->createError('你已经是区域代理了');

            $province = Province::find($params['province_id']);
            $city = City::find($params['city_id']);
            $country = Country::find($params['country_id']);
            if (!$province || !$city || !$country || $country->city_id !== $city->city_id
                || $city->province_id !== $province->province_id) throw $this->createError('错误的区域');

            $agent = Agent::where('province_id', $params['province_id'])
                ->where('city_id', $params['city_id'])
                ->where('country_id', $params['country_id'])
                ->find();

            if ($agent) throw $this->createError('该区域已被代理');

            $config = Config::find(1);

            $order = QuotaOrders::create([
                'user_id' => $uid,
                'quota_price' => $config->quota_price,
                'quota_num' => $config->qd_quota,
                'total' => round($config->quota_price * $config->qd_quota,2),
                'type' => 2,
                'pcc' => "{$province->id},{$city->id},$country->id",
                'create_at' => time()
            ]);

            return $this->jsonBack(0,'创建成功',$order->id);
        } catch (\Exception $e) {
            return $this->jsonBack(2,$e->getMessage());
        }
    }

    /**
     * 个人代理订单
     * @return \think\response\Json
     * @throws \app\common\exception\BusinessBaseException
     */
    public function person() {
        (new BaseValidate())->tokenChick();
        $uid = $this->getUid();
        $user = UserModel::find($uid);
        if ($user->is_gd === 1) return $this->jsonBack(1,'你已经是个人代理了');

        $config = Config::find(1);
        $order = QuotaOrders::create([
            'user_id' => $uid,
            'quota_price' => $config->quota_price,
            'quota_num' => $config->gd_quota,
            'total' => round($config->quota_price * $config->gd_quota,2),
            'type' => 1,
            'create_at' => time()
        ]);

        return $this->jsonBack(0,'创建成功',$order->id);
    }

    /**
     * 普通订单
     * @return \think\response\Json
     * @throws \app\common\exception\BusinessBaseException
     */
    public function normal() {
        (new BaseValidate())->tokenChick();
        $uid = $this->getUid();
        $params = $this->getParams(['num']);
        $rule = [
            'num' => 'require|integer|>:0'
        ];
        $msg = [
            'num' => '购买数量不小于1'
        ];

        $r = $this->validate($params,$rule,$msg);
        if (true !== $r) {
            return $this->jsonBack(1,$r);
        }

        $config = Config::find(1);
        $order = QuotaOrders::create([
            'user_id' => $uid,
            'quota_price' => $config->quota_price,
            'quota_num' => $params['num'],
            'total' => round($config->quota_price * $params['num'],2),
            'type' => 0,
            'create_at' => time()
        ]);

        return $this->jsonBack(0,'创建成功',$order->id);
    }

    /**
     * 订单支付
     * @return \Exception|\think\response\Json
     * @throws \app\common\exception\BusinessBaseException
     */
    public function pay() {
        (new BaseValidate())->tokenChick();
        $uid = $this->getUid();
        $params = $this->getParams(['order_id', 'type']);
        $rule = [
            'order_id' => 'require|integer|>:0',
            'type' => 'require|integer|in:1,2'
        ];
        $msg = [
            'order_id' => '错误的操作',
            'type' => '错误的支付方式'
        ];

        $r = $this->validate($params,$rule,$msg);
        if (true !== $r) {
            return $this->jsonBack(1,$r);
        }

        if ($params['type'] == 1) {
            //微信
            return $this->wxPay($params['order_id'],$uid);
        } else {
            //支付宝
            return $this->aliPay($params['order_id'],$uid);
        }
    }

    /**
     * 微信支付
     * @param $order_id
     * @param $uid
     * @return \Exception|\think\response\Json
     */
    private function wxPay($order_id, $uid) {
        Db::startTrans();
        try {
            $order = QuotaOrders::where('id',$order_id)->lock(true)->find();
            if (!$order || $order->user_id !== $uid || $order->status !== 0) return $this->createError('订单不存在或已支付');
            $service = new WxService();

            if ($order->type === 0) {
                $title = '名学汇-购买名额';
            } elseif ($order->type === 1) {
                $title = '名学汇-个代加盟';
            } else {
                $title = '名学汇-区代加盟';
            }

            $r = $service->unifiedOrder($title,$order->order_number,$order->total);
            if (false === $r) throw $this->createError('统一下单失败');
            Db::commit();
            return $this->jsonBack(0,'',$service->appPay($r));
        } catch (\Exception $e) {
            Db::rollback();
            return $this->jsonBack(11,$e->getMessage());
        }
    }


    private function aliPay($order_id, $uid) {
        return $this->jsonBack(21, '');
    }

    /**
     * 订单支付状态查询
     * @return \think\response\Json
     */
    public function isPay() {
        $params = $this->getParams(['order_id']);
        $rule = [
            'order_id' => 'require|integer|>:0'
        ];
        $msg = [
            'order_id' => '错误的操作'
        ];

        $r = $this->validate($params,$rule,$msg);
        if (true !== $r) {
            return $this->jsonBack(1,$r);
        }

        $order = QuotaOrders::get($params['order_id']);
        if (!$order) return $this->jsonBack(2,'订单不存在');
        return $this->jsonBack(0,'',$order->status === 1);
    }

}