<?php


namespace app\common\model;


use think\Model;

class CenterLogs extends Model
{

    protected $table = 'center_logs';

    protected $type = [
        'create_at' => 'timestamp'
    ];

    public function getDateMonthAttr($value, $data)
    {
        $createAt = $data['create_at'];
        if (!empty($createAt)) {
            return date('Y-m-d H:i', $createAt);
        }
    }

    public static function getEarningList($uid, $agentId, $page, $pageNum)
    {
        return CenterLogs::where('agent_user_id', $uid)
            ->where('agent_id', $agentId)
            ->append(['date_month'])
            ->order('id', 'desc')
            ->paginate($pageNum, false, [
                'page' => $page
            ]);
    }

    public static function getCenterEarningList($uid, $agentId, $centerId, $page, $pageNum)
    {
        return CenterLogs::where('agent_user_id', $uid)
            ->where('agent_id', $agentId)
            ->where('center_id', $centerId)
            ->append(['date_month'])
            ->order('id', 'desc')
            ->paginate($pageNum, false, [
                'page' => $page
            ]);
    }
}