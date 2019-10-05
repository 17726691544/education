<?php


namespace app\api\service;


use app\common\exception\BusinessBaseException;
use app\common\model\User;

class PersonAdminService extends BaseService
{

    public function getDirectPersonList($uid, $page, $pageNum)
    {
        //判断是否有权限
        $this->hasPermission($uid);
        return (new User())->getDirectPersonList($uid, $page, $pageNum);
    }

    public function getVipUserList($uid, $page, $pageNum)
    {
        //判断是否有权限
        $this->hasPermission($uid);
        return (new User())->getVipUserList($uid, $page, $pageNum);
    }

    public function getDirectDetailList($uid, $personUserId, $page, $pageNum)
    {
        //判断是否有权限
        $this->hasPermission($uid);

        //1:判断这个用户是否与属于操作用户
        $user = User::get($personUserId);
        if ($user->parent_id !== $uid) {
            throw new BusinessBaseException('错误操作');
        }
        return (new User())->getVipUserList($user->id, $page, $pageNum);
    }

    public function getQuota($uid)
    {
        //判断是否有权限
        $this->hasPermission($uid);
        $user = User::get($uid);
        if (!$user) {
            throw  new BusinessBaseException('用户不存在');
        }
        return $user;
    }
}