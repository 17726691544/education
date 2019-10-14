<?php

namespace app\man\model;

use think\Model;

class WithdrawLogs extends Model
{
    protected $table = 'withdraw_logs';

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
     * status_str
     * @param $value
     * @param $data
     * @return mixed
     */
    public function getStatusStrAttr($value,$data) {
        $map = ['待审核','已通过','已拒绝'];
        return $map[$data['status']];
    }

    /**
     * 关联用户表
     * @return \think\model\relation\BelongsTo
     */
    public function user() {
        return $this->belongsTo('User','user_id','id');
    }
}