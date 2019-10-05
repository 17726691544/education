<?php


namespace app\common\validate;


class PersonAdminV extends BaseValidate
{
    protected $rules = [
        'invite_code' => 'require|max:10'

    ];

    protected $message = [
        'invite_code' => '请填写正确的编号'
    ];
}