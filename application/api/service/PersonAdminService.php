<?php


namespace app\api\service;


use app\common\exception\BusinessBaseException;
use app\common\model\QuotaLogs;
use app\common\model\User;
use think\Db;

class PersonAdminService extends BaseService
{

    public function getDirectPersonList($uid, $page, $pageNum)
    {
        //判断是否有权限
        $this->hasPermission($uid);
        return (new User())->getDirectPersonList($uid, $page, $pageNum);
    }

    public function getVipUserList($uid, $page, $pageNum)
    {
        //判断是否有权限
        $this->hasPermission($uid);
        return (new User())->getVipUserList($uid, $page, $pageNum);
    }

    public function getDirectDetailList($uid, $personUserId, $page, $pageNum)
    {
        //判断是否有权限
        $this->hasPermission($uid);

        //1:判断这个用户是否与属于操作用户
        $user = User::get($personUserId);
        if ($user->parent_id !== $uid) {
            throw new BusinessBaseException('错误操作');
        }
        return (new User())->getVipUserList($user->id, $page, $pageNum);
    }

    public function getQuota($uid)
    {
        //判断是否有权限
        $this->hasPermission($uid);
        $user = User::get($uid);
        if (!$user) {
            throw  new BusinessBaseException('用户不存在');
        }
        return $user;
    }

    public function getUserInfo($uid, $inviteCode)
    {
        //判断是否有权限
        $this->hasPermission($uid);
        return User::findByInviteCode($inviteCode);

    }

    public function transferQuota($uid, $otherId, $num)
    {
        //判断是否有权限
        $user = $this->hasPermission($uid);
        //判断名额是否充足
        if ($user->quota < (int)$num) {
            throw new BusinessBaseException('可用名额不足');
        }
        Db::startTrans();
        try {
            //减少转出方的名额
            $result = Db::table('user')
                ->where('id', $uid)//
                ->dec('quota', $num)
                ->update();
            if ((int)$result < 1) {
                throw new BusinessBaseException('转出失败');
            }
            //收到方添加名额
            Db::table('user')
                ->where('id', $otherId)//
                ->inc('quota', $num)//
                ->update();

            //写入日志记录
            (new QuotaLogs())->save([
                'from' => $uid,
                'to' => $otherId,
                'num' => $num,
                'create_at' => time()
            ]);
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            return false;
        }
        return true;
    }


    public function getTransferRecordList($uid,$page,$pageNum)
    {
        //判断是否有权限
        $this->hasPermission($uid);
        return (new QuotaLogs())->getTransferRecordList($uid,$page,$pageNum);
    }
}