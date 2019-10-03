<?php

namespace app\man\controller;

use app\common\model\Access;
use app\common\model\Admin;
use app\common\model\AdminAccess;
use app\common\model\Config;
use app\common\model\User as UserModel;

class Setting extends Base
{
    protected $middleware = ['app\man\middleware\Auth'];
    /**
     * 平台信息
     * @return mixed|\think\response\Json
     */
    public function platform() {
        if ($this->request->isPost()) {
            $params = $this->getParams(['name','logo','plate_1','plate_2','plate_3','plate_4','future_desc']);
            $rules = [
                'name' => 'require|min:1',
                'logo' => 'require|min:1',
                'plate_1' => 'require|min:1',
                'plate_2' => 'require|min:1',
                'plate_3' => 'require|min:1',
                'plate_4' => 'require|min:1'
            ];

            $msg = [
                'name' => '名称不能为空',
                'logo' => '请上传logo',
                'plate_1' => '板块名称不能为空',
                'plate_2' => '板块名称不能为空',
                'plate_3' => '板块名称不能为空',
                'plate_4' => '板块名称不能为空'
            ];
            $r = $this->validate($params, $rules, $msg);
            if (true !== $r) {
                return $this->jsonBack(1,$r);
            }

            Config::update([
                'name' => $params['name'],
                'logo' => $params['logo'],
                'plate_1' => $params['plate_1'],
                'plate_2' => $params['plate_2'],
                'plate_3' => $params['plate_3'],
                'plate_4' => $params['plate_4'],
                'future_desc' => $params['future_desc']
            ],['id'=>1]);

            return $this->jsonBack(0,'操作成功');

        } else {
            $info = Config::find(1);
            $data = [
                'name' => $info->name,
                'logo' => [
                    'path' => $info->logo,
                    'url' => $info->logo_url
                ],
                'plate_1' => $info->plate_1,
                'plate_2' => $info->plate_2,
                'plate_3' => $info->plate_3,
                'plate_4' => $info->plate_4,
                'future_desc' => $info->future_desc
            ];

            $this->assign('info',$data);
            return $this->fetch('platform');
        }
    }












    /**
     * 流失账户设置
     * @return mixed
     */
    public function lost() {
        if ($this->request->isPost()) {
            $params = $this->getParams(['code']);
            $rule = [
                'code' => 'require|min:1'
            ];
            $msg = [
                'code' => '错误的操作'
            ];

            $r = $this->validate($params,$rule,$msg);
            if (true !== $r) {
                $this->error($r);
            }

            if (!UserModel::isValidCode($params['code'])) {
                $this->error('不是有效的用户编号');
            }

            $id = UserModel::code2id($params['code']);
            $user = UserModel::find($id);
            if (!$user) {
                $this->error('用户不存在');
            }

            Config::where('id',1)->setField('lost_id',$id);
            $this->success('操作成功');
        } else {
            $id = Config::where('id',1)->value('lost_id');
            $user = UserModel::find($id);
            $code = '';
            if ($user) {
                $code = $user->ivt_code;
            }
            $this->assign('code',$code);
            return $this->fetch('lost');
        }
    }
}
