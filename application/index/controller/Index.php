<?php

namespace app\index\controller;

use app\common\controller\Base;

class Index extends Base
{
    public function index()
    {
        return 'ok';
    }

    public function reg() {
        return $this->fetch('reg');
    }
}
