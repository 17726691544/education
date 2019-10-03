<?php

namespace app\common\model;

use think\Model;

class User extends Model
{
    protected $table = 'user';
    protected $hidden = ['pass','safe_pass','parent_id','u_type'];
    protected $type = [
        'reg_at'  =>  'timestamp'
    ];

    public function getLevelAttr($value)
    {
        $status = [1=>'普通会员'];
        return $status[$value];
    }


    public static function findByTel($tel){
       return self::where('tel',$tel)->find();
    }

    public static function findByInviteCode($inviteCode){
        return self::where('invite_code',$inviteCode)->find();
    }
}