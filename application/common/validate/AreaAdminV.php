<?php


namespace app\common\validate;


class AreaAdminV extends BaseValidate
{
    protected $rules = [
        'name'=>'require|max:50|min:1',
        'province_id' => 'require|integer|>:0',
        'city_id' => 'require|integer|>:0',
        'country_id' => 'require|integer|>:0',
        'area' => 'require|max:255|min:1'
    ];

    protected $message = [
        'name'=>'请正确填写名字长度在[1-50]',
        'province_id' => '错误操作',
        'city_id' => '错误操作',
        'country_id' => '错误操作',
        'area.require' => '请填写详细地址',
        'area.max' => '详细地址已超过最大限制长度',
        'area.min' => '详细地址太短',
    ];
}