<?php

namespace app\api\controller;

use app\common\controller\Base;
use app\common\model\Orders;
use app\common\validate\BaseValidate;
use app\common\model\Course as CourseModel;
use app\common\model\TeachCenter as TeachCenterModel;

class Order extends Base
{
    /**
     * 创建课程购买订单
     * @return \think\response\Json
     * @throws \app\common\exception\BusinessBaseException
     */
        public function create() {
        (new BaseValidate())->tokenChick();
        $uid = $this->getUid();
        $params = $this->getParams(['name','id_card','course_id','center_id','grade']);
        $rule = [
            'name' => 'require|min:1',
            'id_card' => 'require|idCard',
            'course_id' => 'require|integer|>:0',
            'center_id' => 'require|integer|>:0',
            'grade' => 'require|min:1'
        ];
        $msg = [
            'name' => '学生姓名不为空',
            'id_card' => '身份证号不正确',
            'course_id' => '操作错误',
            'center_id' => '操作错误',
            'grade' => '操作错误'
        ];

        $r = $this->validate($params,$rule,$msg);
        if (true !== $r) {
            return $this->jsonBack(1,$r);
        }

        try {
            $course = CourseModel::where('id',$params['course_id'])
                ->where('status',0)
                ->find();
            if (!$course) throw $this->createError('课程不存在或已经删除');

            $center = TeachCenterModel::where('id',$params['center_id'])
                ->where('status',1)
                ->find();
            if (!$center) throw $this->createError('教学中心不存在或已经删除');
            if (!in_array($params['grade'],$course->grades_arr)) throw $this->createError('不支持的年级');

            Orders::create([
                'user_id' => $uid,
                'course_id' => $params['course_id'],
                'center_id' => $params['center_id'],
                'name' => $params['name'],
                'id_card' => $params['id_card'],
                'grade' => $params['grade'],
                'price' => $course->price,
                'create_at' => time()
            ]);

            return $this->jsonBack(0,'创建成功');
        } catch (\Exception $e) {
            return $this->jsonBack(2,$e->getMessage());
        }
    }
}