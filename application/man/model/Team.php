<?php

namespace app\man\model;

use think\Model;

class Team extends Model
{
    protected $table = 'team';

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
     * name_tip
     * @param $value
     * @param $data
     * @return string
     */
    public function getNameTipAttr($value,$data) {
        if (mb_strlen($data['name']) <= 10) {
            return $data['name'];
        }
        return mb_substr($data['name'],0,10) . '...';
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
     * images_arr
     * @param $value
     * @param $data
     * @return mixed
     */
    public function getImagesArrAttr($value,$data) {
        return json_decode(htmlspecialchars_decode($data['images']),true);
    }
}