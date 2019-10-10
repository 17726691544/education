<?php
namespace app\index\controller;

use app\common\model\User;
class Index
{
    public function index()
    {

        /*$tel = 13000000002;
        $pwd = '123456';
        $now = time();
        User::create(
            [
                'tel' => $tel,
                'pass' => md5($now . $pwd),
                'safe_pass' => md5($now . $pwd),
                'parent_id' => 0,
                'u_type' => 2,
                'reg_at' => $now,
                'invite_code'=>$tel
            ]
        );*/

        return 'ok';
    }


}
