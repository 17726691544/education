<?php


namespace app\common\model;


use think\Model;

class UserLogs extends Model
{
    protected $table = 'user_logs';

    protected $type = [
      'create_at' =>'timestamp'
    ];

    public static function getBalanceDetail($uid, $page, $pageNum)
    {
        return self::where('user_id', $uid)
            ->field(['num', 'tip', 'type', 'create_at'])
            ->paginate($pageNum, false, [
                'page' => $page
            ]);

    }

    public static function getWithdrawList($uid,$page,$pageNum){

        return self::where('user_id',$uid)
            ->where('type',1)
            ->field(['id','num','tip','create_at'])
            ->paginate($pageNum,false,[
                'page'=>$page
            ]);
    }
}