<?php

namespace app\api\controller;

use app\common\controller\Base;
use app\common\validate\BaseValidate;
use app\common\model\BankCard as BankCardModel;

class Bankcard extends Base
{
    /**
     * 添加银行卡
     * @return \think\response\Json
     * @throws \app\common\exception\BusinessBaseException
     */
    public function add() {
        (new BaseValidate())->tokenChick();
        $uid = $this->getUid();
        $params = $this->getParams(['bank','name','card']);
        $rule = [
            'bank' => 'require|min:1',
            'name' => 'require|min:1',
            'card' => 'require|min:1'
        ];
        $msg = [
            'bank' => '开户行不能为空',
            'name' => '持卡人姓名不能为空',
            'card' => '银行卡号不能为空'
        ];

        $r = $this->validate($params,$rule,$msg);
        if (true !== $r) {
            return $this->jsonBack(1,$r);
        }

        BankCardModel::create([
            'user_id' => $uid,
            'bank' => $params['bank'],
            'name' => $params['name'],
            'card' => $params['card'],
            'create_at' => time()
        ]);

        return $this->jsonBack(0,'添加成功');
    }

    /**
     * 删除银行卡
     * @return \think\response\Json
     * @throws \app\common\exception\BusinessBaseException
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function del() {
        (new BaseValidate())->tokenChick();
        $uid = $this->getUid();
        $params = $this->getParams(['id']);
        $rule = [
            'id' => 'require|integer|>:0'
        ];
        $msg = [
            'id' => '错误的操作'
        ];

        $r = $this->validate($params,$rule,$msg);
        if (true !== $r) {
            return $this->jsonBack(1,$r);
        }

        BankCardModel::where('id',$params['id'])->where('user_id',$uid)->delete();
        return $this->jsonBack(0,'删除成功');
    }
}