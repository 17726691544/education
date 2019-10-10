<?php
namespace app\index\controller;

class Index
{
    public function index()
    {
        $code = $this->createCode(2501);
        return $code;
    }

    private function createCode($userId)
    {
        static $source_string = 'E5FCDG3HQA4B1NOPIJ2RSTUV67MWX89KLYZ';
        $num = $userId;
        $code = '';
        while ($num > 0) {
            $mod = $num % 35;
            $num = ($num - $mod) / 35;
            $code = $source_string[$mod] . $code;
        }
        if (empty($code[3]))
            $code = str_pad($code, 4, '0', STR_PAD_LEFT);
        return $code;
    }
}
