<?php


namespace app\common\validate;

class TeacherAdminV extends BaseValidate
{
    protected $rules = [
        'id' => 'require|integer|>:0',
        'status' => 'require|in:2,3',
        'center_id' => 'require|integer|>:0',
        'course_id' => 'require|integer|>:0'
    ];

    protected $message = [
        'id' => '错误操作',
        'status' => '错误操作',
        'center_id' => '错误操作',
        'course_id' => '错误操作'
    ];
}