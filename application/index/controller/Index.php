<?php

namespace app\index\controller;

use app\common\controller\Base;
use think\Response;


class Index extends Base
{
    public function index()
    {
        return 'ok';
    }

    public function reg() {
        $params = $this->getParams(['code']);
        $params['code'] = $params['code'] ? $params['code'] : '';
        $this->assign('code',$params['code']);
        return $this->fetch('reg');
    }
}
