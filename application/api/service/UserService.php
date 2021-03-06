<?php


namespace app\api\service;

use app\api\common\JwtAuth;
use app\common\enum\CodeCaheEnum;
use app\common\enum\UserEnum;
use app\common\exception\BusinessBaseException;
use app\common\model\User;
use think\Db;

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
        $this->checkCode($tel, $codeContent, 1, 1);
        //判断用户是否注册
        $user = User::findByTel($tel);
        if ($user) {
            throw new BusinessBaseException('用户已注册');
        }
        $now = time();
        //保存用户
        $parentId = $this->findParentId($invitedCode);
      	if(empty($parentId)){
            throw new BusinessBaseException('邀请码不存在');
        }
        Db::startTrans();
        try {
            $user = new User();
            $user->save([
                'tel' => $tel,
                'pass' => md5($now . $params['pass']),
                'safe_pass' => md5($now . $params['safe_pass']),
                'parent_id' => $parentId ? $parentId : 0,
                'u_type' => $uType,
                'reg_at' => $now,
                'invite_code'=>$tel
            ]);
            //根据保存邀请码
//            $code = $this->createCode($user->id);
//            (new User())->save([
//                'invite_code' => $code
//            ], ['id' => $user->id]);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            return false;
        }
    }


    /**
     * 用户登录
     * @param $tel
     * @param $pass
     */
    public function login($tel, $pass)
    {
        $user = User::where('tel', $tel)->find();
        if (!$user) {
            throw new BusinessBaseException('用户不存在');
        }
        //判断密码
        $inputPass = md5($user->getData('reg_at') . $pass);
        if ($inputPass !== $user->pass) {
            throw new BusinessBaseException('用户名或密码错误');
        }
        //生成登录令牌返回用户信息
        $jwtAuth = JwtAuth::instance();
        return $jwtAuth->tokenEncode(['uid' => $user->id]);

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
        $num = $userId + 2500;
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