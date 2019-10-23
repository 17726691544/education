<?php


namespace app\api\service;


use app\common\exception\BusinessBaseException;
use app\common\model\AttendClassRecord;
use app\common\model\Teacher;
use app\common\model\User;
use app\common\model\UserBalance;
use app\common\model\UserLogs;
use think\Db;

class TeacherAdminService
{
    private function hasTeacher($uid)
    {
        $user = Teacher::where('user_id', $uid)->find();
        if (!$user) {
            throw new BusinessBaseException('错误的操作');
        }
        return $user;
    }

    public function getTeachCenterList($uid, $page, $pageNum)
    {
        //判断是否有权限
        $this->hasPermission($uid);
        return (new Teacher())->getTeachCenterList($uid, $page, $pageNum);
    }

    public function getStudentList($uid, $teachCenterId, $page, $pageNum)
    {
        //判断是否有权限
        $this->hasPermission($uid);
        //查找老师
        $teacher = $this->hasTeacher($uid);
        //判断该老师是否属于该教学中心
        $teacherCenter = Db::table('teacher_center')
            ->where('teacher_id', $teacher->id)//
            ->where('center_id', $teachCenterId)//
            ->find();
        if (!$teacherCenter) {
            throw new BusinessBaseException('错误的操作');
        }
        return (new AttendClassRecord())->getUserListByCenterId($teachCenterId, $page, $pageNum);
    }

    public function confirm($uid, $attendClassId, $teachCenterId, $status)
    {
        $this->hasPermission($uid);
        //查找老师
        $teacher = $this->hasTeacher($uid);
        //判断该老师是否属于该教学中心
        $teacherCenter = Db::table('teacher_center')
            ->where('teacher_id', $teacher->id)//
            ->where('center_id', $teachCenterId)//
            ->find();
        if (!$teacherCenter) {
            throw new BusinessBaseException('错误的操作');
        }

        //获取确认信息
        $attendClassRecord = AttendClassRecord::where('id', $attendClassId)->lock(true)->find();
        if (!$attendClassRecord) {
            throw new BusinessBaseException('获取确认信息失败');
        }

        //修改订单状态
        Db::startTrans();
        try {
            $orderStatus = $attendClassRecord->status;
            if ($orderStatus === 0) {
                throw new BusinessBaseException('非法操作');
            } elseif ($orderStatus === 1) {
                $result = (new AttendClassRecord())->save([
                    'status' => $status
                ], ['id' => $attendClassId, 'status' => 1]);
                if (!$result) {
                    throw new BusinessBaseException('确认失败');
                }
                //解冻资金
                $this->unfreezeBalance($attendClassId);
            } else {
                throw new BusinessBaseException('请勿重复操作');
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            throw $e;
        }
        return true;
    }

    /**
     * 解冻资金
     * @param $uid
     * @param $courseId
     */
    private function unfreezeBalance($attendClassId)
    {
        //获取冻结执行信息
        $userBalanceList = UserBalance
            ::field(['id', 'user_id','sum(lock_balance)'=>'totalLockBalance'])
            ->where('attend_id', $attendClassId)
            ->where('status', 0)
            ->group(['id', 'user_id'])
            ->select();
        if (!$userBalanceList) {
            return;
        }
        foreach ($userBalanceList as $item) {
            //减少锁定余额 增加可用余额
            $changBalance = $item->totalLockBalance;
            $decResult = Db::name('user')
                ->where('id', $item->user_id)
                ->where('lock_balance', '>=', $changBalance)
                ->dec('lock_balance', $changBalance)
                ->inc('balance', $changBalance)
                ->update();
            if ($decResult !== 1) {
                throw new BusinessBaseException('解冻资金失败');
            }
            //更改冻结信息状态
            $ee = $item->id;
            $update = (new UserBalance())->save(['status' => 1], ['id' => $item->id]);
            if (!$update) {
                throw new BusinessBaseException('解冻资金失败');
            }
        }

        //写入解锁资金流水记录
//        (new UserLogs())->save([
//            'user_id'=>$userId,
//            'num'=>$changBalance,
//            'tip'=>'账户锁定资金解冻',
//            'type'=>8,
//            'create_at'=>time()
//        ]);
    }

    private function hasPermission($uid)
    {
        $user = User::get($uid);
        if (!$user) {
            throw new BusinessBaseException('错误的操作');
        }
        if ($user->is_teacher !== 1) {
            throw new BusinessBaseException('你还不是教师！！');
        }
        return $user;
    }

}