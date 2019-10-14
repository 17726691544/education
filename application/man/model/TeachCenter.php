<?php

namespace app\man\model;

use think\Model;

class TeachCenter extends Model
{
    protected $table = 'teach_center';

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
        $map = ['等待审核','审核通过','审核被拒','删除'];
        return $map[$data['status']];
    }

    /**
     * 关联用户表
     * @return \think\model\relation\BelongsTo
     */
    public function user() {
        return $this->belongsTo('User','agent_user_id','id');
    }
}