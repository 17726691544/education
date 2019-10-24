<?php

namespace app\common\service;

use think\facade\Cache;

class WxService
{
    const APP_ID = 'wxe920f09fdbf0dd02';//小程序ID
    const APP_SECRET = '6398551a686d3d6c7631643fe73a6d75';//小程序secret
    const MCH_ID = '1557987211';//微信支付分配的商户号
    const MCH_KEY = '39ba59a4cf10b1c71daa2ee084421a66';//微信支付分配的商户号
    //const NOTIFY_URL = 'http://118.24.73.45/index.php/notify/wx';//异步接收微信支付结果通知的回调地址
    //const NOTIFY_URL = 'http://47.108.80.79/index.php/api/notify/wx';//异步接收微信支付结果通知的回调地址
    const NOTIFY_URL = 'http://jiaoyu.cqallx.com/index.php/api/notify/wx';//异步接收微信支付结果通知的回调地址
    //const TRADE_TYPE = 'JSAPI';//交易类型,小程序取值如下：JSAPI
    const TRADE_TYPE = 'APP';//交易类型,小程序取值如下：JSAPI

    /**
     * get请求
     * @param $url
     * @return bool|string
     */
    private function get($url)
    {
        //初始化
        $curl = curl_init();
        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL, $url);
        //设置头文件的信息作为数据流输出
        curl_setopt($curl, CURLOPT_HEADER, 0);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 跳过证书检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);  // 从证书中检查SSL加密算法是否存在
        //执行命令
        $data = curl_exec($curl);
        //关闭URL请求
        curl_close($curl);
        //显示获得的数据
        return $data;
    }

    /**
     * post请求
     * @param $url
     * @param $params
     * @return bool|string
     */
    private function post($url, $params)
    {
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params); // Post提交的数据包
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $data = curl_exec($curl); // 执行操作
        if (curl_errno($curl)) {
            return curl_error($curl);
        }
        curl_close($curl); // 关闭CURL会话
        return $data; // 返回数据，json格式
    }

    /**
     * code转openid
     * @param $js_code
     * @return bool
     */
    public function code2Session($js_code)
    {
        $app_id = self::APP_ID;
        $secret = self::APP_SECRET;
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid={$app_id}&secret={$secret}&js_code={$js_code}&grant_type=authorization_code";
        $data = json_decode($this->get($url), true);
        if (isset($data['openid'])) {
            return $data['openid'];
        }
        return false;
    }

    /**
     * 微信支付统一下单
     * @param $title
     * @param $out_trade_no
     * @param $total_fee
     * @param null $open_id
     * @return bool|mixed|string
     */
    public function unifiedOrder($title, $out_trade_no, $total_fee, $open_id = null) {
        $data = [
            'appid' => self::APP_ID,
            'mch_id' => self::MCH_ID,
            'nonce_str' => $this->getNonceStr(),
            'body' => $title,
            'out_trade_no' => $out_trade_no,
            'total_fee' => $total_fee * 100,
            'spbill_create_ip' => $_SERVER['SERVER_ADDR'],
            'notify_url' => self::NOTIFY_URL,
            'trade_type' => self::TRADE_TYPE,
        ];

        if ($open_id) {
            $data['openid'] = $open_id;
        }

        $data['sign'] = $this->sign($data);
        $result = $this->post('https://api.mch.weixin.qq.com/pay/unifiedorder',$this->array2xml($data));
        $result = $this->xml2array($result);

        if (isset($result['return_code']) && $result['return_code'] === 'SUCCESS'
            && isset($result['result_code']) && $result['result_code'] === 'SUCCESS') {
            return $result;
        } else {
            return false;
        }
    }

    /**
     * 小程序支付
     * @param $result
     * @return array
     */
    public function jsPay($result) {
        $data = [
            'appId' => self::APP_ID,
            'timeStamp' => time() . '',
            'nonceStr' => $result['nonce_str'],
            'package' => "prepay_id={$result['prepay_id']}",
            'signType' => 'MD5'
        ];

        $sign = $this->sign($data);
        $data['paySign'] = $sign;
        unset($data['appId']);

        return $data;
    }

    /**
     * app支付
     * @param $result
     * @return array
     */
    public function appPay($result) {
        $data = [
            'appid' => self::APP_ID,
            'partnerid' => self::MCH_ID,
            'prepayid' => $result['prepay_id'],
            'noncestr' => $result['nonce_str'],
            'timestamp' => time() . '',
            'package' => 'Sign=WXPay'
        ];
        $sign = $this->sign($data);
        $data['sign'] = $sign;
        return $data;
    }

    /**
     * 数组转xml
     * @param $data
     * @return string
     */
    private function array2xml($data) {
        $xml = '<xml>';
        foreach ($data as $key=>$value) {
            $xml .= "<{$key}>{$value}</{$key}>";
        }
        $xml .= '</xml>';
        return $xml;
    }

    /**
     * xml转数组
     * @param $xml
     * @return mixed
     */
    public function xml2array($xml) {
        return json_decode(json_encode(simplexml_load_string($xml,'SimpleXMLElement',LIBXML_NOCDATA)),true);
    }

    /**
     * 签名
     * @param $data
     * @return string
     */
    public function sign($data) {
        ksort($data);
        $items = [];
        foreach ($data as $key => $value) {
            if ($value) {
                $items[] = $key . '=' . $value;
            }
        }
        $string = implode('&', $items) . '&key=' . self::MCH_KEY;
        $sign = strtoupper(md5($string));
        return $sign;
    }

    /**
     * 生成nonce_str
     * @return string
     */
    private function getNonceStr()
    {
        $str = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $nonce = '';
        for ($i = 0; $i < 32; $i++) {
            $index = mt_rand(0, 61);
            $nonce .= $str[$index];
        }
        return $nonce;
    }

    /**
     * 微信通知成功响应
     * @return string
     */
    public function wxSuccess() {
        return '<xml><return_code><![CDATA[SUCCESS]]>SUCCESS</return_code><return_msg><![CDATA[OK]]>OK</return_msg></xml>';
    }

    /**
     * 微信通知失败响应
     * @return string
     */
    public function wxFail() {
        return '<xml><return_code><![CDATA[FAIL]]>FAIL</return_code><return_msg><![CDATA[FAIL]]>FAIL</return_msg></xml>';
    }

    /**
     * 生成小程序二维码
     * @param $page
     * @param $scene
     * @param $saveName
     * @return bool
     */
    public function getUnlimited($page,$scene,$saveName) {
        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return false;
        }
        $params = [
            'page' => $page,
            'scene' => $scene
        ];

        $url = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token={$accessToken}";
        $data = $this->post($url,json_encode($params));

        if (false === strpos($data,'errcode')) {
            file_put_contents("uploads/user/ivt_qr/{$saveName}",$data);
            return $saveName;
        } else {
            return false;
        }
    }

    /**
     * 取得AccessToken
     * @return bool|mixed
     */
    private function getAccessToken() {
        $data = Cache::get('wx_access_token');
        if ($data) {
            return $data['access_token'];
        } else {
            $appId = self::APP_ID;
            $appSecret = self::APP_SECRET;
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appId}&secret={$appSecret}";
            $data = json_decode($this->get($url),true);
            if (is_array($data) && isset($data['access_token'])) {
                Cache::set('wx_access_token',$data,$data['expires_in'] - 60);
                return $data['access_token'];
            }
            return false;
        }
    }
}