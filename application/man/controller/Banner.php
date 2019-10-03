<?php

namespace app\man\controller;
use app\common\controller\Base;
use app\man\model\Banner as BannerModel;

class Banner extends Base
{
    protected $middleware = ['app\man\middleware\Auth'];

    /**
     * 轮播列表
     * @return mixed|string
     */
    public function index() {
        try {
            $list = BannerModel::order('sort desc')->paginate(10);
            $this->assign('list',$list);
            return $this->fetch('index');
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 添加banner
     * @return mixed|\think\response\Json
     */
    public function add() {
        if ($this->request->isPost()) {
            $params = $this->getParams(['sort','url']);
            $rule = [
                'sort' => 'require|integer|between:0,100',
                'url' => 'require|min:1'
            ];
            $msg = [
                'sort' => '排序在0-100之间',
                'url' => '请上传图片'
            ];
            $r = $this->validate($params,$rule,$msg);
            if (true !== $r) {
                return $this->jsonBack(1,$r);
            }

            BannerModel::create([
               'image' => $params['url'],
               'sort' => $params['sort'],
               'create_at' => time()
            ]);
            return $this->jsonBack(0,'添加成功');
        } else {
            return $this->fetch('add');
        }
    }

    /**
     * 修改sort
     * @return \think\response\Json
     */
    public function setSort() {
        if ($this->request->isPost()) {
            $params = $this->getParams(['sort','id']);
            $rule = [
                'sort' => 'require|integer|between:0,100',
                'id' => 'require|integer|>=:1'
            ];
            $msg = [
                'sort' => '排序在0-100之间',
                'id' => '错误的操作'
            ];
            $r = $this->validate($params,$rule,$msg);
            if (true !== $r) {
                return $this->jsonBack(1,$r);
            }

            BannerModel::update(['sort'=>$params['sort']],['id'=>$params['id']]);
            return $this->jsonBack(0,'修改成功');

        } else {
            return $this->jsonBack(2,'error method');
        }
    }

    /**
     * 删除banner
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

        BannerModel::destroy($params['id']);
        $this->success('操作成功');
    }
}
