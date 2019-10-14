<?php

namespace app\api\controller;

use app\common\controller\Base;
use app\common\model\Orders;
use app\common\service\WxService;
use app\common\validate\BaseValidate;
use app\common\model\Course as CourseModel;
use app\common\model\TeachCenter as TeachCenterModel;
use app\common\model\User as UserModel;
use think\Db;

class Order extends Base
{
    /**
     * 创建课程购买订单
     * @return \think\response\Json
     * @throws \app\common\exception\BusinessBaseException
     */
        public function create() {
        (new BaseValidate())->tokenChick();
        $uid = $this->getUid();
        $params = $this->getParams(['name','id_card','course_id','center_id','grade']);
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

        $r = $this->validate($params,$rule,$msg);
        if (true !== $r) {
            return $this->jsonBack(1,$r);
        }

        try {
            $course = CourseModel::where('id',$params['course_id'])
                ->where('status',0)
                ->find();
            if (!$course) throw $this->createError('课程不存在或已经删除');

            $center = TeachCenterModel::where('id',$params['center_id'])
                ->where('status',1)
                ->find();
            if (!$center) throw $this->createError('教学中心不存在或已经删除');
            if (!in_array($params['grade'],$course->grades_arr)) throw $this->createError('不支持的年级');

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

            return $this->jsonBack(0,'创建成功',$order->id);
        } catch (\Exception $e) {
            return $this->jsonBack(2,$e->getMessage());
        }
    }

    /**
     * 订单支付
     * @return \think\response\Json
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
            $order = Orders::where('id',$order_id)->lock(true)->find();
            if (!$order || $order->user_id !== $uid || $order->status !== 0) return $this->createError('订单不存在或已支付');
            $service = new WxService();
            $r = $service->unifiedOrder('名学汇-购买课程',$order->order_number,$order->price);
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

        $order = Orders::get($params['order_id']);
        if (!$order) return $this->jsonBack(2,'订单不存在');
        return $this->jsonBack(0,'',$order->status === 1);
    }
}