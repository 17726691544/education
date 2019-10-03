<?php


namespace app\common\validate;

class HomeV extends BaseValidate
{
    protected $rules = [
        'num' => 'integer|>:0|<=:20',
        'id' => 'require|integer|>:0'

    ];

    protected $message = [
        'num' => '请输入正确的获取数量',
        'id' => 'id错误'
    ];
}