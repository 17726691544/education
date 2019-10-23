<?php

namespace app\man\controller;
use app\common\controller\Base;
use app\man\model\Course as CourseModel;

class Goods extends Base
{
    protected $middleware = ['app\man\middleware\Auth'];

    /**
     * 商品列表
     * @return mixed|string
     */
    public function index() {
        try {
            $list = CourseModel::where('status',0)->where('type',1)->order('id desc')->paginate(10);
            $this->assign('list',$list);
            return $this->fetch('index');
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 添加商品
     * @return mixed|\think\response\Json
     */
    public function add() {
        if ($this->request->isPost()) {
            $params = $this->getParams(['title','cover','price','tip', 'gd_price','gd_num','qd_price','qd_num']);
            $rule = [
                'title' => 'require|length:1,50',
                'cover' => 'require|min:1',
                'price' => 'require|float|>=:0.01',
                'tip' => 'require|length:1,255',
                'gd_price' => 'require|float|>=:0.01',
                'qd_price' => 'require|float|>=:0.01',
                'gd_num' => 'require|integer|>=:1',
                'qd_num' => 'require|integer|>=:1'
            ];
            $msg = [
                'title' => '商品标题1-50个字符',
                'cover' => '请上传商品封面图',
                'price' => '商品零售价格不小于0.01',
                'tip' => '商品简介1-255个字符',
                'gd_price' => '个代价格不小于0.01',
                'qd_price' => '区代价格不小于0.01',
                'gd_num' => '个代购买数量不小于1',
                'qd_num' => '区代购买数量不小于1'
            ];
            $r = $this->validate($params,$rule,$msg);
            if (true !== $r) {
                return $this->jsonBack(1,$r);
            }

            $now = time();
            CourseModel::create([
                'title' => $params['title'],
                'cover' => $params['cover'],
                'price' => $params['price'],
                'tip' => $params['tip'],
                'gd_price' => $params['gd_price'],
                'qd_price' => $params['qd_price'],
                'gd_num' => $params['gd_num'],
                'qd_num' => $params['qd_num'],
                'type' => 1,
                'create_at' => $now
            ]);

            return $this->jsonBack(0,'添加成功');

        } else {
            return $this->fetch('add');
        }
    }

    /**
     * 商品信息
     * @return \think\response\Json
     */
    public function info() {
        $params = $this->getParams(['id']);
        $rule = [
            'id' => 'require|integer|>=:1'
        ];
        $msg = [
            'id' => '错误的操作'
        ];
        $r = $this->validate($params,$rule,$msg);
        if (true !== $r) {
            return $this->jsonBack(1,$r);
        }
        $course = CourseModel::find($params['id']);
        if (!$course) {
            return $this->jsonBack(2,'商品不存在或已经删除');
        }
        return $this->jsonBack(0,'',$course);
    }

    /**
     * 编辑商品
     * @return mixed|\think\response\Json
     */
    public function edit() {
        if ($this->request->isPost()) {
            $params = $this->getParams(['id','title','cover','price','tip', 'gd_price','gd_num','qd_price','qd_num']);
            $rule = [
                'id' => 'require|integer|>=:1',
                'title' => 'require|length:1,50',
                'cover' => 'require|min:1',
                'price' => 'require|float|>=:0.01',
                'tip' => 'require|length:1,255',
                'gd_price' => 'require|float|>=:0.01',
                'qd_price' => 'require|float|>=:0.01',
                'gd_num' => 'require|integer|>=:1',
                'qd_num' => 'require|integer|>=:1'
            ];
            $msg = [
                'id' => '错误的操作',
                'title' => '商品标题1-50个字符',
                'cover' => '请上传商品封面图',
                'price' => '商品零售价格不小于0.01',
                'tip' => '商品简介1-255个字符',
                'gd_price' => '个代价格不小于0.01',
                'qd_price' => '区代价格不小于0.01',
                'gd_num' => '个代购买数量不小于1',
                'qd_num' => '区代购买数量不小于1'
            ];
            $r = $this->validate($params,$rule,$msg);
            if (true !== $r) {
                return $this->jsonBack(1,$r);
            }

            $now = time();
            CourseModel::update([
                'title' => $params['title'],
                'cover' => $params['cover'],
                'price' => $params['price'],
                'tip' => $params['tip'],
                'gd_price' => $params['gd_price'],
                'qd_price' => $params['qd_price'],
                'gd_num' => $params['gd_num'],
                'qd_num' => $params['qd_num'],
                'type' => 1,
                'create_at' => $now
            ],['id'=>$params['id']]);

            return $this->jsonBack(0,'修改成功');

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

            $this->assign('id',$params['id']);
            return $this->fetch('edit');
        }
    }


    /**
     * 删除商品
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

        CourseModel::update(['status'=>1],['id'=>$params['id']]);
        $this->success('操作成功');
    }
}
