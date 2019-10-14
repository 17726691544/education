<?php

namespace app\man\model;

use think\Model;

class Teacher extends Model
{
    protected $table = 'teacher';

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
     * 关联用户
     * @return \think\model\relation\BelongsTo
     */
    public function user() {
        return $this->belongsTo('User','user_id','id');
    }

    /**
     * video_arr
     * @param $value
     * @param $data
     * @return string
     */
    public function getVideoArrAttr($value,$data) {
        return json_decode($data['videos']);
    }
}