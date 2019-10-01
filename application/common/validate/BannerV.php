<?php


namespace app\common\validate;


use think\Validate;

/**
 * banner验证器
 * @package app\api\validate
 */
class BannerV extends BaseValidate
{
    protected $rules = [
        'bannerId' => 'require',
        'uId' => 'require'
    ];

    protected $message = [
        'bannerId.require' => '轮播广告不能为空',
        'uId.require' => '用户id不能为空'
    ];

}