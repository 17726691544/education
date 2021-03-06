<?php


namespace app\api\service;


use app\api\common\AliSms;
use app\common\enum\CodeCaheEnum;
use app\common\exception\BusinessBaseException;
use app\common\model\CodeCahe;

/**
 * 短信服务类
 * Class SmsService
 * @package app\api\service
 */
class SmsService
{


    /**
     * 获取验证码
     * @param $tel
     * @param $sendType
     * @param $codeType
     * @return bool
     * @throws BusinessBaseException
     * @throws \think\Exception
     */
    public function getCode($tel, $sendType, $codeType)
    {
        //1:获取当前电话号码信息
        $codeCahe = CodeCahe::findByWhere($tel, $sendType, $codeType);
        $randCode = $this->randCode();
        $now = time();

        if (!$codeCahe) {
            $codeCahe = new CodeCahe();
            $codeCahe->save([
                'send_type' => $sendType,
                'send_to' => $tel,
                'code_type' => $codeType,
                'code_content' => $randCode,
                'expire_time' => $now + 300,
                'gap_time' => 60,
                'count_day' => 1,
                'send_at' => $now
            ]);
        } else {
            if ($codeCahe->send_at < strtotime('today')) {
                $codeCahe->code_content = $randCode;
                $codeCahe->send_at = $now;
                $codeCahe->expire_time = $now + 300;
                $codeCahe->count_day = 1;
                $codeCahe->save();
            } else {
                if ($now - $codeCahe->send_at <= $codeCahe->gap_time) {
                    throw new BusinessBaseException('发送太频繁');
                }

                if ($codeCahe->count_day >= 30) {
                    throw new BusinessBaseException('获取验证码超过每日请求限制30次');
                }

                $codeCahe->code_content = $randCode;
                $codeCahe->send_at = $now;
                $codeCahe->expire_time = $now + 300;
                $codeCahe->count_day += 1;
                $codeCahe->save();
            }
        }
        //发送
        if($sendType === '1'){
            return AliSms::instance()->send($tel, $randCode);
        }else{
            throw new BusinessBaseException('暂不支持的发送类型');
        }

    }


    /**
     * 生成验证码
     * @return string
     */
    private function randCode()
    {
        $code = '';
        for ($i = 0; $i < 6; $i++) {
            $code .= mt_rand(0, 9);
        }
        return $code;
    }

}