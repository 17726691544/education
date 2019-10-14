<?php

namespace app\man\controller;
use app\common\controller\Base;
use app\man\model\Team as TeamModel;

class Team extends Base
{
    protected $middleware = ['app\man\middleware\Auth'];

    /**
     * 团队列表
     * @return mixed|string
     */
    public function index() {
        try {
            $list = TeamModel::order('id desc')->paginate(10);
            $this->assign('list',$list);
            return $this->fetch('index');
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 添加团队
     * @return mixed|\think\response\Json
     */
    public function add() {
        if ($this->request->isPost()) {
            $params = $this->getParams(['name','tip','images','detail']);
            $rule = [
                'name' => 'require|length:1,20',
                'tip' => 'require|length:1,40',
                'images' => 'require|min:1',
                'detail' => 'require|min:1'
            ];
            $msg = [
                'name' => '团队名称1-20个字符',
                'tip' => '团队简介1-40个字符',
                'images' => '请上传团队图片',
                'detail' => '请编辑团队详情'
            ];
            $r = $this->validate($params,$rule,$msg);
            if (true !== $r) {
                return $this->jsonBack(1,$r);
            }

            $arr = json_decode($params['images'],true);
            if (!is_array($arr) || empty($arr) || count($arr) > 3) {
                return $this->jsonBack(2,'请上传1-3张团队图片');
            }

            TeamModel::create([
                'name' => $params['name'],
                'tip' => $params['tip'],
                'images' => $params['images'],
                'detail' => $params['detail'],
                'create_at' => time()
            ]);

            return $this->jsonBack(0,'添加成功');

        } else {
            return $this->fetch('add');
        }
    }

    /**
     * 编辑团队
     * @return mixed|\think\response\Json
     */
    public function edit() {
        if ($this->request->isPost()) {
            $params = $this->getParams(['id','name','tip','images','detail']);
            $rule = [
                'id' => 'require|integer|>=:1',
                'name' => 'require|length:1,20',
                'tip' => 'require|length:1,40',
                'images' => 'require|min:1',
                'detail' => 'require|min:1'
            ];
            $msg = [
                'id' => '错误的操作',
                'name' => '团队名称1-20个字符',
                'tip' => '团队简介1-40个字符',
                'images' => '请上传团队图片',
                'detail' => '请编辑团队详情'
            ];
            $r = $this->validate($params,$rule,$msg);
            if (true !== $r) {
                return $this->jsonBack(1,$r);
            }

            $arr = json_decode($params['images'],true);
            if (!is_array($arr) || empty($arr) || count($arr) > 3) {
                return $this->jsonBack(2,'请上传1-3张团队图片');
            }

            TeamModel::update([
                'name' => $params['name'],
                'tip' => $params['tip'],
                'images' => $params['images'],
                'detail' => $params['detail']
            ],['id' => $params['id']]);

            return $this->jsonBack(0,'编辑成功');

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

            $team = TeamModel::find($params['id']);
            if (!$team) {
                $this->error('团队不存在或已经删除');
            }

            $this->assign('team',$team);
            return $this->fetch('edit');
        }
    }

    /**
     * 删除团队
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

        TeamModel::destroy($params['id']);
        $this->success('操作成功');
    }
}
