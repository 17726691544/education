<?php

namespace app\man\controller;

use app\common\controller\Base;
use app\man\model\Config;

class Setting extends Base
{
    protected $middleware = ['app\man\middleware\Auth'];

    /**
     * 平台设置
     * @return mixed
     */
    public function index() {
        if ($this->request->isPost()) {
            $params = $this->getParams(['quota_price','qd_quota','qdtjr_rate','gd_quota','tixian_less']);
            $rule = [
                'quota_price' => 'require|float|>=:0.01',
                'qd_quota' => 'require|integer|>=:1',
                'qdtjr_rate' => 'require|integer|between:0,100',
                'gd_quota' => 'require|integer|>=:1',
                'tixian_less' => 'require|float|>=:0.01',
            ];
            $msg = [
                'quota_price' => '名额单价不小于0.01',
                'qd_quota' => '区代购买名额不小于1',
                'qdtjr_rate' => '区代推荐人奖励百分比在0-100之间',
                'gd_quota' => '个代购买名额不小于1',
                'tixian_less' => '最低提现金额不小于0.01'
            ];
            $r = $this->validate($params,$rule,$msg);
            if (true !== $r) {
                $this->error($r);
            }

            Config::update([
                'quota_price' => $params['quota_price'],
                'qd_quota' => $params['qd_quota'],
                'qdtjr_rate' => $params['qdtjr_rate'],
                'gd_quota' => $params['gd_quota'],
                'tixian_less' => $params['tixian_less']
            ],['id'=>1]);

            $this->success('修改成功');
        } else {
            $config = Config::find(1);
            $this->assign('config',$config);
            return $this->fetch('index');
        }
    }

    /**
     * 规则说明
     * @return mixed
     */
    public function rule() {
        if ($this->request->isPost()) {
            $params = $this->getParams(['gd_rule','qd_rule']);
            $rule = [
                'gd_rule' => 'require|min:1',
                'qd_rule' => 'require|min:1'
            ];
            $msg = [
                'gd_rule' => '请设置个代加盟规则',
                'qd_rule' => '请设置区代加盟规则'
            ];
            $r = $this->validate($params,$rule,$msg);
            if (true !== $r) {
                return $this->jsonBack(1,$r);
            }

            $gdRule = json_decode($params['gd_rule'],true);
            $qdRule = json_decode($params['qd_rule'],true);
            if (!is_array($gdRule) || !is_array($qdRule) || empty($gdRule) || empty($qdRule)) {
                return $this->jsonBack(2,'请设置加盟规则');
            }

            Config::update([
                'gd_rule' => $params['gd_rule'],
                'qd_rule' => $params['qd_rule']
            ],['id'=>1]);

            return $this->jsonBack(0,'设置成功');
        } else {
            $config = Config::get(1);
            $this->assign('config',$config);
            return $this->fetch('rule');
        }
    }
}
