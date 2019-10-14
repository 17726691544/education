<?php

namespace app\man\model;

use think\Model;

class User extends Model
{
    protected $table = 'user';

    /**
     * reg_at_str
     * @param $value
     * @param $data
     * @return false|string
     */
    public function getRegAtStrAttr($value,$data) {
        return date('Y-m-d H:i:s',$data['reg_at']);
    }

    /**
     * 推荐人
     * @return \think\model\relation\HasOne
     */
    public function inviter() {
        return $this->hasOne('User','parent_id','id');
    }
}