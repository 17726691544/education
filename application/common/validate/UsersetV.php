<?php


namespace app\common\validate;

class UsersetV extends BaseValidate
{
    protected $rules = [
        'tel' => 'require|mobile',
        'tel_code'=> 'require|max:6',
        'nick' => 'require|max:20',
        'pass' => 'require|max:32',
        'safe_pass' => 'require|max:32',
        'newpass' => 'require|max:32',
        'renewpass' => 'require|confirm:newpass',

    ];

    protected $message = [
        'tel' => '请输入正确的手机号',
        'tel_code' => '请输入正确的验证码',
        'nick.require' => '请填写昵称',
        'nick.max' => '昵称不能超过20个字',
        'pass.require' => '请输入登录密码',
        'pass.max' => '登录密码最大长度32',
        'safe_pass.require' => '请输入安全密码',
        'safe_pass.max' => '安全密码最大长度32',
        'newpass.require' => '请输入新密码',
        'newpass.max' => '新密码最大长度32',
        'renewpass.require' => '请输入确认密码',
        'renewpass.confirm' => '两次新密码不一致',
    ];
}