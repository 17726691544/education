<?php


namespace app\api\service;


use app\common\enum\CodeCaheEnum;
use app\common\exception\BusinessBaseException;
use app\common\model\CodeCahe;

class BaseService
{

    /**
     * 判断验证码有效性
     * @param $tel
     * @throws BusinessBaseException
     */
    protected function checkCode($sendTo, $codeContent, $sendType, $codeType)
    {
        $codeCahe = CodeCahe::findByWhere($sendTo, $sendType, $codeType);
        if (!$codeCahe) {
            throw new BusinessBaseException('验证码错误');
        }
        $now = time();
        $c = $codeCahe->expire_time;
        if ($now > $codeCahe->expire_time) {
            throw new BusinessBaseException('验证码已过期');
        }
        if ($codeContent !== $codeCahe->code_content) {
            throw new BusinessBaseException('验证码错误');
        }

    }

}