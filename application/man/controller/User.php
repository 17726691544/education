<?php

namespace app\man\controller;
use app\common\controller\Base;
use app\man\model\User as UserModel;
use app\man\model\UserLogs;
use app\man\model\WithdrawLogs;

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
            $user_id = UserModel::where('invite_code',$params['code'])->value('id');
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
            $user_id = UserModel::where('invite_code',$params['code'])->value('id');
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
        WithdrawLogs::update(['status'=>$params['status']],['id'=>$params['id']]);
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
            $user_id = UserModel::where('invite_code',$params['code'])->value('id');
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
}
