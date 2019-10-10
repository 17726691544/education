<?php


namespace app\api\service;


use app\common\exception\BusinessBaseException;
use app\common\model\QuotaLogs;
use app\common\model\User;
use think\Db;

class QuotaadminService extends BaseService
{
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

    public function transferQuota($uid, $otherId, $num ,$safePass)
    {
        //判断是否有权限
        $user = $this->hasPermission($uid);
        //判断安全密码是否正确
        $md5Pass = md5($user->reg_at . $safePass);
        if ($md5Pass !== $user->safe_pass) {
            throw new BusinessBaseException('安全密码错误');
        }

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


    public function getTransferRecordList($uid, $page, $pageNum)
    {
        //判断是否有权限
        $this->hasPermission($uid);
        return (new QuotaLogs())->getTransferRecordList($uid, $page, $pageNum);
    }


    protected function hasPermission($uid)
    {
        $user = User::get($uid);
        if(!$user){
            throw new BusinessBaseException('错误操作');
        }
        if($user->is_gd !== 1 && $user->is_qd !==1 ){
            throw new BusinessBaseException('你没有权限做此操作');
        }
        return $user;
    }
}