<?php


namespace app\api\service;

use app\common\exception\BusinessBaseException;
use app\common\model\Agent;
use app\common\model\AttendClassRecord;
use app\common\model\CenterLogs;
use app\common\model\TeachCenter;
use app\common\model\User;

class AreaAdminService extends BaseService
{

    public function getTeachCenterArea($uid)
    {
        //判断是否有权限
        $this->hasPermission($uid);
        return Agent::where('user_id', $uid)->visible(['id', 'province', 'city', 'country'])->find();
    }

    public function applyTeachCenter($uid, $name, $area)
    {
        //判断是否有权限
        $this->hasPermission($uid);
        //获取区域信息
        $agent = Agent::where('user_id', $uid)->find();
        if (!$agent) {
            throw new BusinessBaseException('获取区域信息失败');
        }
        //向数据库写入申请请求
        (new TeachCenter())->save([
            'agent_id' => $agent->id,
            'agent_user_id' => $uid,
            'name' => $name,
            'province_id' => $agent->province_id,
            'city_id' => $agent->city_id,
            'country_id' => $agent->country_id,
            'province' => $agent->province,
            'city' => $agent->city,
            'country' => $agent->country,
            'area' => $area,
            'create_at' => time(),
            'status' => 0

        ]);
        return true;

    }

    public function getEarningList($uid, $page, $pageNum)
    {
        $this->hasPermission($uid);
        //获取当前用户的区域id
        $agent = Agent::where('user_id', $uid)->find();
        if (!$agent) {
            throw new BusinessBaseException('获取区域信息失败');
        }
        //分页查询用户的收益记录
        return CenterLogs::where('agent_user_id', $uid)
            ->where('agent_id', $agent->id)
            ->order('create_at', 'asc')
            ->paginate($pageNum, false, [
                'page' => $page
            ]);
    }

    public function getTeachCenterList($uid, $page, $pageNum)
    {
        $this->hasPermission($uid);
        //获取当前用户的区域id
        $agent = Agent::where('user_id', $uid)->find();
        if (!$agent) {
            throw new BusinessBaseException('获取区域信息失败');
        }
        //分页获取教学中心列表
        return TeachCenter::where('agent_id', $agent->id)
            ->where('agent_user_id', $uid)
            ->where('status', 1)
            ->visible(['id', 'agent_id', 'name', 'province', 'city', 'country', 'area'])
            ->paginate($pageNum, false, [
                'page' => $page
            ]);
    }


    public function getStudentList($uid, $centerId, $page, $pageNum)
    {
        //判断权限
        $this->hasPermission($uid);
        //判断该区代理是否有代理该学习中心
        $teachCenter = TeachCenter::where('id', $centerId)
            ->where('agent_user_id', $uid)
            ->where('status', 1)
            ->find();
        if (!$teachCenter) {
            throw new BusinessBaseException('非法操作');
        }
        //查询该代理中心的学生列表
        return (new AttendClassRecord())->getUserListByCenterId($centerId, 0, $page, $pageNum);
    }

    /**
     * 判断权限
     * @param $uid
     * @return mixed
     * @throws BusinessBaseException
     */
    private function hasPermission($uid)
    {
        $user = User::get($uid);
        if (!$user) {
            throw new BusinessBaseException('错误的操作');
        }
        if ($user->is_qd !== 1) {
            throw new BusinessBaseException('你还没申请为区域代理');
        }
        return $user;
    }


}