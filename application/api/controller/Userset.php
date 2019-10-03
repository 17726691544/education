<?php


namespace app\api\controller;


use app\api\common\JwtAuth;
use app\common\controller\Base;
use app\common\service\OssService;
use app\common\validate\BaseValidate;

class Userset extends Base
{
    /**
     * 上传头像
     */
    public function uploadHead()
    {
        (new BaseValidate())->tokenChick();
        $jwtAuth = JwtAuth::instance();
        $uid = $jwtAuth->getUid();

        if(!$uid){
           return $this->jsonBack(1,'无效的令牌');
        }

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
        echo  $saveName;
        $r = OssService::uploadFile($info['tmp_name'], $saveName, 'images');
        if (false === $r) {
            return $this->jsonBack(1, '同步到云失败');
        }

        return $this->jsonBack(0, '', $r);
    }




}