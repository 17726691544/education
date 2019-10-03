<?php

namespace app\man\model;

use think\Model;

class Admin extends Model
{
    protected $table = 'admin';

    /**
     * 密码校验
     * @param $admin
     * @param $input
     * @return bool
     */
    public static function checkPwd($admin,$input)
    {
        return md5($admin->reg_at . $input) === $admin->pwd;
    }

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
     * 关联access
     * @return \think\model\relation\BelongsToMany
     */
    public function access() {
        return $this->belongsToMany('Access','admin_access','access_id','admin_id');
    }
}