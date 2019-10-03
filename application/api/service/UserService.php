<?php


namespace app\api\service;

use app\common\enum\CodeCaheEnum;
use app\common\enum\UserEnum;
use app\common\exception\BusinessBaseException;
use app\common\model\User;

class UserService extends BaseService
{

    /**
     * 注册
     * @param $params
     * @return bool
     * @throws BusinessBaseException
     */
    public function register($params)
    {
        $tel = $params['tel'];
        $codeContent = $params['tel_code'];
        $uType = $params['u_type'];
        $invitedCode = $params['invite_code'];

        //判断验证码是否正确
        $this->checkCode($tel, $codeContent, CodeCaheEnum::SEND_TYPE_TEL, CodeCaheEnum::CODE_TYPE_REG);
        //判断用户是否注册
        $user = User::findByTel($tel);
        if ($user) {
            throw new BusinessBaseException('用户已注册');
        }
        $now = time();
        //保存用户
        $parentId = $this->findParentId($invitedCode);
        $user = new User();
        $user->save([
            'tel' => $tel,
            'pass' => md5($tel . $params['pass']),
            'safe_pass' => md5($tel . $params['safe_pass']),
            'nick' => $tel,
            'level' => UserEnum::USER_LEVEL_1,
            'parent_id' => $parentId ? $parentId : 0,
            'u_type' => $uType,
            'u_status' => (int)$uType === UserEnum::USER_TYPE_TEA ? UserEnum::USER_STATUS_AUDIT : UserEnum::USER_STATUS_AUDITSUCEE,
            'reg_at' => $now
        ]);
        //根据保存邀请码
        $code = $this->createCode($user->id);
        (new User())->save([
            'invite_code' => $code
        ], ['id' => $user->id]);

        return true;
    }


    /**
     * 用户登录
     * @param $tel
     * @param $pass
     * @return bool
     * @throws BusinessBaseException
     */
    public function login($tel, $pass)
    {
        $user = User::where('tel', $tel)->find();
        if (!$user) {
            throw new BusinessBaseException('用户不存在');
        }
        //判断密码
        $inputPass = md5($user->tel . $pass);
        if ($inputPass !== $user->pass) {
            throw new BusinessBaseException('用户名或密码错误');
        }
        //判断状态
        $u_status = $user->u_status;
        if($u_status !== UserEnum::USER_STATUS_AUDITSUCEE){
            switch ($u_status) {
                case UserEnum::USER_STATUS_AUDIT:
                    throw new BusinessBaseException('账号审核中',1);
                    break;
                case UserEnum::USER_STATUS_AUDITFAIL:
                    throw new BusinessBaseException('账号未通过审核',2);
                    break;
                case UserEnum::USER_STATUS_HITLIST:
                    throw new BusinessBaseException('账号已冻结',3);
                    break;
            }
        }
        //生成登录令牌返回用户信息
        return $user;


    }

    /**
     * 查找父id
     * @param $invitedCode
     * @return mixed|null
     */
    private function findParentId($invitedCode)
    {
        if ($invitedCode) {
            $user = User::findByInviteCode($invitedCode);
            if ($user) {
                return $parentId = $user->id;
            }
        }
        return null;
    }

    /**
     * 通过用户id生成唯一邀请码
     * @param $userId
     * @return string
     */
    private function createCode($userId)
    {
        static $source_string = 'E5FCDG3HQA4B1NOPIJ2RSTUV67MWX89KLYZ';
        $num = $userId;
        $code = '';
        while ($num > 0) {
            $mod = $num % 35;
            $num = ($num - $mod) / 35;
            $code = $source_string[$mod] . $code;
        }
        if (empty($code[3]))
            $code = str_pad($code, 4, '0', STR_PAD_LEFT);
        return $code;
    }

}