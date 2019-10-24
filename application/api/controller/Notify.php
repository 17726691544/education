<?php


namespace app\api\controller;

use app\common\controller\Base;
use app\common\exception\BusinessBaseException;
use app\common\model\Agent;
use app\common\model\AgentOther;
use app\common\model\AttendClassRecord;
use app\common\model\CenterLogs;
use app\common\model\City;
use app\common\model\Country;
use app\common\model\Orders;
use app\common\model\OrdersOther;
use app\common\model\OrdersOtherLoss;
use app\common\model\Province;
use app\common\model\QuotaOrders;
use app\common\model\User as UserModel;
use app\common\model\Config as ConfigModel;
use app\common\model\UserBalance;
use app\common\model\UserBalanceOther;
use app\common\model\UserLogs;
use app\common\model\Course as CourseModel;
use app\common\model\TeachCenter as TeachCenterModel;
use app\common\service\WxService;
use think\Db;

class Notify extends Base
{
    /**
     * 微信支付回调
     * @return string
     */
    public function wx()
    {
        $input = file_get_contents("php://input");
        $service = new WxService();
        $info = $service->xml2array($input);
        //$info = '{"out_trade_no":"F2875331572","appid":"wxd90cb1ea6f277822","bank_type":"CFT","cash_fee":"1","fee_type":"CNY","is_subscribe":"N","mch_id":"1551669691","nonce_str":"1HkCscuOferj6Le2KMhFHLQvzk2gfkVA","openid":"o2YH74ltilkSx7YItGcc9G3vvGjg","result_code":"SUCCESS","return_code":"SUCCESS","sign":"0563C0CCD962A10C6736BA153292E8A9","time_end":"20190823140257","total_fee":"1","trade_type":"JSAPI","transaction_id":"4200000345201908236847096049"}';
        if (!is_array($info)) {
            return $service->wxFail();
        }
        //验证签名
        $preSign = $info['sign'];
        unset($info['sign']);
        $sign = $service->sign($info);
        if ($preSign !== $sign) {
            return $service->wxFail();
        }

        $r = $this->dealOrder($info['out_trade_no'], 1);

        if (true !== $r) {
            file_put_contents('wx.log', "{$info['openid']}---{{$info['out_trade_no']}}---{$r}" . PHP_EOL, FILE_APPEND);
            return $service->wxFail();
        }

        return $service->wxSuccess();
    }

    public function ali()
    {

    }

    public function testDealOrder()
    {
        $orderNo = $this->request->param('orderId');
        $orderNo = 'E' . ($orderNo + 1370178326);
        $this->dealOrder($orderNo, 1);
        return 'ok';

    }

    private function dealOrder($orderNo, $payType)
    {

        if (false !== $id = Orders::orderNo2Id($orderNo)) {
            Db::startTrans();
            try {
                $order = Orders::where('id', $id)->lock(true)->find();
                if ($order->status !== 0) throw $this->createError('订单已处理');
                $now = time();
                $order->pay_type = $payType;
                $order->pay_at = $now;
                $order->status = 1;
                $order->save();

                $course = CourseModel::get($order->course_id);
                $center = TeachCenterModel::get($order->center_id);
                $attend = AttendClassRecord::create([
                    'agent_id' => $center->agent_id,
                    'center_id' => $order->center_id,
                    'user_id' => $order->user_id,
                    'course_id' => $order->course_id,
                    'course_title' => $course->title,
                    'grade' => $order->grade,
                    'create_at' => $now
                ]);

                $user = UserModel::get($order->user_id);


                if ($user->parent_id !== 0) {
                    //个代
                    $uParent = UserModel::get($user->parent_id);
                    while (true) {
                        if ($uParent->is_gd === 1 || $uParent->is_qd === 1) break;
                        if ($uParent->parent_id === 0) break;
                        $uParent = UserModel::get($uParent->parent_id);
                    }

                    if (($uParent->is_gd === 1 || $uParent->is_qd === 1) && $uParent->quota > 0) {
                        UserLogs::create([
                            'user_id' => $uParent->id,
                            'num' => $course->grdl,
                            'tip' => "用户[{$user->invite_code}]购买课程",
                            'type' => 6,
                            'create_at' => $now
                        ]);

                        UserBalance::create([
                            'user_id' => $uParent->id,
                            'attend_id' => $attend->id,
                            'lock_balance' => $course->grdl,
                            'create_at' => $now
                        ]);

                        UserModel::where('id', $uParent->id)->setInc('lock_balance', $course->grdl);
                        UserModel::where('id', $uParent->id)->setDec('quota', 1);
                    }

                    //个代推荐人
                    if (($uParent->is_gd === 1 || $uParent->is_qd === 1) && $uParent->parent_id !== 0) {
                        $u2Parent = UserModel::get($uParent->parent_id);
                        while (true) {
                            if ($u2Parent->is_gd === 1 || $u2Parent->is_qd === 1) break;
                            if ($u2Parent->parent_id === 0) break;
                            $u2Parent = UserModel::get($u2Parent->parent_id);
                        }

                        if ($u2Parent->is_gd === 1 || $u2Parent->is_qd === 1) {
                            UserLogs::create([
                                'user_id' => $u2Parent->id,
                                'num' => $course->gdtjr,
                                'tip' => "用户[{$user->invite_code}]购买课程",
                                'type' => 7,
                                'create_at' => $now
                            ]);

                            UserBalance::create([
                                'user_id' => $u2Parent->id,
                                'attend_id' => $attend->id,
                                'lock_balance' => $course->gdtjr,
                                'create_at' => $now
                            ]);

                            UserModel::where('id', $u2Parent->id)->setInc('lock_balance', $course->gdtjr);
                        }
                    }
                }

                //区代
                $uCenter = UserModel::get($center->agent_user_id);

                UserLogs::create([
                    'user_id' => $center->agent_user_id,
                    'num' => $course->qydl,
                    'tip' => "用户[{$user->invite_code}]购买课程",
                    'type' => 4,
                    'create_at' => $now
                ]);

                UserBalance::create([
                    'user_id' => $center->agent_user_id,
                    'attend_id' => $attend->id,
                    'lock_balance' => $course->qydl,
                    'create_at' => $now
                ]);

                UserModel::where('id', $center->agent_user_id)->setInc('lock_balance', $course->qydl);
                //区代推荐人
                if ($uCenter->parent_id !== 0) {
                    $centerParent = UserModel::get($uCenter->parent_id);

                    if ($centerParent->is_gd === 1 || $centerParent->is_qd === 1) {
                        UserLogs::create([
                            'user_id' => $centerParent->id,
                            'num' => $course->qdtjr,
                            'tip' => "用户[{$user->invite_code}]购买课程",
                            'type' => 3,
                            'create_at' => $now
                        ]);

                        UserBalance::create([
                            'user_id' => $centerParent->id,
                            'attend_id' => $attend->id,
                            'lock_balance' => $course->qdtjr,
                            'create_at' => $now
                        ]);

                        UserModel::where('id', $centerParent->id)->setInc('lock_balance', $course->qdtjr);
                    }
                }

                //教学中心
                UserLogs::create([
                    'user_id' => $center->agent_user_id,
                    'num' => $course->jxzx,
                    'tip' => "用户[{$user->invite_code}]购买课程",
                    'type' => 5,
                    'create_at' => $now
                ]);

                UserBalance::create([
                    'user_id' => $center->agent_user_id,
                    'attend_id' => $attend->id,
                    'lock_balance' => $course->jxzx,
                    'create_at' => $now
                ]);

                CenterLogs::create([
                    'center_id' => $center->id,
                    'center_name' => $center->name,
                    'agent_id' => $center->agent_id,
                    'agent_user_id' => $center->agent_user_id,
                    'num' => $course->jxzx,
                    'tip' => "用户[{$user->invite_code}]购买课程",
                    'create_at' => $now
                ]);

                UserModel::where('id', $center->agent_user_id)->setInc('lock_balance', $course->jxzx);

                Db::commit();
                return true;
            } catch (\Exception $e) {
                Db::rollback();
                return $e->getMessage();
            }

        } else if (false !== $id = QuotaOrders::orderNo2Id($orderNo)) {
            Db::startTrans();
            try {
                $order = QuotaOrders::where('id', $id)->lock(true)->find();
                if ($order->status !== 0) throw $this->createError('订单已处理');
                $now = time();
                $order->pay_type = $payType;
                $order->pay_at = $now;
                $order->status = 1;
                $order->save();

                if ($order->type === 0) {
                    //普通名额购买
                    UserModel::where('id', $order->user_id)->setInc('quota', $order->quota_num);
                } elseif ($order->type === 1) {
                    //个代
                    UserModel::where('id', $order->user_id)->setInc('quota', $order->quota_num);
                    UserModel::where('id', $order->user_id)->setField('is_gd', 1);
                } else {
                    //区代
                    $user = UserModel::get($order->user_id);
                    $user->is_qd = 1;
                    $user->save();
                    UserModel::where('id', $order->user_id)->setInc('quota', $order->quota_num);

                    if ($user->parent_id !== 0) {
                        $config = ConfigModel::get(1);
                        $qudaParent = UserModel::get($user->parent_id);
                        if ($qudaParent->is_qd === 1 || $qudaParent->is_gd === 1) {
                            $rewardNum = round($order->total * $config->qdtjr_rate / 100, 2);
                            UserLogs::create([
                                'user_id' => $user->parent_id,
                                'num' => $rewardNum,
                                'tip' => "用户[{$user->invite_code}]成为区域代理",
                                'type' => 2,
                                'create_at' => $now
                            ]);
                            UserModel::where('id', $order->user_id)->setInc('balance', $rewardNum);
                        }
                    }

                    $area = explode(',', $order->pcc);
                    $province = Province::get($area[0]);
                    $city = City::get($area[1]);
                    $country = Country::get($area[2]);

                    Agent::create([
                        'user_id' => $order->user_id,
                        'province_id' => $province->id,
                        'city_id' => $city->id,
                        'country_id' => $country->id,
                        'province' => $province->name,
                        'city' => $city->name,
                        'country' => $country->name,
                        'create_at' => $now
                    ]);
                }

                Db::commit();
                return true;
            } catch (\Exception $e) {
                Db::rollback();
                return $e->getMessage();
            }

        } elseif (false !== $id = OrdersOther::orderNo2Id($orderNo)) {
            Db::startTrans();
            try {
                $order = OrdersOther::where('id', $id)->lock(true)->find();
                if ($order->status !== 0) throw $this->createError('订单已处理');
                $now = time();
                $order->pay_type = $payType;
                $order->pay_at = $now;
                $order->status = 1;
                $order->save();

                //处理代理关系
                if ($order->apply_status === 2 && !empty($order->dl_pcc)) {
                    //区代
                    $dl_pcc_arry = explode(',', $order->dl_pcc);
                    if (count($dl_pcc_arry) !== 3) {
                        throw $this->createError('订单处理失败');
                    }
                    $province = Province::get($dl_pcc_arry[0]);
                    $city = City::get($dl_pcc_arry[1]);
                    $country = Country::get($dl_pcc_arry[2]);
                    //插入代理表
                    AgentOther::create([
                        'user_id' => $order->user_id,
                        'province_id' => $dl_pcc_arry[0],
                        'city_id' => $dl_pcc_arry[1],
                        'country_id' => $dl_pcc_arry[2],
                        'province' => $province->name,
                        'city' => $city->name,
                        'country' => $country->name,
                        'create_at' => $now
                    ]);
                    UserModel::where('id', $order->user_id)->setField('is_ej_qd', 1);
                } elseif ($order->apply_status === 1) {
                    //个代
                    UserModel::where('id', $order->user_id)->setField('is_ej_gd', 1);
                }

                //奖励关系处理
                $user = UserModel::get($order->user_id);
                $course = CourseModel::get($order->course_id);
                if ($user->is_ej_gd === 1) {
                    //寻找父级第一个区代
                    $uParent = UserModel::get($user->parent_id);
                    $qdUParent = null;
                    while (true) {
                        if (!$uParent) break;
                        if ($uParent->is_ej_qd === 1) {
                            $qdUParent = $uParent;
                            break;
                        }
                        if ($uParent->parent_id === 0) break;
                        $uParent = UserModel::get($uParent->parent_id);
                    }
                    //奖励
                    $awardBalance = ($order->price - ($course->qd_price)) * ($order->num);
                    if ($qdUParent) {
                        UserBalanceOther::create([
                            'user_id' => $qdUParent->id,
                            'order_other_id' => $order->id,
                            'lock_balance' => $awardBalance,
                            'create_at' => $now
                        ]);
                        //增加当前用户锁定余额
                        UserModel::where('id', $qdUParent->id)->setInc('lock_balance', $awardBalance);
                    } else {
                        //奖励流失,插入流失记录
                        OrdersOtherLoss::create([
                            'user_id' => $user->id,
                            'orders_otherid' => $order->id,
                            'loss_balance' => $awardBalance,
                            'create_at' => $now
                        ]);
                    }
                } elseif ($user->is_ej_qd === 0 && $user->is_ej_gd === 0) {
                    //寻找父级第一个 个代 和区代
                    $uParent = UserModel::get($user->parent_id);
                    $gdUParent = null;
                    $qdUParent = null;
                    while (true) {
                        if (!$uParent) break;
                        if ($uParent->is_ej_gd === 1) {
                            if (!$gdUParent) {
                                $gdUParent = $uParent;
                            }
                        }
                        if ($uParent->is_ej_qd === 1) {
                            if (!$qdUParent) {
                                $qdUParent = $uParent;
                            }
                        }
                        if ($gdUParent && $qdUParent) {
                            if ($gdUParent->id === $qdUParent->id) {
                                $qdUParent = null;
                            } else {
                                break;
                            }
                        }
                        if ($uParent->parent_id === 0) break;
                        $uParent = UserModel::get($uParent->parent_id);
                    }
                    $lossBalance = 0;
                    //奖励个代
                    $gdAwardBalance = ($order->price - ($course->gd_price)) * ($order->num);
                    if ($gdUParent) {
                        UserBalanceOther::create([
                            'user_id' => $gdUParent->id,
                            'order_other_id' => $order->id,
                            'lock_balance' => $gdAwardBalance,
                            'create_at' => $now
                        ]);
                        //增加当前用户锁定余额
                        UserModel::where('id', $gdUParent->id)->setInc('lock_balance', $gdAwardBalance);
                    } else {
                        $lossBalance += $gdAwardBalance;
                    }
                    //奖励区代
                    $qdAwardBalance = ($order->price - ($course->qd_price)) * ($order->num) - $gdAwardBalance;
                    if ($qdUParent) {
                        UserBalanceOther::create([
                            'user_id' => $qdUParent->id,
                            'order_other_id' => $order->id,
                            'lock_balance' => $qdAwardBalance,
                            'create_at' => $now
                        ]);
                        //增加当前用户锁定余额
                        UserModel::where('id', $qdUParent->id)->setInc('lock_balance', $qdAwardBalance);
                    } else {
                        $lossBalance += $qdAwardBalance;
                    }
                    //写入流失记录
                    if ($lossBalance > 0) {
                        OrdersOtherLoss::create([
                            'user_id' => $user->id,
                            'orders_otherid' => $order->id,
                            'loss_balance' => $lossBalance,
                            'create_at' => $now
                        ]);
                    }

                }

                Db::commit();
                return true;
            } catch (\Exception $e) {
                Db::rollback();
                return $e->getMessage();
            }
        } else {
            return false;
        }

    }

    public function testPay()
    {
        $list = OrdersOther::where('status', 0)->select();
        foreach ($list as $item) {
            $r = $this->dealOrder($item->order_number, 1);
            dump($r);
        }
        return 'ok';
    }
}