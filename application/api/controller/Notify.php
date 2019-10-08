<?php


namespace app\api\controller;

use app\common\controller\Base;
use app\common\model\Agent;
use app\common\model\AttendClassRecord;
use app\common\model\CenterLogs;
use app\common\model\City;
use app\common\model\Country;
use app\common\model\Orders;
use app\common\model\Province;
use app\common\model\QuotaOrders;
use app\common\model\User as UserModel;
use app\common\model\Config as ConfigModel;
use app\common\model\UserBalance;
use app\common\model\UserLogs;
use app\common\model\Course as CourseModel;
use app\common\model\TeachCenter as TeachCenterModel;
use think\Db;

class Notify extends Base
{
    public function wx() {
        $orders = Orders::where('status',0)->select();
        foreach ($orders as $order) {
            $this->dealOrder($order->order_number,1);
        }

        $orders = QuotaOrders::where('status',0)->select();
        foreach ($orders as $order) {
            $this->dealOrder($order->order_number,1);
        }

        return 'ok';
    }

    public function ali() {

    }

    private function dealOrder($orderNo,$payType) {

        if (false !== $id = Orders::orderNo2Id($orderNo)) {
            Db::startTrans();
            try {
                $order = Orders::where('id',$id)->lock(true)->find();
                if ($order->status !== 0) throw $this->createError('订单已处理');
                $now = time();
                $order->pay_type = $payType;
                $order->pay_at = $now;
                $order->status = 1;
                $order->save();

                $course = CourseModel::get($order->course_id);
                $center = TeachCenterModel::get($order->center_id);
                AttendClassRecord::create([
                    'agent_id' => $center->agent_id,
                    'center_id' => $order->center_id,
                    'user_id' => $order->user_id,
                    'course_id' => $order->course_id,
                    'course_title' => $course->title,
                    'grade' => $order->grade,
                    'create_at' => $now
                ]);

                $user = UserModel::get($order->user_id);
                //个代
                if ($user->parent_id !== 0) {
                    $uParent = UserModel::get($user->parent_id);
                    while (true) {
                        if ($uParent->is_gd === 1) break;
                        if ($uParent->parent_id === 0) break;
                        $uParent = UserModel::get($uParent->parent_id);
                    }

                    if ($uParent->is_gd === 1) {
                        UserLogs::create([
                            'user_id' => $user->parent_id,
                            'num' => $course->grdl,
                            'tip' => "用户[{$user->invite_code}]购买课程",
                            'type' => 6,
                            'create_at' => $now
                        ]);

                        UserBalance::create([
                            'user_id' => $user->parent_id,
                            'lock_balance' => $course->grdl,
                            'create_at' => $now
                        ]);

                        UserModel::where('id',$user->parent_id)->setInc('lock_balance',$course->grdl);

                        //个代推荐人
                        if ($uParent->parent_id !== 0) {
                            //$u2Parent = UserModel::get($uParent->parent_id);

                            UserLogs::create([
                                'user_id' => $uParent->parent_id,
                                'num' => $course->gdtjr,
                                'tip' => "用户[{$user->invite_code}]购买课程",
                                'type' => 7,
                                'create_at' => $now
                            ]);

                            UserBalance::create([
                                'user_id' => $uParent->parent_id,
                                'lock_balance' => $course->gdtjr,
                                'create_at' => $now
                            ]);

                            UserModel::where('id',$uParent->parent_id)->setInc('lock_balance',$course->gdtjr);
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
                    'lock_balance' => $course->qydl,
                    'create_at' => $now
                ]);

                UserModel::where('id',$center->agent_user_id)->setInc('lock_balance',$course->qydl);
                //区代推荐人
                if ($uCenter->parent_id !== 0) {
                    UserLogs::create([
                        'user_id' => $uCenter->parent_id,
                        'num' => $course->qdtjr,
                        'tip' => "用户[{$user->invite_code}]购买课程",
                        'type' => 3,
                        'create_at' => $now
                    ]);

                    UserBalance::create([
                        'user_id' => $uCenter->parent_id,
                        'lock_balance' => $course->qdtjr,
                        'create_at' => $now
                    ]);

                    UserModel::where('id',$uCenter->parent_id)->setInc('lock_balance',$course->qdtjr);
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
                    'lock_balance' => $course->jxzx,
                    'create_at' => $now
                ]);

                CenterLogs::create([
                    'center_id' => $center->id,
                    'agent_id' => $center->agent_id,
                    'agent_user_id' => $center->agent_user_id,
                    'num' => $course->jxzx,
                    'tip' => "用户[{$user->invite_code}]购买课程",
                    'create_at' => $now
                ]);

                UserModel::where('id',$center->agent_user_id)->setInc('lock_balance',$course->jxzx);

                Db::commit();
                return true;
            } catch (\Exception $e) {
                Db::rollback();
                return $e->getMessage();
            }

        } else if (false !== $id = QuotaOrders::orderNo2Id($orderNo)) {
            Db::startTrans();
            try {
                $order = QuotaOrders::where('id',$id)->lock(true)->find();
                if ($order->status !== 0) throw $this->createError('订单已处理');
                $now = time();
                $order->pay_type = $payType;
                $order->pay_at = $now;
                $order->status = 1;
                $order->save();

                if ($order->type === 0) {
                    //普通名额购买
                    UserModel::where('id',$order->user_id)->setInc('quota',$order->quota_num);
                } elseif ($order->type === 1) {
                    //个代
                    UserModel::where('id',$order->user_id)->setInc('quota',$order->quota_num);
                    UserModel::where('id',$order->user_id)->setField('is_gd',1);
                } else {
                    //区代
                    $user = UserModel::get($order->user_id);
                    $user->is_qd = 1;
                    $user->save();
                    UserModel::where('id',$order->user_id)->setInc('quota',$order->quota_num);

                    if ($user->parent_id !== 0) {
                        $config = ConfigModel::get(1);
                        $rewardNum = round($order->total * $config->qdtjr_rate / 100,2);
                        UserLogs::create([
                            'user_id' => $user->parent_id,
                            'num' => $rewardNum,
                            'tip' => "用户[{$user->invite_code}]成为区域代理",
                            'type' => 2,
                            'create_at' => $now
                        ]);
                        UserModel::where('id',$order->user_id)->setInc('balance',$rewardNum);
                    }

                    $area = explode(',',$order->pcc);
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

        } else {
            return false;
        }

    }
}