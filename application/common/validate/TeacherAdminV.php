<?php


namespace app\common\validate;

class TeacherAdminV extends BaseValidate
{
    protected $rules = [
        'id' => 'require|integer|>:0',
        'status' => 'require|in:2,3'

    ];

    protected $message = [
        'id' => '错误操作',
        'status' => '错误操作'
    ];
}