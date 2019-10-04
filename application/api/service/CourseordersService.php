<?php


namespace app\api\service;


use app\common\exception\BusinessBaseException;
use app\common\model\Course;
use app\common\model\Orders;
use app\common\model\UserCourseSign;

class CourseordersService
{
    public function getCourseCentorDetail($uid, $courseId)
    {
        //1:判断用户是否具有该课程
        $order = Orders::where('user_id', $uid)->where('course_id', $courseId)->select();
        if (!$order) {
            throw new BusinessBaseException('非法操作');
        }
        //2:获取课程详情
        $courseCentorDetail = (new Course())->getCourseCentorDetail($courseId);
        if (!$courseCentorDetail) {
            return null;
        }
        $courseCentorDetail = $courseCentorDetail->toArray();

        //3:获取观看进度
        $userCourseSin = UserCourseSign::where('user_id', $uid)->where('course_id', $courseId)->visible(['sign'])->find();
        if ($userCourseSin) {
            $userCourseSin = $userCourseSin->toArray();
            $courseCentorDetail['sign'] = $userCourseSin['sign'];
        }
        return $courseCentorDetail;
    }
}