<?php

namespace app\man\controller;
use app\common\controller\Base;
use app\man\model\User as UserModel;

class User extends Base
{
    protected $middleware = ['app\man\middleware\Auth'];

    public function index() {
        try {
            $list = UserModel::order('id desc')->paginate(10);
            $this->assign('list',$list);
            return $this->fetch('index');
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
