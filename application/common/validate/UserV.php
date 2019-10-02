<?php


namespace app\common\validate;

class UserV extends BaseValidate
{
    protected $rules = [
        'tel' => 'require|mobile',
        'u_type'=> 'require|integer',
        'tel_code'=> 'require|max:6',
        'pass' => 'require|max:32',
        'safe_pass' => 'require|max:32'
    ];

    protected $message = [
        'tel' => '请输入正确的手机号',
        'u_type' => '请选择正确的注册类型',
        'tel_code' => '请输入正确的验证码',
        'pass.require' => '请输入登录密',
        'pass.max' => '登录密码最大长度32',
        'safe_pass' => '请输入安全密码',
        'safe_pass' => '安全密码最大长度32'
    ];
}