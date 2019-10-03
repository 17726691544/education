<?php

namespace app\man\model;

use think\Model;

class Video extends Model
{
    protected $table = 'video';

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
}