<?php


namespace app\common\validate;


class BannerV extends BaseValidate
{
    protected $rules = [
        'num' => 'integer|>:0|<=:20'

    ];

    protected $message = [
        'num' => '请输入正确的获取数量'
    ];
}