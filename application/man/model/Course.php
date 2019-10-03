<?php

namespace app\man\model;

use think\Model;

class Course extends Model
{
    protected $table = 'course';

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
     * title_tip
     * @param $value
     * @param $data
     * @return string
     */
    public function getTitleTipAttr($value,$data) {
        if (mb_strlen($data['title']) <= 10) {
            return $data['title'];
        }
        return mb_substr($data['title'],0,10) . '...';
    }

    /**
     * tip_tip
     * @param $value
     * @param $data
     * @return string
     */
    public function getTipTipAttr($value,$data) {
        if (mb_strlen($data['tip']) <= 10) {
            return $data['tip'];
        }
        return mb_substr($data['tip'],0,10) . '...';
    }

    /**
     * 关联CourseItem
     * @return \think\model\relation\HasMany
     */
    public function items() {
        return $this->hasMany('CourseItem','course_id','id');
    }
}