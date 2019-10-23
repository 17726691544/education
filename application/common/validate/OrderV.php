<?php


namespace app\common\validate;

class OrderV extends BaseValidate
{
    protected $rules = [
        'course_id' => 'require|integer|>:0',
        'name' => 'require|max:20',
        'tel' => 'require|mobile',
        'num' => 'require|number|>:0',
        'address' => 'require|max:255',
        'province_id' => 'require|integer|>:0',
        'city_id' => 'require|integer|>:0',
        'country_id' => 'require|integer|>:0',
        'dl_province_id' => 'integer|>:0',
        'dl_city_id' => 'integer|>:0',
        'dl_country_id' => 'integer|>:0'

    ];

    protected $message = [
        'course_id' => '错误操作',
        'name' => '请填写收货人名字',
        'tel' => '请正确填写收货人联系电话',
        'num' => '请正确填写购买数量',
        'address.require' => '收货地址不能为空',
        'address.max' => '收货地址太长了',
        'province_id' => '错误操作',
        'city_id' => '错误操作',
        'country_id' => '错误操作',
        'dl_province_id' => '错误操作',
        'dl_city_id' => '错误操作',
        'dl_country_id' => '错误操作'
    ];
}