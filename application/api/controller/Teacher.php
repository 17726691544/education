<?php


namespace app\api\controller;

use app\common\controller\Base;
use app\common\validate\PageV;
use app\common\model\Teacher as TeacherModel;
use app\common\validate\IdV;
use app\common\model\Video;

class Teacher extends Base
{

    /**
     * 获取教师列表
     * @return \think\response\Json
     * @throws \app\common\exception\BusinessBaseException
     */
    public function getTeacherList()
    {
        $params = $this->getParams(['page', 'pageNum']);
        (new PageV())->goChick($params);
        $teacherList = TeacherModel::getTeacherList($params['page'] ?? 1, $params['pageNum'] ?? 5);
        return $this->jsonBack(0, '成功', $teacherList);
    }

    /**
     * 获取教师详情
     * @return \think\response\Json
     * @throws \app\common\exception\BusinessBaseException
     */
    public function getTeacherDetail()
    {
        $params = $this->getParams(['id']);
        (new IdV())->goChick($params);
        //老师介绍详情
        $teacherDetail = TeacherModel::getTeacherDetail($params['id']);
        $teacherDetail = $teacherDetail ? $teacherDetail->toArray() : null;

        if ($teacherDetail) {
            //获取最新的10条视频
            $videoList = Video::getVideoListByNum(10);
            $teacherDetail['videoList'] = $videoList ? $videoList->toArray() : null;
        }
        return $this->jsonBack(0, '成功', $teacherDetail);
    }
}