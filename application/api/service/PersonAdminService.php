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

    protected function hasPermission($uid)
    {
        $user = User::get($uid);
        if(!$user){
            throw new BusinessBaseException('错误操作');
        }
        if($user->is_gd !== 1){
            throw new BusinessBaseException('你没有权限做此操作');
        }
        return $user;
    }

}