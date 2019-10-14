<?php


namespace app\common\model;


use app\common\enum\CodeCaheEnum;
use think\Model;

class CodeCahe extends Model
{
    protected $table = 'code_cahe';

    /**
     * 获取注册验证码记录
     * @param $phoneNum
     */
    public static function findByWhere($sendTo,$sendType,$codeType)
    {
        return self::where([
            'send_to' => $sendTo,
            'send_type' => $sendType,
            'code_type' =>$codeType
        ])->find();
    }

}