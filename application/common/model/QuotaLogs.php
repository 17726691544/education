<?php


namespace app\common\model;


use think\Model;

class QuotaLogs extends Model
{
    protected $table = 'quota_logs';

    protected $type = [
        'create_at'  =>  'timestamp'
    ];

    public function user(){
        return $this->belongsTo('User','to','id');
    }

    public function getTransferRecordList($uid,$page,$pageNum)
    {
        return self::with('user')
            ->where('from',$uid)//
                ->visible(['id','num','create_at','user'=>['nick','invite_code','head_url']])   //
            ->paginate($pageNum,false,[
                'page'=>$page
            ]);
    }
}