<?php


namespace app\api\service;


use app\common\exception\BusinessBaseException;
use app\common\model\Course;
use app\common\model\CourseItem;
use app\common\model\Orders;
use app\common\model\UserCourseSign;

class CourseCentorService
{
    /**
     * 判断用户时候已经购买该课程
     * @param $uid
     * @param $courseId
     * @throws BusinessBaseException
     */
    private function hasCourse($uid, $courseId)
    {
        $order = Orders::where('user_id', $uid)
            ->where('course_id', $courseId)//
            ->where('status', 1)//
            ->select();
        if (!$order) {
            throw new BusinessBaseException('非法操作');
        }
    }

    public function getCourseCentorDetail($uid, $courseId)
    {
        //1:判断用户是否具有该课程
        $this->hasCourse($uid, $courseId);
        //2:获取课程详情
        $courseCentorDetail = (new Course())->getCourseCentorDetail($courseId);
        if (!$courseCentorDetail) {
            return null;
        }
        $courseCentorDetail = $courseCentorDetail->toArray();
        $courseCentorDetail['totalItem'] = count($courseCentorDetail['course_items']);

        //3:获取观看进度
        $userCourseSin = UserCourseSign::where('user_id', $uid)
            ->field(['sign'])//
            ->where('course_id', $courseId)//
            ->find();
        if ($userCourseSin) {
            $userCourseSin = $userCourseSin->toArray();
            $courseCentorDetail['sign'] = $userCourseSin['sign'];
            $sign = 0;
            if (!empty($userCourseSin['sign'])) {
                $sign = count(json_decode($userCourseSin['sign']));
            }
            $courseCentorDetail['studyItem'] = $sign;
        }
        return $courseCentorDetail;
    }


    public function sinCourse($uid, $courseId, $signId)
    {
        //1:判断用户是否具有该课程
        $this->hasCourse($uid, $courseId);
        //2:判断该打卡小节是否是正确的
        $courseItem = CourseItem::where('id', $signId)->where('course_id', $courseId)->find();
        if (!$courseItem) {
            throw  new BusinessBaseException('错误操作');
        }
        $userCourseSign = UserCourseSign::where('user_id', $uid)->where('course_id', $courseId)->find();
        if ($userCourseSign) {
            $sign = $userCourseSign['sign'];
            if (!empty($sign)) {
                $newSign = json_decode($sign);
                if (in_array($signId, $newSign)) {
                    throw new BusinessBaseException('已经打过卡了');
                }
                $newSign[] = $signId;
            } else {
                $newSign = [$signId];
            }
            $userCourseSign->save([
                'sign' => json_encode($newSign)
            ], ['id' => $userCourseSign->id]);
        } else {
            (new UserCourseSign())->save([
                'user_id' => $uid,
                'course_id' => $courseId,
                'sign' => $signId
            ]);
        }
        return true;
    }
}