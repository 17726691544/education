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
    private $errorCode;
    private $msg;

    /**
     * BusinessBaseException constructor.
     * @param int $errorCode
     * @param $msg
     */
    public function __construct($msg, $errorCode = null)
    {
        $this->errorCode = $errorCode;
        $this->msg = $msg;
    }

    /**
     * @return int
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