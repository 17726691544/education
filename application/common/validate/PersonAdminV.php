<?php


namespace app\common\validate;


class PersonAdminV extends BaseValidate
{
    protected $rules = [
        'invite_code' => 'require|max:10',
        'id' => 'require|integer|>:0',
        'num'=> 'require|integer|>:0'

    ];

    protected $message = [
        'invite_code' => '请填写正确的编号',
        'id' => '错误操作',
        'num' => '请输入正确的转出数量'
    ];
}