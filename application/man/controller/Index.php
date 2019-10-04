<?php

namespace app\man\controller;
use app\common\controller\Base;
use app\common\service\OssService;
use app\man\model\Admin;
use think\Db;
use think\Request;

class Index extends Base
{
    protected $middleware = ['app\man\middleware\Auth' => ['except'=>['noneAuth']]];

    /**
     * 首页
     * @return mixed
     */
    public function index()
    {
        $_auth = session('auth');

        try {
            $admin = Admin::with('access')->find($_auth['id']);
            $access= array_column($admin->access->toArray(),'action');
            $plate = [];
            $accessArr = [];
            foreach ($access as $item) {
                $accessArr = array_merge($accessArr,explode(',',$item));
            }
            foreach ($accessArr as $item) {
                $tempArr = explode('/',$item);
                if (!in_array($tempArr[0],$plate)) {
                    $plate[] = $tempArr[0];
                }
            }

            $this->assign('is_su',$_auth['is_su']);
            $this->assign('access',$accessArr);
            $this->assign('plate',$plate);
        } catch (\Exception $e) {
            $this->redirect('Normal/index');
        }

        return $this->fetch('index');
    }

    /**
     * 面板
     * @return mixed
     */
    public function main()
    {
        return 'ok';
    }

    /**
     * @param Request $request
     * @return mixed|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function pwd(Request $request) {

        if ($this->request->isPost()) {

            $params = $this->getParams(['old_pwd', 'pwd', 're_pwd']);

            $rules = [
                'old_pwd' => 'require|alphaNum|length:6,32',
                'pwd' => 'require|alphaNum|length:6,32',
                're_pwd' => 'require|confirm:pwd',
            ];

            $msg = [
                'old_pwd' => '密码格式不正确',
                'pwd' => '密码格式不正确',
                're_pwd' => '两次密码不一致'
            ];

            $r = $this->validate($params, $rules, $msg);

            if (true !== $r) {
                $this->error($r);
            }

            $admin = Admin::where('id',$request->admin_id)->find();

            if (true !== $admin->checkPwd($admin,$params['old_pwd'])) {
                $this->error('原密码不正确');
            }

            $admin->pwd = md5($admin->reg_at . $params['pwd']);
            $admin->save();
            $this->success('修改成功');
        } else {
            return $this->fetch('pwd');
        }

    }

    /**
     * 退出
     */
    public function logout() {
        session(null);
        $this->redirect('Normal/login');
    }

    /**
     * 没有权限
     * @return mixed
     */
    public function noneAuth() {
        return $this->fetch('noneAuth');
    }

    /**
     * 上传图片
     * @return \think\response\Json
     */
    public function uploadImage() {
        $file = request()->file('image');

        if (!$file) {
            return $this->jsonBack(1, '请上传文件');
        }

        $info = $file->getInfo();
        if ($info['error'] !== 0 || !$file->isValid()) {
            return $this->jsonBack(2, '上传文件失败');
        }

        if (!$file->check(['ext' => 'jpg,png,jpeg'])) {
            return $this->jsonBack(3, '仅支持jpg，png，jpeg格式');
        }

        $saveName = md5(mt_rand(0, 10000) . time()) . '.' . pathinfo($info['name'], PATHINFO_EXTENSION);
        $r = OssService::uploadFile($info['tmp_name'], $saveName, 'images');
        if (false === $r) {
            return $this->jsonBack(4, '同步到云失败');
        }

        return $this->jsonBack(0, '', $r);
    }

    /**
     * 上传视频
     * @return \think\response\Json
     */
    public function uploadVideo() {
        $file = request()->file('video');

        if (!$file) {
            return $this->jsonBack(1, '请上传文件');
        }

        $info = $file->getInfo();
        if ($info['error'] !== 0 || !$file->isValid()) {
            return $this->jsonBack(2, '上传文件失败');
        }

        if (!$file->check(['ext' => 'mp4'])) {
            return $this->jsonBack(3, '仅支持mp4格式');
        }

        $saveName = md5(mt_rand(0, 10000) . time()) . '.' . pathinfo($info['name'], PATHINFO_EXTENSION);
        $r = OssService::uploadFile($info['tmp_name'], $saveName, 'videos');
        if (false === $r) {
            return $this->jsonBack(4, '同步到云失败');
        }

        return $this->jsonBack(0, '', $r);
    }

    public function test() {
        $p = Db::name('province')->where('id',22)->find();
        $citys = Db::name('city')->where('province_id',$p['province_id'])->select();
        foreach ($citys as $key=>$city) {

            $child = Db::name('country')->where('city_id',$city['city_id'])
                ->select();
            $citys[$key]['child'] = $child;
        }

        dump($p);
        dump($citys);
        dump(time());


    }

}
