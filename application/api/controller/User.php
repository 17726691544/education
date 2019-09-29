<?php

namespace app\api\controller;

use app\common\controller\Base;

class User extends Base
{

    public function register()
    {
//        $params = $this->getParams(['tel', 'reg_code', 'pass', 'safe_pass', 'invite_code']);
//
//        $rule = [
//            'tel' => 'require|max:11|regex:/^1[3-8]{1}[0-9]{9}$/',
//            'reg_code' => 'require|max:6',
//            'pass' => 'require|max:20',
//            'safe_pass' => 'require|max:20',
//            'invite_code' => 'max:10',
//        ];
//        $msg = [
//            'tel.require' => '手机号不能为空',
//            'tel.max' => '手机号不能超过11位',
//            'tel.regex' => '无效的手机号',
//            'reg_code.require' =>'手机验证码不能为空',
//            'reg_code.max' =>'无效的手机验证码',
//            'pass.require' =>'登录密码不能为空',
//            'pass.max' =>'无效的登录密码',
//            'safe_pass.require' =>'安全密码不能为空',
//            'safe_pass.max' =>'无效的安全密码'
//        ];
//
//        $validate = $this->validate($params, $rule, $msg);
//        if (true !== $validate) {
//            return $this->jsonBack(1,$validate);
//        }
    }
}