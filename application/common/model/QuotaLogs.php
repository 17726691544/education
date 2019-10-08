<?php


namespace app\common\model;


use think\Model;

class QuotaLogs extends Model
{
    protected $table = 'quota_logs';

    protected $type = [
        'create_at' => 'timestamp'
    ];

    public function user()
    {
        return $this->belongsTo('User', 'to', 'id');
    }

    public function getTransferRecordList($uid, $page, $pageNum)
    {
        return self::with(['user' => function ($query) {
            $query->field(['nick', 'invite_code', 'head_url', 'id']);
        }])
            ->where('from', $uid)//
            ->field(['id', 'num', 'create_at', 'to'])
            ->hidden(['to', 'user.id'])
            ->order('id','desc')
            ->paginate($pageNum, false, [
                'page' => $page
            ]);
    }
}