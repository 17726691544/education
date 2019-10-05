<?php


namespace app\common\validate;

class IdV extends BaseValidate
{
    protected $rules = [
        'id' => 'require|integer|>:0'

    ];

    protected $message = [
        'id' => '错误操作'
    ];
}