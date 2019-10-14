<?php


namespace app\common\validate;

class PageV extends BaseValidate
{
    protected $rules = [
        'page' => 'integer|>:0',
        'pageNum' => 'integer|>:0|<=:20'

    ];

    protected $message = [
        'page' => '请正确输入的页码',
        'pageNum' => '请正确输入每页获取的数量'
    ];
}