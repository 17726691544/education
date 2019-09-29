<?php

namespace app\common\controller;

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
}
