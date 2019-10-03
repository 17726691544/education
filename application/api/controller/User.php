<?php


namespace app\api\controller;

use app\api\common\JwtAuth;
use app\api\service\SmsService;
use app\api\service\UserService;
use app\common\controller\Base;
use app\common\exception\BusinessBaseException;
use app\common\validate\BaseValidate;
use app\common\validate\UserV;
use \app\common\model\User as UserModel;

class User extends Base
{

    /**
     * 注册获取验证码
     * @return \think\response\Json
     * @throws BusinessBaseException
     */
    public function getRegCode()
    {
        $params = $this->getParams(['tel']);
        (new UserV())->goChick($params);

        $regCode = (new SmsService())->getRegCode($params['tel']);
        if (!$regCode) {
            return $this->jsonBack(1, '发送失败');
        }

        return $this->jsonBack(0, '发送成功');

    }

    /**
     * 用户注册
     * @return \think\response\Json
     * @throws BusinessBaseException
     */
    public function register()
    {
        $params = $this->getParams(['tel', 'u_type', 'tel_code', 'pass', 'safe_pass', 'invite_code']);
        (new UserV())->goChick($params);

        $register = (new UserService())->register($params);
        if (!$register) {
            return $this->jsonBack(1, '注册失败');
        }
        return $this->jsonBack(0, '注册成功');
    }

    /**
     * 用户登录
     * @throws BusinessBaseException
     */
    public function login()
    {
        $params = $this->getParams(['tel', 'pass']);
        (new UserV())->goChick($params);

        $login = (new UserService())->login($params['tel'], $params['pass']);
        if (!$login) {
            return $this->jsonBack(1, '登录失败');
        }
        return $this->jsonBack(0, '登录成功', $login);
    }

    /**
     * 获取用户基本信息
     * @return |null
     * @throws BusinessBaseException
     */
    public function getUserInfo()
    {
        (new BaseValidate())->tokenChick();
        $jwtAuth = JwtAuth::instance();
        $uid = $jwtAuth->getUid();
        if (!$uid) {
            return $this->jsonBack(1, '无效令牌');
        }
        //查询数据库获取用户信息
        $userInfo = UserModel::get($uid)->find();
        return $this->jsonBack(0, '成功', $userInfo);

    }

}