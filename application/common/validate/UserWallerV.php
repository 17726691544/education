<?php


namespace app\common\validate;

class UserWallerV extends BaseValidate
{
    protected $rules = [
        'bankCard_id' => 'require|integer|>:0',
        'moneyNum' => 'require|float|>:0'

    ];

    protected $message = [
        'bankCard_id' => '错误操作',
        'moneyNum'=>'请输入正确的提现金额'
    ];
}