<?php

namespace app\man\model;

use think\Model;

class UserLogs extends Model
{
    protected $table = 'user_logs';

    /**
     * create_at_str
     * @param $value
     * @param $data
     * @return false|string
     */
    public function getCreateAtStrAttr($value,$data) {
        return date('Y-m-d H:i:s', $data['create_at']);
    }

    /**
     * type_str
     * @param $value
     * @param $data
     * @return mixed
     */
    public function getTypeStrAttr($value,$data) {
        $map = ['','提现','推荐区代','区代推荐人', '区代', '教学中心', '个代', '个代推荐人','解冻资金'];
        return $map[$data['type']];
    }

    /**
     * 关联用户表
     * @return \think\model\relation\BelongsTo
     */
    public function user() {
        return $this->belongsTo('User','user_id','id');
    }
}