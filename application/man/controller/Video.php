<?php

namespace app\man\controller;
use app\common\controller\Base;
use app\man\model\Video as VideoModel;

class Video extends Base
{
    protected $middleware = ['app\man\middleware\Auth'];

    /**
     * 视频列表
     * @return mixed|string
     */
    public function index() {
        try {
            $list = VideoModel::where('status',0)->order('id desc')->paginate(10);
            $this->assign('list',$list);
            return $this->fetch('index');
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 添加视频
     * @return mixed|\think\response\Json
     */
    public function add() {
        if ($this->request->isPost()) {

            $params = $this->getParams(['title','cover','video']);
            $rule = [
                'title' => 'require|length:1,50',
                'cover' => 'require|min:1',
                'video' => 'require|min:1'
            ];
            $msg = [
                'title' => '课程标题1-50个字符',
                'cover' => '请上传视频封面图',
                'video' => '请上传视频'
            ];
            $r = $this->validate($params,$rule,$msg);
            if (true !== $r) {
                return $this->jsonBack(1,$r);
            }

            VideoModel::create([
                'title' => $params['title'],
                'cover' => $params['cover'],
                'video' => $params['video'],
                'create_at' => time()
            ]);
            return $this->jsonBack(0,'添加成功');
        } else {
            return $this->fetch('add');
        }
    }

    /**
     * 编辑video
     * @return mixed|\think\response\Json
     */
    public function edit() {
        if ($this->request->isPost()) {
            $params = $this->getParams(['id','title','cover','video']);
            $rule = [
                'id' => 'require|integer|>=:1',
                'title' => 'require|length:1,50',
                'cover' => 'require|min:1',
                'video' => 'require|min:1'
            ];
            $msg = [
                'id' => '错误的操作',
                'title' => '课程标题1-50个字符',
                'cover' => '请上传视频封面图',
                'video' => '请上传视频'
            ];
            $r = $this->validate($params,$rule,$msg);
            if (true !== $r) {
                return $this->jsonBack(1,$r);
            }

            VideoModel::update([
                'title' => $params['title'],
                'cover' => $params['cover'],
                'video' => $params['video']
            ],['id' => $params['id']]);
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

            $video = VideoModel::find($params['id']);
            if (!$video) {
                $this->error('课程案例不存在或已经删除');
            }

            $this->assign('video',$video);
            return $this->fetch('edit');
        }
    }

    /**
     * 删除video
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

        VideoModel::update(['status'=>1],['id'=>$params['id']]);
        $this->success('操作成功');
    }

    /**
     * 展示控制
     */
    public function show() {
        $params = $this->getParams(['id','is_show']);
        $rule = [
            'id' => 'require|integer|>=:1',
            'is_show' => 'require|integer|in:0,1',
        ];
        $msg = [
            'id' => '错误的操作',
            'is_show' => '错误的操作'
        ];
        $r = $this->validate($params,$rule,$msg);
        if (true !== $r) {
            $this->error($r);
        }

        VideoModel::update(['is_show' => $params['is_show']], ['id' => $params['id']]);
        $this->success('操作成功');
    }
}
