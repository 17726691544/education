<?php


namespace app\api\controller;

use app\api\service\SmsService;
use app\api\service\UserService;
use app\common\controller\Base;
use app\common\exception\BusinessBaseException;
use app\common\model\Orders;
use app\common\validate\BaseValidate;
use app\common\validate\PageV;
use app\common\validate\UserV;
use \app\common\model\User as UserModel;

/**
 * 用户controller
 * @package app\api\controller
 */
class User extends Base
{

    /**
     * 获取验证码
     * @return \think\response\Json
     * @throws BusinessBaseException
     */
    public function getCode()
    {
        $params = $this->getParams(['tel', 'send_type', 'code_type']);
        (new UserV())->goChick($params);

        $result = (new SmsService())->getCode($params['tel'], $params['send_type'], $params['code_type']);
        if (!$result) {
            throw new BusinessBaseException('发送失败');
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
            throw new BusinessBaseException('注册失败');
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
            throw new BusinessBaseException('登录失败');
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
        $uid = $this->getUid();
        //查询数据库获取用户信息
        $userInfo = UserModel::get($uid);
        return $this->jsonBack(0, '成功', $userInfo);

    }

    /**
     * 分页获取购买记录
     */
    public function getBuyRecordList()
    {
        $params = $this->getParams(['page', 'pageNum']);
        (new PageV())->tokenChick()->goChick($params);
        $uid = $this->getUid();

        $buyRecordList = (new Orders())->getBuyRecordList($uid, $params['page'] ?? 1, $params['pageNum'] ?? 5);
        return $this->jsonBack(0, '成功', $buyRecordList);
    }

}