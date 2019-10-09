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

    /**
     * 关联查询
     * @return \think\model\relation\HasMany
     */
    public function user()
    {
        return $this->hasMany('User', 'parent_id', 'id');
    }

    /**
     * 关联查询
     * @return \think\model\relation\HasMany
     */
    public function attendClassRecords()
    {
        return $this->hasMany('AttendClassRecord', 'user_id', 'id');
    }

    public static function findByTel($tel)
    {
        return self::where('tel', $tel)->find();
    }

    public static function findByInviteCode($inviteCode)
    {
        return self::where('invite_code', $inviteCode)->visible(['id', 'nick', 'head_url', 'invite_code'])->find();
    }

    public function editByUId($uid, $editVal = [])
    {
        return $this->save($editVal, ['id' => $uid]);
    }

    public function editPass($uid, $pass, $newpass, $type)
    {
        $user = self::get($uid);
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

    public function getDirectPersonList($uid, $page, $pageNum)
    {
        return self::withCount(['user' => function ($query) {
            $query->where('is_gd', 0)
                ->where('is_qd', 0)
                ->where('is_teacher', 0);
            return 'totalVip';
        }])
            ->where('parent_id', $uid)//
            ->where('is_gd', 1)//
            ->order('id', 'desc')
            ->visible(['id', 'nick', 'is_qd', 'is_gd', 'is_teacher', 'invite_code', 'head_url', 'id_card', 'real_name', 'totalVip'])
            ->paginate($pageNum, false, [
                'page' => $page
            ]);
    }

    public function getVipUserList($uid, $page, $pageNum)
    {
        return self::with(['attendClassRecords' => function ($query) {
            $query->visible(['course_title', 'status']);
        }])->where('parent_id', $uid)//
        ->where('is_gd', 0)
            ->where('is_qd', 0)
            ->where('is_teacher', 0)
            ->order('id', 'desc')
            ->visible(['id', 'nick', 'is_qd', 'is_gd', 'is_teacher', 'invite_code', 'id_card', 'real_name', 'head_url'])
            ->paginate($pageNum, false, [
                'page' => $page
            ]);
    }
}