<?php


namespace app\api\controller;


use app\api\service\UsersetService;
use app\common\controller\Base;
use app\common\service\OssService;
use app\common\validate\BaseValidate;
use app\common\validate\UsersetV;
use app\common\model\User as UserModel;

/**
 * 用户设置controller
 * @package app\api\controller
 */
class Userset extends Base
{
    /**
     * 修改头像
     */
    public function uploadHead()
    {
        (new BaseValidate())->tokenChick();

        $file = request()->file('image');
        if (!$file) {
            return $this->jsonBack(1, '请上传文件');
        }

        $info = $file->getInfo();
        if ($info['error'] !== 0 || !$file->isValid()) {
            return $this->jsonBack(1, '上传文件失败');
        }

        if (!$file->check(['ext' => 'jpg,png,jpeg'])) {
            return $this->jsonBack(1, '仅支持jpg，png，jpeg格式');
        }
        $saveName = md5(mt_rand(0, 10000) . time()) . '.' . pathinfo($info['name'], PATHINFO_EXTENSION);
        echo $saveName;
        $r = OssService::uploadFile($info['tmp_name'], $saveName, 'images');
        if (false === $r) {
            return $this->jsonBack(1, '同步到云失败');
        }
        //保存地址到数据库

        return $this->jsonBack(0, '', $r);
    }

    /**
     * 修改昵称
     * @return \think\response\Json
     * @throws \app\common\exception\BusinessBaseException
     */
    public function editNick()
    {
        $params = $this->getParams(['nick']);
        (new UsersetV())->tokenChick()->goChick($params);
        $uid = $this->getUid();

        $result = (new UserModel())->editByUId($uid, ['nick' => $params['nick']]);
        if (!$result) {
            return $this->jsonBack(1, '修改失败');
        }
        return $this->jsonBack(0, '修改成功');
    }

    /**
     * 修改登密码
     * @return \think\response\Json
     */
    public function editPass()
    {
        $params = $this->getParams(['pass', 'newpass', 'renewpass']);
        (new UsersetV())->tokenChick()->goChick($params);
        $uid = $this->getUid();

        $result = (new UserModel())->editPass($uid, $params['pass'], $params['newpass'], 1);
        if (!$result) {
            return $this->jsonBack(1, '修改失败');
        }
        return $this->jsonBack(0, '修改成功');

    }

    /**
     * 修改安全密码
     * @return \think\response\Json
     */
    public function editSafePass()
    {
        $params = $this->getParams(['safe_pass', 'newpass', 'renewpass']);
        (new UsersetV())->tokenChick()->goChick($params);
        $uid = $this->getUid();

        $result = (new UserModel())->editPass($uid, $params['safe_pass'], $params['newpass'], 2);
        if (!$result) {
            return $this->jsonBack(1, '修改失败');
        }
        return $this->jsonBack(0, '修改成功');

    }

    /**
     * 找回密码
     * @return \think\response\Json
     * @throws \app\common\exception\BusinessBaseException
     */
    public function findPass()
    {
        $params = $this->getParams(['tel', 'tel_code', 'newpass', 'renewpass']);
        (new UsersetV())->goChick($params);

        $result = (new UsersetService())->findPass($params);
        if (!$result) {
            return $this->jsonBack(1, '设置失败');
        }
        return $this->jsonBack(0, '设置成功');
    }

}