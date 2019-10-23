<?php
namespace app\index\controller;
//require_once __DIR__ . '/../../../extend/alipay/AopSdk.php';
//require_once __DIR__ . '/../../../extend/alipay/aop/AopClient.php';
//require_once __DIR__ . '/../../../extend/alipay/aop/request/AlipayTradeAppPayRequest.php';
//use AopClient;
//use AlipayTradeAppPayRequest;
//use app\common\service\WxService;

class Index
{
    public function index() {
        return 'ok';
        //$serive = new WxService();
        //$serive->unifiedOrder('测试', 't' . time(),1);


        /*$aop = new AopClient;
        $aop->gatewayUrl = "https://openapi.alipay.com/gateway.do";
        $aop->appId = "2019101068291091";
        $aop->rsaPrivateKey = 'MIIEowIBAAKCAQEAjj7gC37Bcs4Z3qt894hA58HUcMDegN6+29hw5mIOZEAwZiePxH6ati9lEXdE1zY4gnJcw7pT7UgUJ+sbxXLGhcUVhtY9GJ7lvnolHSEf7/uM75yjUc8e6rImOX+CJxSgaGktBiuCBuJCt3uZw70ZzMRKtNvo5l50+ttwawunZeMCK3n6TpWkQkYOiCNE0YcaZzPzQg5onnYGyemakotmRtmJ3GJrynWDx9ikm1HUQSLT/+f28qHVYDt/9lh2RQeZsPtoZu72FJ5LjdU+jOY7OXhxlM7XC7oHwlR43fBtTvo+tJewzfsyDrR4amr9VmL7+Xk4ZhjDNyk3E+jDGKE03QIDAQABAoIBAQCIo6lAIZDeIVEEbqLqfL4sYisZ7ItQK6gXMwCwrby5XFehqZsW267uFvT1JCbCvNjnpceqVJBOPJDwD45ryucclMcq8R1bSzfSEy8Xnjw5oOAvQ7421ckTFfJWBUAXHl9ALNgNNzn+NtZzvo1kU4A3DM/ej+hXcqDYYHbKvTUeTtUVrs2GZJY+vGK9TUlj/I+35nv29T1Lr5ae6uSVQSa3vFh80vCW3d6YFDLDRxdgiat3DLkvzsP/nmYPocMtqB0IRUZpX9mJvEbzY3px7LyhfFXAElgGPQIj3XImW2s1ZKt3SJY1oyKGJAtoBWKfbZOQGfa2kt91fSOVRCCQE53BAoGBAMm8fQYUnyWcQj3K6BV1uCUUBPnZtRBHFkzDsgZHRZ9StVIgb3t/yVd1HpJq5AxKRxAQYOG4nhVCCWsDk/iUzOHVo72U29mH2IZ6Sd84Llg9L9h+zc6O00qIbVJGbVf6Y8wK+IrhXyPAjwU6DEhg3I6luzN84yIzJUzcHF36M2iZAoGBALSB34pa9SqdH4CCWUYkU2mK4pL5yQA94brsy6ph6FaPq9EoMT9R8QdVt268X2QswlutQoV7skJlze0a4TzQftx0EcD/Ft1/PXem/AOYD+5xoS0/+DNOLGmRQJBk0kA+mto6N3jB1UU0lyAq9VWIPgHqu11SYMQPGRjJEeIlPETlAoGAPggviYcdyLrbw5R4+OBR11TokstW2cKCIOir/TI9Plh4WOCQZzFMABCLbgpgRUcxwsbGg6H5OiAsNTPMdEuNFIRU7R6w25riAbforzLfTVLDO5cCQdm5Ltn0g7AUP1FHDp7w/gN+9cdoMvs+e6MNLu/XOGXWkdWYSqIENwGQfUECgYBz89WYN9OzmxkoYRNd9mnKq5feWCyhRh8iB4jRG3G3YTU693mjNk0BrDNYJBOW8xWq9IJ94rbg5irBO/oiKcDEdp8M/hA/GsRuf0r2OBFL15x0x9n1gysOaWszFkDlWhH5S1pk7QbGHQFTfaT5W7qRWqsfSJK2fwhdHpSpU8gCbQKBgCtNUtqm1v5ROXWDltooubEINeS75/R3yTe60KUrr5hUSd5ii86VLXtfMh6Z5rKHsAmVSGSR2TlYnK4f4G3bljkXoBJDvRwQpxdImIKxA8pDb0DbnXjOaLkEMLLt8O+bYx9TOGl36LdLy6w+v3zbxlcctgBhv8hK5T9g6D22UevZ';
        $aop->format = "json";
        $aop->charset = "UTF-8";
        $aop->signType = "RSA2";
        $aop->alipayrsaPublicKey = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAyDWmTBcsMS2VR9lrAA7MhRpsSFOU0ir5HO/23Cs8Q14CMg677zABDlNgv3hOkM+EES2l+IZ7SPCIka+cAd+2+gzhVhun+vpyRFfObnnILBAJawcW4QCn7VAJwCSrkxSKxJFx4I9FRsJmoMQgA7r+CNBCObg4eg4Jl/uqzEbA+eY3XiZduJId8PaQ+GWQg9tUoZ8bU4joaEBftU2rDEBuUAYqDBh7ceUHxVKgdLwAIWjrQTf/fZG2ClEKj72ynSXgFMBoObaWgY8mAG0C9OT5XtKOZfzNq4odaFQCZuFgJpDK3w8sm0xdZ8dEdd+vz5z2Dzw3rObAupMz9+VqSYEDZQIDAQAB';
        //实例化具体API对应的request类,类名称和接口名称对应,当前调用接口名称：alipay.trade.app.pay
        $request = new AlipayTradeAppPayRequest();
        //SDK已经封装掉了公共参数，这里只需要传入业务参数
        $data = [
            'body' => '我是测试数据',
            'subject' => 'App支付测试',
            'out_trade_no' => time() .'',
            'timeout_express' => '30m',
            'total_amount' => '0.01'
        ];

        $request->setNotifyUrl("http://47.108.80.79/index.php/api/Notify/wx");
        $request->setBizContent(json_encode($data));
        //这里和普通的接口调用不同，使用的是sdkExecute
        $response = $aop->sdkExecute($request);

        return json(['code'=>0,'msg'=>'','data'=>$response]);


        //htmlspecialchars是为了输出到页面时防止被浏览器将关键参数html转义，实际打印到日志以及http传输不会有这个问题
        //echo htmlspecialchars($response);//就是orderString 可以直接给客户端请求，无需再做处理。*/
    }
}
