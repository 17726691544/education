<?php

namespace app\man\controller;

use app\common\controller\Base;
use app\man\model\Config;

class Setting extends Base
{
    protected $middleware = ['app\man\middleware\Auth'];

    public function index() {
        if ($this->request->isPost()) {
            $params = $this->getParams(['quota_price','qd_quota','qdtjr_rate','gd_quota']);
            $rule = [
                'quota_price' => 'require|float|>:0.01',
                'qd_quota' => 'require|integer|>=:1',
                'qdtjr_rate' => 'require|integer|between:0,100',
                'gd_quota' => 'require|integer|>=:1'
            ];
            $msg = [
                'quota_price' => '名额单价不小于0.01',
                'qd_quota' => '区代购买名额不小于1',
                'qdtjr_rate' => '区代推荐人奖励百分比在0-100之间',
                'gd_quota' => '个代购买名额不小于1'
            ];
            $r = $this->validate($params,$rule,$msg);
            if (true !== $r) {
                $this->error($r);
            }

            Config::update([
                'quota_price' => $params['quota_price'],
                'qd_quota' => $params['qd_quota'],
                'qdtjr_rate' => $params['qdtjr_rate'],
                'gd_quota' => $params['gd_quota']
            ],['id'=>1]);

            $this->success('修改成功');
        } else {
            $config = Config::find(1);
            $this->assign('config',$config);
            return $this->fetch('index');
        }
    }
}
