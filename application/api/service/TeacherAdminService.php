<?php


namespace app\api\service;


use app\common\exception\BusinessBaseException;
use app\common\model\AttendClassRecord;
use app\common\model\Teacher;
use app\common\model\User;
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
        $attendClassRecord = AttendClassRecord::where('id', $attendClassId)->find();
        if (!$attendClassRecord) {
            throw new BusinessBaseException('获取确认信息失败');
        }
        //修改订单状态
        $orderStatus = $attendClassRecord->status;
        if ($orderStatus === 0) {
            throw new BusinessBaseException('非法操作');
        } elseif ($orderStatus === 1) {
            (new AttendClassRecord())->save([
                'status' => $status
            ], ['id' => $attendClassId]);

        } else {
            throw new BusinessBaseException('请勿重复操作');
        }
        return true;
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