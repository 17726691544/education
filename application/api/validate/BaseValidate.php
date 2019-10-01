<?php


namespace app\api\validate;


use app\common\exception\BusinessBaseException;
use think\Validate;

class BaseValidate extends Validate
{

    public function goChick($data, $batch = false)
    {
        if(!is_array($data)){
            return;
        }
        $keys = array_keys($data);
        foreach ($keys as $key) {
            if(array_key_exists($key,$this->rules)){
                $this->rule[] = $this->rules[$key];
            }
        }
        if ($batch) {
            $this->batch = true;
        }
        $check = $this->check($data);
        if (!$check) {
            dump($this->error);
            throw  new BusinessBaseException($this->getError());
        }
    }

}