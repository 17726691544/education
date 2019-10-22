<?php


namespace app\common\validate;

class CourseV extends BaseValidate
{
    protected $rules = [
        'province_id' => 'require|integer|>:0',
        'city_id' => 'require|integer|>:0',
        'country_id' => 'require|integer|>:0'

    ];

    protected $message = [
        'province_id' => '错误操作',
        'city_id' => '错误操作',
        'country_id' => '错误操作'
    ];
}