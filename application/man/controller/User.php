<?php

namespace app\man\controller;
use app\common\controller\Base;
use app\common\model\AgentOther;
use app\common\model\OrdersOther;
use app\man\model\User as UserModel;
use app\man\model\UserLogs;
use app\man\model\WithdrawLogs;
use think\Db;

class User extends Base
{
    protected $middleware = ['app\man\middleware\Auth'];

    /**
     * 用户列表
     * @return mixed|string
     */
    public function index() {
        $params = $this->getParams(['is_qd','is_gd','is_teacher','type','code']);
        $params['is_qd'] = $params['is_qd'] === null ? '-1' : $params['is_qd'];
        $params['is_gd'] = $params['is_gd'] === null ? '-1' : $params['is_gd'];
        $params['is_teacher'] = $params['is_teacher'] === null ? '-1' : $params['is_teacher'];
        $params['type'] = $params['type'] === null ? '0' : $params['type'];

        $query = [
            'query' => $params
        ];

        if ($this->request->isPost()) {
            $query['page'] = 1;
        }

        $map = [];
        if ($params['is_qd'] >= 0) $map[] = ['is_qd','=',$params['is_qd']];
        if ($params['is_gd'] >= 0) $map[] = ['is_gd','=',$params['is_gd']];
        if ($params['is_teacher'] >= 0) $map[] = ['is_teacher','=',$params['is_teacher']];
        if ($params['type'] > 0) $map[] = ['u_type','=',$params['type']];

        if ($params['code']) {
            $user_id = UserModel::where('tel',$params['code'])->value('id');
            if (!$user_id) {
                $this->error('用户不存在');
            }
            $map[] = ['id','=',$user_id];
        }

        try {
            $list = UserModel::with('inviter')->where($map)->order('id desc')->paginate(10,false,$query);
            $this->assign('params',$params);
            $this->assign('list',$list);
            return $this->fetch('index');
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 提现申请
     * @return mixed|string
     */
    public function withdraw() {
        $params = $this->getParams(['status','code']);
        $params['status'] = $params['status'] === null ? '0' : $params['status'];
        $query = [
            'query' => $params
        ];

        if ($this->request->isPost()) {
            $query['page'] = 1;
        }

        $showNum = false;

        $map = [];
        if ($params['status'] >= 0) $map[] = ['status','=',$params['status']];

        if ($params['code']) {
            $user_id = UserModel::where('tel',$params['code'])->value('id');
            if (!$user_id) {
                $this->error('用户不存在');
            }
            $map[] = ['user_id','=',$user_id];
            $showNum = true;
        }

        try {
            $list = WithdrawLogs::with('user')->where($map)->order('id desc')->paginate(10,false,$query);
            if ($showNum) {
                $total = WithdrawLogs::where($map)->sum('num');
                $this->assign('total',$total);
            } else {
                $this->assign('total',0);
            }
            $this->assign('params',$params);
            $this->assign('list',$list);
            return $this->fetch('withdraw');
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 提现处理
     */
    public function dealWithdraw() {
        $params = $this->getParams(['id','status']);
        $rule = [
            'id' => 'require|integer|>:0',
            'status' => 'require|integer|in:1,2'
        ];
        $msg = [
            'id' => '错误的操作',
            'status' => '错误的操作'
        ];
        $r = $this->validate($params,$rule,$msg);
        if (true !== $r) {
            $this->error($r);
        }

        Db::startTrans();
        try {
            $with = WithdrawLogs::get($params['id']);
            $with->status = $params['status'];
            $with->save();
            if ($params['status'] === '2') {
                UserModel::where('id',$with->user_id)->setInc('balance',$with->num);
            }
            Db::commit();
        }catch (\Exception $e) {
            $this->error($e->getMessage());
            Db::rollback();
        }
        $this->success('操作成功');
    }

    /**
     * 用户流水
     * @return mixed|string
     */
    public function log() {
        $params = $this->getParams(['type','start','end','code']);
        $params['type'] = $params['type'] === null ? '0' : $params['type'];
        $query = [
            'query' => $params
        ];
        if ($this->request->isPost()) {
            $query['page'] = 1;
        }

        $showNum = false;

        $map = [];
        if ($params['start'] && $params['end']) {
            $startTime = strtotime($params['start']);
            $endTime = strtotime($params['end']);
            if ($startTime >= $endTime) {
                $this->error('开始时间必须小于结束时间');
            }
            $map[] = ['create_at','between',[$startTime,$endTime]];
        }

        if ($params['code']) {
            $user_id = UserModel::where('tel',$params['code'])->value('id');
            if (!$user_id) {
                $this->error('用户不存在');
            }
            $map[] = ['user_id','=',$user_id];
            $showNum = true;
        }

        if ($params['type'] > 0) $map[] = ['type','=',$params['type']];

        try {
            $list = UserLogs::with('user')->where($map)->order('id desc')->paginate(10,false,$query);
            if ($showNum) {
                $total = UserLogs::where($map)->sum('num');
                $this->assign('total',$total);
            } else {
                $this->assign('total',0);
            }

            $this->assign('params',$params);
            $this->assign('list',$list);
            return $this->fetch('log');
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 商品订单
     * @return mixed
     */
    public function goodsOrder() {
        $params = $this->getParams(['status','code']);
        $params['status'] = $params['status'] === null ? '1' : $params['status'];
        $query = [
            'query' => $params
        ];
        if ($this->request->isPost()) {
            $query['page'] = 1;
        }

        $map = [];

        if ($params['code']) {
            $user_id = UserModel::where('tel',$params['code'])->value('id');
            if (!$user_id) {
                $this->error('用户不存在');
            }
            $map[] = ['user_id','=',$user_id];
        }

        if ($params['status'] === '-1') {
            $map[] = ['status','in',[1,2,3]];
        } else {
            $map[] = ['status','=',$params['status']];
        }

        try {
            $list = OrdersOther::with('user,course')->where($map)->paginate(10,false,$query);
            $this->assign('list',$list);
            $this->assign('params',$params);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        return $this->fetch('goodsOrder');
    }

    /**
     * 订单信息
     * @return \think\response\Json
     */
    public function orderInfo() {
        $params = $this->getParams(['id']);
        $rule = [
            'id' => 'require|integer|>:0'
        ];
        $msg = [
            'id' => '错误的操作'
        ];

        $r = $this->validate($params,$rule,$msg);
        if (true !== $r) {
            return $this->jsonBack(1,$r);
        }

        try {
            $order = OrdersOther::with('user,course')->append(['order_number'])->get($params['id']);
            if (!$order) {
                throw $this->createError('错误的操作');
            }
        } catch (\Exception $e) {
            return $this->jsonBack(2,$e->getMessage());
        }

        return $this->jsonBack(0,'',$order);
    }

    /**
     * 订单发货
     * @return \think\response\Json
     */
    public function sendOrder() {
        $params = $this->getParams(['id','express_name','express_code']);
        $rule = [
            'id' => 'require|integer|>:0',
            'express_name' => 'require|min:1',
            'express_code' => 'require|min:1'
        ];
        $msg = [
            'id' => '错误的操作',
            'express_name' => '快递公司不为空',
            'express_code' => '快递单号不为空'
        ];

        $r = $this->validate($params,$rule,$msg);
        if (true !== $r) {
            return $this->jsonBack(1,$r);
        }

        Db::startTrans();
        try {
            $order = OrdersOther::where('id',$params['id'])->lock(true)->find();
            if (!$order) {
                throw $this->createError('订单不存在');
            }

            if ($order->status !== 1) {
                throw $this->createError('该订单已发货');
            }

            $order->express_name = $params['express_name'];
            $order->express_code = $params['express_code'];
            $order->status = 2;
            $order->save();
            Db::commit();
            return $this->jsonBack(0,'操作成功');
        } catch (\Exception $e) {
            Db::rollback();;
            return $this->jsonBack(2,$e->getMessage());
        }
    }

    /**
     * 商品销量
     * @return mixed|\think\response\Json
     */
    public function goodsNum() {
        if ($this->request->isPost()) {
            $params = $this->getParams(['key']);
            $rule = [
                'key' => 'require|mobile'
            ];
            $msg = [
                'key' => '区代手机号不正确'
            ];

            $r = $this->validate($params,$rule,$msg);
            if (true !== $r) {
                return $this->jsonBack(1,$r);
            }

            try {
                $user = UserModel::where('tel',$params['key'])->find();
                if (!$user || $user->is_ej_qd !== 1) throw $this->createError('用户不存在或不是区代');
                $agent = AgentOther::where('user_id',$user->id)->find();
                if (!$agent) throw $this->createError('系统错误,找不到匹配的区代');

                $now = time();
                $monthStart = strtotime(date('Y-m-1',$now));
                $info = OrdersOther::where('status','>',0)
                    ->where('create_at','between',[$monthStart,$now])
                    ->where('country_id',$agent->country_id)
                    ->field('SUM(`num`) as totalNum,SUM(`total_price`) as totalSell')
                    ->find();

                return $this->jsonBack(0,'',[
                    'id' => $user->id,
                    'tel' => $user->tel,
                    'area' => "{$agent->province} {$agent->city} {$agent->country}",
                    'totalNum' => $info['totalNum'],
                    'totalSell' => $info['totalSell']
                ]);

            } catch (\Exception $e) {
                return $this->jsonBack(2,$e->getMessage());
            }

        } else {
            $now = time();
            $monthStart = strtotime(date('Y-m-1',$now));

            try {
                $info = OrdersOther::where('status','>',0)
                    ->where('create_at','between',[$monthStart,$now])
                    ->field('SUM(`num`) as totalNum,SUM(`total_price`) as totalSell')
                    ->find();
                $this->assign('info',$info);
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }

            return $this->fetch('goodsNum');
        }
    }


}
