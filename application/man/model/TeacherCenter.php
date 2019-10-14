<?php

namespace app\man\model;

use think\Model;

class TeacherCenter extends Model
{
    protected $table = 'teacher_center';

    /**
     * 关联teach_center
     * @return \think\model\relation\BelongsTo
     */
    public function center() {
        return $this->belongsTo('TeachCenter','center_id','id');
    }
}