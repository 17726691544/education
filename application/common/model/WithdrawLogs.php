<?php


namespace app\common\model;


use think\Model;

class WithdrawLogs extends Model
{
    protected $table = 'withdraw_logs';
    protected $type = [
        'create_at' => 'timestamp'
    ];

    public function getDateMonthAttr($value, $data)
    {
        $creatAt = $data['create_at'];
        if (!empty($creatAt)) {
            return date('Y年m月', $creatAt);
        }
    }



    public function getStatus2Attr($value,$data)
    {
        $value = $data['status'];
        $status = [0 => '审核中', 1 => '通过', 2 => '审核未通过', 3 => '已转账'];
        return $status[$value];
    }

    public static function getWithdrawList($uid, $page, $pageNum)
    {
        return self::where('user_id', $uid)
            ->append(['date_month','status2'])
            ->order('id', 'desc')
            ->paginate($pageNum, false, [
                'page' => $page
            ]);
    }
}