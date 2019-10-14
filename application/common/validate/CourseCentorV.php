<?php


namespace app\common\validate;

class CourseCentorV extends BaseValidate
{
    protected $rules = [
        'id' => 'require|integer|>:0',
        'signId' => 'require|integer|>:0',
        'order_id' => 'require|integer|>:0'

    ];

    protected $message = [
        'id' => '错误操作',
        'signId' => '错误操作',
        'order_id' => '错误操作',
    ];
}