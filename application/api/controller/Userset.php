<?php


namespace app\api\controller;


use app\api\common\JwtAuth;
use app\common\controller\Base;
use app\common\exception\BusinessBaseException;
use app\common\validate\BaseValidate;
use app\common\model\User as UserModel;

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

    }




}