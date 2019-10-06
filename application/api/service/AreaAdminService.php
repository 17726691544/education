<?php


namespace app\api\service;

use app\common\exception\BusinessBaseException;
use app\common\model\Agent;
use app\common\model\User;

class AreaAdminService extends BaseService
{

    public function getTeachCenterArea($uid)
    {
        $user = $this->hasPermission($uid);

        return Agent::where('user_id',$uid)->find();
    }


    protected function hasPermission($uid)
    {
        $user = User::get($uid);
        if(!$user){
            throw new BusinessBaseException('错误的操作');
        }
        if($user->is_qd !== 1){
            throw new BusinessBaseException('你没有权限做此操作');
        }
        return $user;
    }


}