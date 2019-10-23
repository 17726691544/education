<?php

namespace app\api\controller;

use app\common\controller\Base;
use app\common\exception\BusinessBaseException;
use app\common\model\AgentOther;
use app\common\model\Orders;
use app\common\model\OrdersOther;
use app\common\service\WxService;
use app\common\validate\BaseValidate;
use app\common\model\Course as CourseModel;
use app\common\model\TeachCenter as TeachCenterModel;
use app\common\model\User as UserModel;
use app\common\validate\OrderV;
use think\Db;

class Order extends Base
{
    /**
     * 创建课程购买订单
     * @return \think\response\Json
     * @throws \app\common\exception\BusinessBaseException
     */
    public function create()
    {
        (new BaseValidate())->tokenChick();
        $uid = $this->getUid();
        $params = $this->getParams(['name', 'id_card', 'course_id', 'center_id', 'grade']);
        $rule = [
            'name' => 'require|min:1',
            'id_card' => 'require|idCard',
            'course_id' => 'require|integer|>:0',
            'center_id' => 'require|integer|>:0',
            'grade' => 'require|min:1'
        ];
        $msg = [
            'name' => '学生姓名不为空',
            'id_card' => '身份证号不正确',
            'course_id' => '操作错误',
            'center_id' => '操作错误',
            'grade' => '操作错误'
        ];

        $r = $this->validate($params, $rule, $msg);
        if (true !== $r) {
            return $this->jsonBack(1, $r);
        }

        try {
            $course = CourseModel::where('id', $params['course_id'])
                ->where('status', 0)
                ->find();
            if (!$course) throw $this->createError('课程不存在或已经删除');

            $center = TeachCenterModel::where('id', $params['center_id'])
                ->where('status', 1)
                ->find();
            if (!$center) throw $this->createError('教学中心不存在或已经删除');
            if (!in_array($params['grade'], $course->grades_arr)) throw $this->createError('不支持的年级');

            $order = Orders::create([
                'user_id' => $uid,
                'course_id' => $params['course_id'],
                'center_id' => $params['center_id'],
                'name' => $params['name'],
                'id_card' => $params['id_card'],
                'grade' => $params['grade'],
                'price' => $course->price,
                'create_at' => time()
            ]);

            $user = UserModel::get($uid);
            if ($user->id_card === '' || $user->real_name === '') {
                $user->id_card = $params['id_card'];
                $user->real_name = $params['name'];
                $user->save();
            }

            return $this->jsonBack(0, '创建成功', $order->id);
        } catch (\Exception $e) {
            return $this->jsonBack(2, $e->getMessage());
        }
    }

    /**
     * 创建购买耳机类型的订单
     */
    public function createOther()
    {
        $params = $this->getParams(['course_id', 'name', 'tel', 'num', 'province_id', 'city_id',
            'country_id', 'dl_province_id', 'dl_city_id', 'dl_country_id',
            'address']);
        (new OrderV())->tokenChick()->goChick($params);
        $uid = $this->getUid();

        //判断当前产品是否有效
        $course = CourseModel::where('id', $params['course_id'])
            ->where('status', 0)
            ->find();
        if (!$course) {
            throw new BusinessBaseException('商品不存在或已删除了');
        }

        $num = $params['num'];
        $dlPcc = '';
        $price = $course->price;
        $applyStatus = 0;

        //判断用户代理关系
        $user = UserModel::get($uid);
        if ($user->is_ej_qd === 1) {
            //区代
            $price = $course->qd_price;
        } else {
            //数量大于等于区代限制数量
            if ($num >= ($course->qd_num)) {
                //判断代理区域信息
                if (!($params['dl_province_id'] && $params['dl_city_id'] && $params['dl_country_id'])) {
                    throw new BusinessBaseException('请选择代理区域信息');
                }
                //判断该代理区域是否已经被代理
                if ($this->hasAgent($params['dl_province_id'], $params['dl_city_id'], $params['dl_country_id'])) {
                    throw new BusinessBaseException('该区域已有代理');
                }

                $applyStatus = 2;
                $price = $course->qd_price;
                $dlPcc = $params['dl_province_id'] . ',' . $params['dl_city_id'] . ',' . $params['dl_country_id'];
            } else {
                if ($user->is_ej_gd === 1) {
                    $price = $course->gd_price;
                } else {
                    if ($num >= ($course->gd_num)) {
                        $applyStatus = 1;
                        $price = $course->gd_price;
                    }
                }
            }
        }

        //创建订单
        $pcc = $params['province_id'] . ',' . $params['city_id'] . ',' . $params['country_id'];

        $order = OrdersOther::create([
            'user_id' => $uid,
            'course_id' => $params['course_id'],
            'name' => $params['name'],
            'tel' => $params['tel'],
            'price' => $price,
            'total_price' => $price * $num,
            'num' => $num,
            'dl_pcc' => $dlPcc,
            'pcc' => $pcc,
            'address' => $params['address'],
            'create_at' => time(),
            'apply_status' => $applyStatus,
        ]);
        return $this->jsonBack(0, '创建成功', $order->id);

    }

    /**
     * 判断耳机区域是否被代理
     * @param $provinceId
     * @param $cityId
     * @param $countryId
     */
    private function hasAgent($provinceId, $cityId, $countryId)
    {
        $agentOtherWhere = [
            'province_id' => $provinceId,
            'city_id' => $cityId,
            'country_id' => $countryId
        ];
        return AgentOther::where($agentOtherWhere)->find();
    }

    /**
     * 订单支付
     * @return \think\response\Json
     * @throws \app\common\exception\BusinessBaseException
     */
    public function pay()
    {
        (new BaseValidate())->tokenChick();
        $uid = $this->getUid();
        $params = $this->getParams(['order_id', 'type']);
        $order_type = $this->request->param('order_type') ?? '0';
        $rule = [
            'order_id' => 'require|integer|>:0',
            'type' => 'require|integer|in:1,2'
        ];
        $msg = [
            'order_id' => '错误的操作',
            'type' => '错误的支付方式'
        ];

        $r = $this->validate($params, $rule, $msg);
        if (true !== $r) {
            return $this->jsonBack(1, $r);
        }
        if (!($order_type === '1' || $order_type === '0')) {
            return $this->jsonBack(1, '错误操作');
        }

        if ($params['type'] == 1) {
            //微信
            return $this->wxPay($params['order_id'], $uid, $order_type);
        } else {
            //支付宝
            return $this->aliPay($params['order_id'], $uid);
        }

    }

    /**
     * 微信支付
     * @param $order_id
     * @param $uid
     * @return \Exception|\think\response\Json
     */
    private function wxPay($order_id, $uid, $order_type = 0)
    {
        Db::startTrans();
        try {
            $service = new WxService();
            if ($order_type === '1') {
                $order = OrdersOther::where('id', $order_id)->lock(true)->find();
                if (!$order || $order->user_id !== $uid || $order->status !== 0) return $this->createError('订单不存在或已支付');

                if ($order->apply_status === 2 && !empty($order->dl_pcc)) {
                    $dl_pcc_arry = explode(',', $order->dl_pcc);
                    if (count($dl_pcc_arry) !== 3) {
                        throw new BusinessBaseException('支付失败');
                    }
                    //判断区域是否被代理
                    if ($this->hasAgent($dl_pcc_arry[0], $dl_pcc_arry[1], $dl_pcc_arry[2])) {
                        throw new BusinessBaseException('该区域已经被代理了');
                    }
                }
                $r = $service->unifiedOrder('名学汇-购买耳机', $order->order_number, $order->total_price);
            } else {
                $order = Orders::where('id', $order_id)->lock(true)->find();
                if (!$order || $order->user_id !== $uid || $order->status !== 0) return $this->createError('订单不存在或已支付');
                $r = $service->unifiedOrder('名学汇-购买课程', $order->order_number, $order->price);
            }
            if (false === $r) throw $this->createError('统一下单失败');
            Db::commit();
            return $this->jsonBack(0, '', $service->appPay($r));
        } catch (\Exception $e) {
            Db::rollback();
            return $this->jsonBack(11, $e instanceof BusinessBaseException ? $e->getMsg() : $e->getMessage());
        }
    }

    private function aliPay($order_id, $uid)
    {
        return $this->jsonBack(21, '');
    }

    /**
     * 订单支付状态查询
     * @return \think\response\Json
     */
    public function isPay()
    {
        $params = $this->getParams(['order_id']);
        $rule = [
            'order_id' => 'require|integer|>:0'
        ];
        $msg = [
            'order_id' => '错误的操作'
        ];

        $r = $this->validate($params, $rule, $msg);
        if (true !== $r) {
            return $this->jsonBack(1, $r);
        }

        $order = Orders::get($params['order_id']);
        if (!$order) return $this->jsonBack(2, '订单不存在');
        return $this->jsonBack(0, '', $order->status === 1);
    }

    /**
     * 订单支付状态查询
     * @return \think\response\Json
     */
    public function isOhterPay()
    {
        $params = $this->getParams(['order_id']);
        $rule = [
            'order_id' => 'require|integer|>:0'
        ];
        $msg = [
            'order_id' => '错误的操作'
        ];

        $r = $this->validate($params, $rule, $msg);
        if (true !== $r) {
            return $this->jsonBack(1, $r);
        }

        $order = OrdersOther::get($params['order_id']);
        if (!$order) return $this->jsonBack(2, '订单不存在');
        return $this->jsonBack(0, '', $order->status === 1);
    }
}