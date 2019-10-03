<?php

namespace app\man\controller;
use app\common\controller\Base;
use app\man\model\Course as CourseModel;
use app\man\model\CourseItem;
use app\man\model\User as UserModel;
use app\man\model\Video as VideoModel;
use app\man\model\TeachCenter as TeachCenterModel;
use think\Db;
use think\facade\Validate;

class Teacher extends Base
{
    protected $middleware = ['app\man\middleware\Auth'];

    /**
     * 课程列表
     * @return mixed|string
     */
    public function index() {
        try {
            $list = CourseModel::order('id desc')->paginate(10);
            $this->assign('list',$list);
            return $this->fetch('index');
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 添加课程
     * @return mixed|\think\response\Json
     */
    public function add() {
        if ($this->request->isPost()) {
            $params = $this->getParams(['title','cover','price','tip', 'qydl','qdtjr','jxzx','grdl','gdtjr','items','grades']);
            $rule = [
                'title' => 'require|length:1,50',
                'cover' => 'require|min:1',
                'price' => 'require|float|>:0.01',
                'tip' => 'require|length:1,255',
                'qydl' => 'require|float|>=:0',
                'qdtjr' => 'require|float|>=:0',
                'jxzx' => 'require|float|>=:0',
                'grdl' => 'require|float|>=:0',
                'gdtjr' => 'require|float|>=:0'
            ];
            $msg = [
                'title' => '课程标题1-50个字符',
                'cover' => '请上传课程封面图',
                'price' => '课程价格不小于0.01',
                'tip' => '课程简介1-255个字符',
                'qydl' => '区代奖励不小于0',
                'qdtjr' => '区代推荐人奖励不小于0',
                'jxzx' => '教学中心奖励不小于0',
                'grdl' => '个代奖励不小于0',
                'gdtjr' => '个代推荐人奖励不小于0'
            ];
            $r = $this->validate($params,$rule,$msg);
            if (true !== $r) {
                return $this->jsonBack(1,$r);
            }

            $items = json_decode($params['items'],true);
            if (!is_array($items) || empty($items)) {
                return $this->jsonBack(2,'请添加课程小节');
            }

            $grades = json_decode($params['grades'],true);
            if (!is_array($grades) || empty($grades)) {
                return $this->jsonBack(3,'请添加可选年级');
            }

            Db::startTrans();;
            try {
                $now = time();
                $course = CourseModel::create([
                    'title' => $params['title'],
                    'cover' => $params['cover'],
                    'price' => $params['price'],
                    'tip' => $params['tip'],
                    'qydl' => $params['qydl'],
                    'qdtjr' => $params['qdtjr'],
                    'jxzx' => $params['jxzx'],
                    'grdl' => $params['grdl'],
                    'gdtjr' => $params['gdtjr'],
                    'grades' => $params['grades'],
                    'create_at' => $now
                ]);

                $data = [];
                foreach ($items as $item) {
                    $data[] = [
                        'course_id' => $course->id,
                        'name' => $item['name'],
                        'create_at' => $now
                    ];
                }
                (new CourseItem)->saveAll($data);
                Db::commit();
                return $this->jsonBack(0,'添加成功');
            } catch (\Exception $e) {
                Db::rollback();
                return  $this->jsonBack(4,$e->getMessage());
            }

        } else {
            return $this->fetch('add');
        }
    }

    /**
     * 编辑课程
     * @return mixed|\think\response\Json
     */
    public function edit() {
        if ($this->request->isPost()) {
            $params = $this->getParams(['id','title','cover','price','tip', 'qydl','qdtjr','jxzx','grdl','gdtjr','items','grades']);
            $rule = [
                'id' => 'require|integer|>=:1',
                'title' => 'require|length:1,50',
                'cover' => 'require|min:1',
                'price' => 'require|float|>:0.01',
                'tip' => 'require|length:1,255',
                'qydl' => 'require|float|>=:0',
                'qdtjr' => 'require|float|>=:0',
                'jxzx' => 'require|float|>=:0',
                'grdl' => 'require|float|>=:0',
                'gdtjr' => 'require|float|>=:0'
            ];
            $msg = [
                'id' => '错误的操作',
                'title' => '课程标题1-50个字符',
                'cover' => '请上传课程封面图',
                'price' => '课程价格不小于0.01',
                'tip' => '课程简介1-255个字符',
                'qydl' => '区代奖励不小于0',
                'qdtjr' => '区代推荐人奖励不小于0',
                'jxzx' => '教学中心奖励不小于0',
                'grdl' => '个代奖励不小于0',
                'gdtjr' => '个代推荐人奖励不小于0'
            ];
            $r = $this->validate($params,$rule,$msg);
            if (true !== $r) {
                return $this->jsonBack(1,$r);
            }

            $items = json_decode($params['items'],true);
            if (!is_array($items) || empty($items)) {
                return $this->jsonBack(2,'请添加课程小节');
            }

            $grades = json_decode($params['grades'],true);
            if (!is_array($grades) || empty($grades)) {
                return $this->jsonBack(3,'请添加可选年级');
            }

            Db::startTrans();
            try {
                CourseModel::update([
                    'title' => $params['title'],
                    'cover' => $params['cover'],
                    'price' => $params['price'],
                    'tip' => $params['tip'],
                    'qydl' => $params['qydl'],
                    'qdtjr' => $params['qdtjr'],
                    'jxzx' => $params['jxzx'],
                    'grdl' => $params['grdl'],
                    'gdtjr' => $params['gdtjr'],
                    'grades' => $params['grades']
                ],['id'=>$params['id']]);

                $now = time();
                $data = [];
                foreach ($items as $item) {
                    if ($item['id'] === null) {
                        $data[] = [
                            'course_id' => $params['id'],
                            'name' => $item['name'],
                            'create_at' => $now
                        ];
                    }
                }
                if (!empty($data)) {
                    (new CourseItem)->saveAll($data);
                }
                Db::commit();
                return $this->jsonBack(0,'编辑成功');
            } catch (\Exception $e) {
                Db::rollback();
                return  $this->jsonBack(4,$e->getMessage());
            }

        } else {

            $params = $this->getParams(['id']);
            $rule = [
                'id' => 'require|integer|>=:1'
            ];
            $msg = [
                'id' => '错误的操作'
            ];
            $r = $this->validate($params,$rule,$msg);
            if (true !== $r) {
                $this->error($r);
            }

            $course = CourseModel::find($params['id']);
            if (!$course) {
                $this->error('课程不存在或已经删除');
            }

            $this->assign('items',$course->items);
            $this->assign('course',$course);
            return $this->fetch('edit');
        }
    }

    /**
     * 删除课程
     */
    public function del() {
        $params = $this->getParams(['id']);
        $rule = [
            'id' => 'require|integer|>=:1'
        ];
        $msg = [
            'id' => '错误的操作'
        ];
        $r = $this->validate($params,$rule,$msg);
        if (true !== $r) {
            $this->error($r);
        }

        CourseModel::destroy($params['id']);
        $this->success('操作成功');
    }

    /**
     * 查询用户
     * @return \think\response\Json
     */
    public function getUser() {
        $params = $this->getParams(['key']);
        $rule = [
            'key' => 'require|min:1'
        ];
        $msg = [
            'key' => '请输入手机号码或编号'
        ];
        $r = $this->validate($params,$rule,$msg);
        if (true !== $r) {
            return $this->jsonBack(1,$r);
        }

        if (Validate::is($params['key'],'mobile')) {
            $map[] = ['tel','=',$params['key']];
        } else {
            $map[] = ['invite_code','=',$params['key']];
        }

        try {
            $user = UserModel::where($map)->find();
            if (!$user) throw new \Exception('用户不存在');
            if ($user->is_teacher === 1) throw new \Exception('该用户已经是教师了');
            return $this->jsonBack(0,'',$user);
        } catch (\Exception $e) {
            return $this->jsonBack(2,$e->getMessage());
        }
    }

    /**
     * 视频列表
     * @return \think\response\Json
     */
    public function getVideos() {
        try {
            $list = VideoModel::where('status',0)->select();
            return $this->jsonBack(0,'',$list);
        } catch (\Exception $e) {
            return $this->jsonBack(1,$e->getMessage());
        }
    }

    /**
     * 取得教学中心
     * @return \think\response\Json
     */
    public function getCenters() {
        try {
            $list = TeachCenterModel::where('status',1)->select();
            return $this->jsonBack(0,'',$list);
        } catch (\Exception $e) {
            return $this->jsonBack(1,$e->getMessage());
        }
    }
}
