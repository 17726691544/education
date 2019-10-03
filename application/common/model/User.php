<?php

namespace app\common\model;

use app\api\common\JwtAuth;
use app\common\exception\BusinessBaseException;
use http\Exception\RuntimeException;
use think\Model;

class User extends Model
{
    protected $table = 'user';
    protected $hidden = ['pass', 'safe_pass', 'parent_id', 'u_type'];
    protected $type = [
        'reg_at' => 'timestamp'
    ];

    public function getLevelAttr($value)
    {
        $status = [1 => '普通会员'];
        return $status[$value];
    }


    public static function findByTel($tel)
    {
        return self::where('tel', $tel)->find();
    }

    public static function findByInviteCode($inviteCode)
    {
        return self::where('invite_code', $inviteCode)->find();
    }

    public function editByUId($uid, $editVal = [])
    {
        return $this->save($editVal, ['id' => $uid]);
    }

    public function editPass($uid, $pass, $newpass, $type)
    {
        $user = self::get($uid)->find();
        if (!$user) {
            throw  new BusinessBaseException('用户不存在');
        }
        $md5Pass = md5($user->tel . $pass);
        if ($type === 1) {
            $dbPass = $user->pass;
        } else {
            $dbPass = $user->safe_pass;
        }
        if ($md5Pass !== $dbPass) {
            throw new BusinessBaseException('原始密码错误');
        }

        if ($type === 1) {
            return $this->editByUId($uid, ['pass' => md5($user->tel . $newpass)]);
        } else {
            return $this->editByUId($uid, ['safe_pass' => md5($user->tel . $newpass)]);
        }
    }

}