<?php


namespace app\api\common;

use AlibabaCloud\Client\AlibabaCloud;
use think\Exception;


class AliSms
{
    private static $aliSms;

    private function __construct()
    {
    }

    private function __clone()
    {
    }


    public static function instance()
    {
        if (is_null(self::$aliSms)) {
            self::$aliSms = new self();
        }
        return self::$aliSms;
    }

    public function send($phoneNums, $content)
    {

        AlibabaCloud::accessKeyClient(config('sms.access_key_id'), config('sms.access_key_secret'))
            ->regionId('cn-hangzhou')
            ->asDefaultClient();

        try {
            $result = AlibabaCloud::rpc()
                ->product('Dysmsapi')
                // ->scheme('https') // https | http
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->host('dysmsapi.aliyuncs.com')
                ->options([
                    'query' => [
                        'RegionId' => "cn-hangzhou",
                        'PhoneNumbers' => $phoneNums,
                        'SignName' => config('sms.sign_name'),
                        'TemplateCode' => config('sms.template_code'),
                        'TemplateParam' => json_encode([
                            'code' => $content
                        ])
                    ],
                ])
                ->request();
            $result = $result->toArray();
            if ('OK' === $result['Code']) {
                return true;
            }
        } catch (\Exception $e) {
           return false;
        }

    }


}