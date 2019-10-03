<?php


namespace app\api\controller;

use app\common\controller\Base;
use app\common\validate\PageV;

class Coursecase extends Base
{
    public  function getCourseList(){
        $params = $this->getParams(['page', 'pageNum']);
        (new PageV())->goChick($params);
    }
}