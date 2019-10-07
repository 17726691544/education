<?php


namespace app\common\validate;


class AreaAdminV extends BaseValidate
{
    protected $rules = [
        'name'=>'require|max:50|min:1',
        'area' => 'require|max:255|min:1',
        'center_id' => 'require|integer|>:0',
        'ids' => 'require|array|min:1',
        'status' => 'require|in:1'

    ];

    protected $message = [
        'name'=>'请正确填写名字长度在[1-50]',
        'area.require' => '请填写详细地址',
        'area.max' => '详细地址已超过最大限制长度',
        'area.min' => '详细地址太短',
        'center_id' => '错误操作',
        'ids' => '错误操作',
        'status' => '错误操作'
    ];
}