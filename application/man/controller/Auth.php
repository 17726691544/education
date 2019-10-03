<?php

namespace app\man\controller;
use app\common\controller\Base;
use app\man\model\Access;
use app\man\model\Admin;
use app\man\model\AdminAccess;

class Auth extends Base
{
    protected $middleware = ['app\man\middleware\Auth'];
    /**
     * 管理员列表
     */
    public function user() {
        try {
            $list = Admin::where('is_su',0)->paginate(10);
            $this->assign('list',$list);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        return $this->fetch('user');
    }

    /**
     * 添加管理员
     * @return mixed
     */
    public function addUser() {
        if ($this->request->isPost()) {
            $params = $this->getParams(['account','pwd','re_pwd']);
            $rule = [
                'account' => 'require|alphaNum|length:6,20',
                'pwd' => 'require|alphaDash|length:6,20',
                're_pwd' => 'require|confirm:pwd'
            ];
            $msg = [
                'account' => '账号由6-20位字母数字组成',
                'pwd' => '密码由6-20位字母、数字、下划线和-组成',
                're_pwd' => '两次密码不一致'
            ];

            $r = $this->validate($params,$rule,$msg);
            if (true !== $r) {
                $this->error($r);
            }

            $now = time();
            Admin::create([
                'account' => $params['account'],
                'pwd' => md5( $now . $params['pwd'] ),
                'reg_at' => $now
            ]);

            $this->success('添加成功');

        } else {
            return $this->fetch('addUser');
        }
    }

    /**
     * 删除管理员
     */
    public function delUser() {
        $params = $this->getParams(['id']);
        $rule = [
            'id' => 'require|integer|>:1'
        ];
        $msg = [
            'id' => '错误的操作'
        ];

        $r = $this->validate($params,$rule,$msg);
        if (true !== $r) {
            $this->error($r);
        }
        try {
            Admin::destroy($params['id']);
            AdminAccess::where('admin_id',$params['id'])->delete();
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }

        $this->success('操作成功');

    }

    /**
     * 权限列表
     */
    public function access() {
        try {
            $list = (new Access)->paginate(10);
            $this->assign('list',$list);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        return $this->fetch('access');
    }

    /**
     * 添加权限
     */
    public function addAccess() {
        if ($this->request->isPost()) {
            $params = $this->getParams(['name','action']);
            $rule = [
                'name' => 'require|min:1',
                'action' => 'require|min:1'
            ];
            $msg = [
                'name' => '名称不能为空',
                'action' => '操作不能为空'
            ];

            $r = $this->validate($params,$rule,$msg);
            if (true !== $r) {
                $this->error($r);
            }

            Access::create([
                'name' => $params['name'],
                'action' => strtolower($params['action']),
                'create_at' => time()
            ]);

            $this->success('添加成功');

        } else {
            return $this->fetch('addAccess');
        }
    }



    /**
     * 删除权限
     */
    public function delAccess() {
        $params = $this->getParams(['id']);
        $rule = [
            'id' => 'require|integer|>:0'
        ];
        $msg = [
            'id' => '错误的操作'
        ];

        $r = $this->validate($params,$rule,$msg);
        if (true !== $r) {
            $this->error($r);
        }
        try {
            Access::destroy($params['id']);
            AdminAccess::where('access_id',$params['id'])->delete();
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }

        $this->success('操作成功');
    }


    /**
     * 分配权限
     */
    public function assignAccess() {
        if ($this->request->isPost()) {
            $params = $this->getParams(['man_id','ids']);
            $rule = [
                'man_id' => 'require|integer|>:1'
            ];
            $msg = [
                'man_id' => '错误的操作'
            ];

            $r = $this->validate($params,$rule,$msg);
            if (true !== $r) {
                $this->error($r);
            }

            try {

                AdminAccess::where('admin_id',$params['man_id'])->delete();
                if ($params['ids'] !== '') {
                    $ids = explode(',',$params['ids']);
                    $data = [];
                    foreach ($ids as $id) {
                        if ($id) {
                            $data[] = [
                                'admin_id' => $params['man_id'],
                                'access_id' => $id
                            ];
                        }
                    }

                    if (!empty($data)) {
                        (new AdminAccess())->saveAll($data);
                    }
                }
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }

            $this->success('操作成功');

        } else {

            $params = $this->getParams(['id']);
            $rule = [
                'id' => 'require|integer|>:1'
            ];
            $msg = [
                'id' => '错误的操作'
            ];

            $r = $this->validate($params,$rule,$msg);
            if (true !== $r) {
                $this->error($r);
            }

            try {
                $access = Access::all();
                $access = $access->toArray();
                $had = AdminAccess::field('access_id')->where('admin_id',$params['id'])->select();
                $had = array_column($had->toArray(),'access_id');
                foreach ($access as $key=>$item) {
                    $access[$key]['had'] = false;
                    if (in_array($item['id'],$had)) {
                        $access[$key]['had'] = true;
                    }
                }

                $admin = Admin::where('id',$params['id'])->find();
                $this->assign('admin',$admin);
                $this->assign('list',json_encode($access));
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
            return $this->fetch('assignAccess');
        }
    }

}
