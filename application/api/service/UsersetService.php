<?php


namespace app\api\service;

use app\common\exception\BusinessBaseException;
use app\common\model\User;

class UsersetService extends BaseService
{
    public function findPass($params)
    {
        $tel = $params['tel'];
        $codeContent = $params['tel_code'];
        $newpass = $params['newpass'];

        $this->checkCode($tel, $codeContent, 1, 2);
        //查找用户
        $user = User::findByTel($tel);
        if (!$user) {
            throw new BusinessBaseException('用户不存在');
        }
        //重置密码
        return (new User())->editByUId($user->id, ['pass' => md5($user->tel . $newpass)]);
    }
}