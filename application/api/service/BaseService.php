<?php


namespace app\api\service;


use app\common\enum\CodeCaheEnum;
use app\common\exception\BusinessBaseException;
use app\common\model\CodeCahe;
use app\common\model\User;

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

    /**
     * 是否有操作权限
     * @param $uid
     */
    protected function hasPermission($uid)
    {
        $user = User::get($uid);
        $permissionValue = $user->is_qd + $user->is_gd + $user->is_teacher;
        if((int)$permissionValue < 1){
            throw new BusinessBaseException('你没有权限做此操作');
        }
        return $user;

    }
}