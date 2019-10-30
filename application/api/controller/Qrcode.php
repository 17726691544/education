<?php


namespace app\api\controller;


use app\api\common\QrcodeUtil;
use app\common\controller\Base;
use app\common\exception\BusinessBaseException;
use app\common\validate\BaseValidate;
use app\common\model\User;

class Qrcode extends Base
{

    /**
     * 获取自定的推荐二维码
     */
    public function getReferralQrcode()
    {
        (new BaseValidate())->tokenChick();
        $uid = $this->getUid();

        //获取该用户的信息
        $user = User::get($uid);
        if (!$user) throw new BusinessBaseException('获取用户信息失败');

        // 生成二维码并直接展示给前端
        $content = 'http://www.baidu.com?inviteCode=' . $user->invite_code;
        $qr_code = new QrcodeUtil();
        $qr_img = $qr_code->createServer($content);
        $base64_encode = base64_encode($qr_img);
        return $this->jsonBack(0,'成功' ,$base64_encode);

    }

}