<?php


namespace app\api\service;

use app\common\model\User;

class TestService
{
    /**
     * 获取用户信息
     * @param $uid
     * @return mixed
     */
    public function getUserInfo($uid)
    {
        $user = User::get(1);
        dump($user);
        return $user;

    }

    public function save($userInfo = [])
    {
        if (empty($userInfo)) {
            return '无效的保存信息';
        }
        $user = new User();
        foreach ($userInfo as $key => $value) {
            if (!empty(trim($value))) {
                $user->$key = $value;

            }
        }
        return $user->save();
    }

}