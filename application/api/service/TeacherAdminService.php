<?php


namespace app\api\service;


use app\common\exception\BusinessBaseException;
use app\common\model\AttendClassRecord;
use app\common\model\Teacher;
use think\Db;

class TeacherAdminService
{
    private function hasTeacher($uid)
    {
        $user = Teacher::where('user_id',$uid)->find();
        if(!$user){
            throw new BusinessBaseException('错误的操作');
        }
        return $user;
    }

    public function getTeachCenterList($uid, $page, $pageNum)
    {
        //判断当前用户是否是教师
        $this->hasTeacher($uid);
        return (new Teacher())->getTeachCenterList($uid, $page, $pageNum);
    }

    public function getStudentList($uid, $teachCenterId)
    {
        //判断该用户是否属于老师
        $teacher = $this->hasTeacher($uid);

        //判断该老师是否属于该教学中心
        $teacherCenter = Db::table('teacher_center')
                ->where('teacher_id', $teacher->id)//
                ->where('center_id', $teachCenterId)//
                ->find();
        if(!$teacherCenter){
            throw new BusinessBaseException('错误的操作');
        }
        return (new AttendClassRecord())->getUserListByCenterId($teachCenterId);
    }
}