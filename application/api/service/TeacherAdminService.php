<?php


namespace app\api\service;


use app\common\exception\BusinessBaseException;
use app\common\model\AttendClassRecord;
use app\common\model\Teacher;
use think\Db;

class TeacherAdminService extends BaseService
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

    public function confirm($uid, $attendClassId, $status)
    {
        $this->hasPermission($uid);
        //查找老师
        $teacher = $this->hasTeacher($uid);

        $attendClassRecord = AttendClassRecord::where('id', $attendClassId)->where('teacher_id', $teacher->id)->find();
        //判断该订单是否是属于该老师
        if (!$attendClassRecord) {
            throw new BusinessBaseException('错误操作');
        }
        //判断订单状态
        $orderStatus = $attendClassRecord->status;
        if ($orderStatus !== $status) {
            (new AttendClassRecord())->save([
                'status' => $status
            ], ['id' => $attendClassId]);
        }
        return true;
    }

}