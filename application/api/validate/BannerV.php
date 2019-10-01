<?php


namespace app\api\validate;


use think\Validate;

/**
 * banner验证器
 * @package app\api\validate
 */
class BannerV extends BaseValidate
{
    protected  $rules =   [
        'bannerId'  => 'require'
    ];

}