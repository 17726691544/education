<?php


namespace app\common\model;


use think\Model;

class UserLogs extends Model
{
    protected $table = 'user_logs';

    protected $type = [
        'create_at' => 'timestamp'
    ];

    public function getTypeAttr($value)
    {
        if (!empty($value)) {
            $types = [1 => '提现', 2 => '推荐区代', 3 => '区代推荐人', 4 => '区代', 5 => '教学中心', 6 => '个代', 7 => '个代推荐人'];
            return $types[$value];
        }

    }

    public static function getBalanceDetail($uid, $page, $pageNum)
    {
        return self::where('user_id', $uid)
            ->field(['num', 'tip', 'type', 'create_at'])
            ->paginate($pageNum, false, [
                'page' => $page
            ]);

    }

    public static function getWithdrawList($uid, $page, $pageNum)
    {

        return self::where('user_id', $uid)
            ->where('type', 1)
            ->field(['id', 'num', 'tip', 'create_at'])
            ->paginate($pageNum, false, [
                'page' => $page
            ]);
    }
}