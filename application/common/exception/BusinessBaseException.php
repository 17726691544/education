<?php


namespace app\common\exception;

use  think\Exception;

/**
 * 自定义业务异常基类
 * Class BusinessBaseException
 * @package app\common\exception
 */
class BusinessBaseException extends Exception
{
    protected $errorCode;
    protected $msg;

    /**
     * BusinessBaseException constructor.
     * @param int $errorCode
     * @param $msg
     */
    public function __construct($msg, $errorCode = 1)
    {
        $this->errorCode = $errorCode;
        $this->msg = $msg;
    }

    /**
     * @return int|null
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * @return mixed
     */
    public function getMsg()
    {
        return $this->msg;
    }


}