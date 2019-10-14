<?php

namespace app\man\model;

use think\Model;

class Access extends Model
{
    protected $table = 'access';

    /**
     * create_at_str
     * @param $value
     * @param $data
     * @return false|string
     */
    public function getCreateAtStrAttr($value,$data) {
        return date('Y-m-d H:i:s',$data['create_at']);
    }
}