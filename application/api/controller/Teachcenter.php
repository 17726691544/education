<?php


namespace app\api\controller;

use app\common\controller\Base;
use app\common\model\TeachCenter as TeachCenterModel;

/**
 * 教学中心controller
 * Class Teachcenter
 * @package app\api\controller
 */
class Teachcenter extends Base
{

    /**
     * 获取教育中心
     */
    public function getTeachCenterList()
    {
        $teachCenterList = TeachCenterModel::getTeachCenterList();
        if ($teachCenterList) {
            $allAddr = $teachCenterList->toArray();
            foreach ($allAddr as $key => $value) {
                $agentId = $allAddr[$key]['agent_id'];
                $id = $allAddr[$key]['id'];
                $name = $allAddr[$key]['name'];
                $agentGrop[$agentId]['province'] =$allAddr[$key]['province'];
                $agentGrop[$agentId]['city'] =$allAddr[$key]['city'];
                $agentGrop[$agentId]['country'] =$allAddr[$key]['country'];
                $agentGrop[$agentId]['teachCenter'][] = ['id' => $id, 'name' => $name];
            }
            foreach ($agentGrop as $key => $val) {
                $item = [];
                $item['name'] = $agentGrop[$key]['province'];
                $item['children']['name'] = $agentGrop[$key]['city'];
                $item['children']['children']['name'] = $agentGrop[$key]['country'];
                $item['children']['children']['children'] = $agentGrop[$key]['teachCenter'];
                $resltArr[] = $item;
            }
        }
        return $this->jsonBack(0, '成功', $resltArr ?? null);
    }
}