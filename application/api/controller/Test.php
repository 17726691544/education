<?php


namespace app\api\controller;

use  app\common\controller\Base;
use  app\api\service\TestService;

class Test extends Base
{

    public function test()
    {
        $params = $this->getParams(['name']);
        $rule = [
            'name' => 'require'
        ];
        $msg = [
            'name.require' => '名字不能为空'

        ];

        $validate = $this->validate($params, $rule, $msg);
        if (true !== $validate) {
            return $this->jsonBack(1, $validate);
        }
    }
}