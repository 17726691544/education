<?php


namespace app\common\model;


use think\Model;

class WithdrawLogs extends Model
{
    protected $table = 'withdraw_logs';
    protected $type =[
      'create_at'=>'timestamp'
    ];

    public function getStatusAttr($value)
    {
        //状态：0、待审核，1、通过，2、拒绝
        $status = [0=>'审核中',1=>'通过',2=>'审核未通过'];
        return $status[$value];
    }

    public static function getWithdrawList($uid, $page, $pageNum)
    {
        return self::where('user_id',$uid)
            ->paginate($pageNum,false,[
               'page' =>$page
            ]);
    }
}