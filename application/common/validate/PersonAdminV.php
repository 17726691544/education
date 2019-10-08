<?php


namespace app\common\validate;


class PersonAdminV extends BaseValidate
{
    protected $rules = [
        'invite_code' => 'require|max:10',
        'id' => 'require|integer|>:0',
        'num'=> 'require|integer|>:0',
        'safe_pass' => 'require|max:32'

    ];

    protected $message = [
        'invite_code' => '请填写正确的编号',
        'id' => '错误操作',
        'num' => '请输入正确的转出数量',
        'safe_pass.require' => '请输入安全密码',
        'safe_pass.max' => '安全密码最大长度32'
    ];
}