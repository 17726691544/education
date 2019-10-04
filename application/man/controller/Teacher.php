<?php

namespace app\man\controller;
use app\common\controller\Base;
use app\man\model\TeacherCenter;
use app\man\model\User as UserModel;
use app\man\model\TeachCenter as TeachCenterModel;
use app\man\model\Teacher as TeacherModel;
use think\Db;
use think\facade\Validate;

class Teacher extends Base
{
    protected $middleware = ['app\man\middleware\Auth'];

    /**
     * 教师列表
     * @return mixed|string
     */
    public function index() {
        $params = $this->getParams(['key']);
        $map = [];
        $query = ['query' => $params];
        if ($this->request->isPost()) {
            $query['page'] = 1;
        }
        if ($params['key']) {
            if (Validate::is($params['key'],'mobile')) {
                $user_id = UserModel::where('tel',$params['key'])->value('id');
                if ($user_id) {
                    $map[] = ['user_id','=',$user_id];
                } else {
                    $this->error('用户不存在');
                }
            } else {
                $user_id = UserModel::where('invite_code',$params['key'])->value('id');
                if ($user_id) {
                    $map[] = ['user_id','=',$user_id];
                } else {
                    $this->error('用户不存在');
                }
            }
        }

        try {
            $list = TeacherModel::with('user')->where($map)->order('id desc')->paginate(10,false,$query);
            $this->assign('params',$params);
            $this->assign('list',$list);
            return $this->fetch('index');
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 添加教师
     * @return mixed|\think\response\Json
     */
    public function add() {
        if ($this->request->isPost()) {
            $params = $this->getParams(['user_id','name','education','position', 'ability','image','cover','tips','centers']);
            $rule = [
                'user_id' => 'require|integer|>:0',
                'name' => 'require|length:1,30',
                'education' => 'require|length:1,30',
                'position' => 'require|length:1,30',
                'ability' => 'require|length:1,1024',
                'image' => 'require|min:1',
                'cover' => 'require|min:1'
            ];
            $msg = [
                'user_id' => '请指定用户',
                'name' => '姓名1-30个字符',
                'education' => '学历1-30个字符',
                'position' => '职称1-30个字符',
                'ability' => '特长及能力1-1024个字符',
                'image' => '请上传首页展示图',
                'cover' => '请上传详情页展示图'
            ];
            $r = $this->validate($params,$rule,$msg);
            if (true !== $r) {
                return $this->jsonBack(1,$r);
            }

            $tips = json_decode($params['tips'],true);
            if (!is_array($tips) || empty($tips)) {
                return $this->jsonBack(2,'请添加履历成就');
            }

            $centers = json_decode($params['centers'],true);
            if (!is_array($centers) || empty($centers)) {
                return $this->jsonBack(3,'请指定教学中心');
            }

            Db::startTrans();;
            try {
                $user = UserModel::find($params['user_id']);
                if ($user->is_teacher === 1) throw new \Exception('该用户已经是教师了');
                $dbCenters = TeachCenterModel::where('id','in',$centers)
                    ->where('status',1)
                    ->select();
                if ($dbCenters->count() !== count($centers)) throw new \Exception('错误的操作');

                $now = time();
                $teacher = TeacherModel::create([
                    'user_id' => $params['user_id'],
                    'name' => $params['name'],
                    'education' => $params['education'],
                    'position' => $params['position'],
                    'ability' => $params['ability'],
                    'tips' => $params['tips'],
                    'image' => $params['image'],
                    'cover' => $params['cover'],
                    'create_at' => $now
                ]);

                $data = [];
                foreach ($centers as $center) {
                    $data[] = [
                        'teacher_id' => $teacher->id,
                        'center_id' => $center
                    ];
                }
                (new TeacherCenter())->saveAll($data);
                $user->is_teacher = 1;
                $user->save();
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
     * 编辑教师
     * @return mixed|\think\response\Json
     */
    public function edit() {
        if ($this->request->isPost()) {
            $params = $this->getParams(['id','name','education','position', 'ability','image','cover','tips','centers']);
            $rule = [
                'id' => 'require|integer|>:0',
                'name' => 'require|length:1,30',
                'education' => 'require|length:1,30',
                'position' => 'require|length:1,30',
                'ability' => 'require|length:1,1024',
                'image' => 'require|min:1',
                'cover' => 'require|min:1'
            ];
            $msg = [
                'id' => '错误的操作',
                'name' => '姓名1-30个字符',
                'education' => '学历1-30个字符',
                'position' => '职称1-30个字符',
                'ability' => '特长及能力1-1024个字符',
                'image' => '请上传首页展示图',
                'cover' => '请上传详情页展示图'
            ];
            $r = $this->validate($params,$rule,$msg);
            if (true !== $r) {
                return $this->jsonBack(1,$r);
            }

            $tips = json_decode($params['tips'],true);
            if (!is_array($tips) || empty($tips)) {
                return $this->jsonBack(2,'请添加履历成就');
            }

            $centers = json_decode($params['centers'],true);
            if (!is_array($centers) || empty($centers)) {
                return $this->jsonBack(3,'请指定教学中心');
            }


            Db::startTrans();;
            try {
                $teacher = TeacherModel::find($params['id']);
                if (!$teacher) throw new \Exception('错误的操作');

                $dbCenters = TeachCenterModel::where('id','in',$centers)
                    ->where('status',1)
                    ->select();
                if ($dbCenters->count() !== count($centers)) throw new \Exception('错误的操作');

                $teacher->save([
                    'name' => $params['name'],
                    'education' => $params['education'],
                    'position' => $params['position'],
                    'ability' => $params['ability'],
                    'tips' => $params['tips'],
                    'image' => $params['image'],
                    'cover' => $params['cover']
                ]);

                $middle = TeacherCenter::where('teacher_id',$params['id'])->select();
                $middleMap = [];
                foreach ($middle as $item) {
                    $middleMap[$item->center_id] = true;
                }

                $data = [];
                foreach ($centers as $center) {
                    if (isset($middleMap[$center])) {
                        $middleMap[$center] = false;
                    } else {
                        $data[] = [
                            'teacher_id' => $teacher->id,
                            'center_id' => $center
                        ];
                    }
                }

                if (!empty($data)) (new TeacherCenter())->saveAll($data);
                $delIds = [];
                foreach ($middleMap as $id => $flag) {
                    if ($flag) $delIds[] = $id;
                }

                if (!empty($delIds)) TeacherCenter::destroy($delIds);

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

            $teacher = TeacherModel::find($params['id']);
            if (!$teacher) {
                $this->error('教师不存在或已经删除');
            }

            try {
                $middle = TeacherCenter::with('center')->where('teacher_id',$params['id'])->select();
                $centers = [];
                foreach ($middle as $item) {
                    $centers[] = $item->center;
                }
                $this->assign('centers',$centers);
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
            $this->assign('teacher',$teacher);
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

        Db::startTrans();
        try {
            $teacher = TeacherModel::find($params['id']);
            if (!$teacher) throw new \Exception('错误的操作');
            $teacher->user->is_teacher = 0;
            $teacher->user->save();
            TeacherCenter::where('teacher_id',$teacher->id)->delete();
            $teacher->delete();
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
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
