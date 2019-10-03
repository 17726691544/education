<?php
namespace app\man\controller;

use app\common\controller\Base;
use app\man\model\Admin;

class Normal extends Base
{
    /**
     * 登陆
     * @return \think\response\Json
     */
    public function login() {
        if ($this->request->isPost()) {
            $params = $this->getParams(['account','pwd']);
            $rule = [
                'account' => 'require|min:1',
                'pwd' => 'require|min:1'
            ];
            $msg = [
                'account' => '账号不能为空',
                'pwd' => '密码不能为空'
            ];
            $r = $this->validate($params,$rule,$msg);
            if (true !== $r) {
                $this->error($r);
            }

            try {
                $admin = Admin::where('account',$params['account'])->find();
                if (!$admin || !Admin::checkPwd($admin,$params['pwd'])) {
                    throw new \Exception('账号或密码错误');
                }
                session('auth', ['id' => $admin->id, 'account' => $admin->account,'is_su'=>$admin->is_su]);
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
            $this->redirect('index/index');
        } else {
            return $this->fetch('login');
        }
    }
}
