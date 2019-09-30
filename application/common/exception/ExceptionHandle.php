<?php


namespace app\common\exception;

use Exception;
use think\exception\Handle;
use think\facade\Log;

/**
 * 全局异常处理类
 * @package app\common\exception
 */
class ExceptionHandle extends Handle
{
    private $code = 1; //json返回返回code 1:代表失败
    private $errorCode = 500;
    private $msg;

    /**
     * 重写父类的异常处理方法
     * @param Exception $e
     * @return \think\Response|void
     */
    public function render(Exception $e)
    {
        if ($e instanceof BusinessBaseException) {
            //属于自定义业务异常
            $this->msg = $e->getMsg();
            $this->errorCode = $e->getErrorCode();
        } else {
            //属于系统运行异常
            $this->msg = '服务器内部错误';
            //系统异常记录日志信息
            Log::record($e->getMessage(), 'error');
        }
        $errorArr = [
            'code' => $this->code,
            'msg' => $this->msg,
            'errorCode' => $this->errorCode
        ];
        return json($errorArr);
    }
}