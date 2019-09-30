<?php


namespace app\api\validate;

/**
 * ID验证器:必须为正整数
 * Class IDMustPositiveInt
 * @package app\api\validate
 */
class IDMustPositiveInt extends BaseValidate
{
    protected $rule =[
        'id' => 'require'
    ];
}