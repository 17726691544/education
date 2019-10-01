<?php


namespace app\common\validate;


use app\common\exception\BusinessBaseException;
use think\Validate;

/**
 * 基础验证器类
 * Class BaseValidate
 * @package app\api\validate
 */
class BaseValidate extends Validate
{
    //同一类操作的的验证规则，动态根据传出的data数据动态选择
    protected $rules;

    public function goChick($data, $batch = false)
    {
        if (!is_array($data)) {
            return;
        }
        $keys = array_keys($data);
        foreach ($keys as $key) {
            if (array_key_exists($key, $this->rules)) {
                $this->rule[$key] = $this->rules[$key];
            }
        }
        if ($batch) {
            $this->batch = true;
        }
        $check = $this->check($data);
        if (!$check) {
            throw  new BusinessBaseException($this->getError());
        }
    }

}