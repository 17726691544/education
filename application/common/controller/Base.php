<?php

namespace app\common\controller;

use app\api\common\JwtAuth;
use app\common\exception\BusinessBaseException;
use think\Controller;

class Base extends Controller
{
    /**
     * json返回
     * @param $code
     * @param string $msg
     * @param null $data
     * @return \think\response\Json
     */
    protected function jsonBack($code, $msg = '', $data = null)
    {
        return json(['code' => $code, 'msg' => $msg, 'data' => $data]);
    }

    /**
     * 取得参数
     * @param $keys
     * @return array
     */
    protected function getParams($keys)
    {
        $params = $this->request->param();
        $fix = [];
        foreach ($keys as $key) {
            $fix[$key] = isset($params[$key]) ? trim($params[$key]) : null;
        }
        return $fix;
    }


    /**
     * 获取用户id
     * @return mixed
     * @throws BusinessBaseException
     */
    protected function getUid()
    {
        $jwtAuth = JwtAuth::instance();
        $uid = $jwtAuth->getUid();
        if (!$uid) {
            throw new BusinessBaseException('无效的令牌');
        }
        return $uid->getValue();
    }

    /**
     * 创建exception
     * @param $msg
     * @return \Exception
     */
    public function createError($msg) {
        return new \Exception($msg);
    }
}
