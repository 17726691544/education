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
            throw new BusinessBaseException('无效的token');
        }
        $file = request()->file('head');
        if(!$file){
            throw new BusinessBaseException('上传不能为空');
        }
        $info = $file->validate(['size'=>10240,'ext'=>'jpg,png,gif'])->move( '../uploads');
        if($info){
            $saveName = $info->getSaveName();
            $user = new UserModel();

           $result = $user->save([
                'head_url'=>'/'.$saveName
            ],['id'=>$uid]);
           if(!$result){
               throw new BusinessBaseException('上传失败');
           }
            return $this->jsonBack(0,'上传成功','/uploads/'.$saveName);
        }else{
            throw new BusinessBaseException($file->getError());
        }
    }




}