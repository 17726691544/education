<?php

namespace app\man\controller;
use app\common\controller\Base;
use app\man\model\TeachCenter as TeachCenterModel;
use app\man\model\User as UserModel;
use think\facade\Validate;

class TeachCenter extends Base
{
    protected $middleware = ['app\man\middleware\Auth'];

    /**
     * 教学中心列表
     * @return mixed|string
     */
    public function index() {
        $params = $this->getParams(['key','status']);
        $map = [];

        $query = ['query' => $params];
        if ($this->request->isPost()) {
            $query['page'] = 1;
        }

        if ($params['key']) {
            if (Validate::is($params['key'],'mobile')) {
                $user_id = UserModel::where('tel',$params['key'])->value('id');
                if ($user_id) {
                    $map[] = ['agent_user_id','=',$user_id];
                } else {
                    $this->error('用户不存在');
                }
            } else {
                $user_id = UserModel::where('invite_code',$params['key'])->value('id');
                if ($user_id) {
                    $map[] = ['agent_user_id','=',$user_id];
                } else {
                    $this->error('用户不存在');
                }
            }
        }

        if (in_array($params['status'],[0,1,2])) {
            $map[] = ['status','=',$params['status']];
        } else {
            $map[] = ['status','in',[0,1,2]];
        }

        try {
            $list = TeachCenterModel::where($map)->order('id desc')->paginate(10,false,$query);
            $this->assign('params',$params);
            $this->assign('list',$list);
            return $this->fetch('index');
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 教学中心处理
     */
    public function deal() {
        $params = $this->getParams(['id','status']);
        $rule = [
            'id' => 'require|integer|>:0',
            'status' => 'require|integer|in:1,2,3'
        ];
        $msg = [
            'id' => '错误的操作',
            'status' => '错误的操作'
        ];
        $r = $this->validate($params,$rule,$msg);
        if (true !== $r) {
            $this->error($r);
        }

        TeachCenterModel::update(['status' => $params['status']], ['id' => $params['id']]);
        $this->success('操作成功');
    }
}
