<?php


namespace app\api\controller;


use think\Request;

class Test
{
    public function test(Request $request)
    {
        echo $request->domain();
        echo $request->baseUrl();
        echo '<br>';
        echo $request->root();
        echo $request->rootUrl();
        echo $request->url();
    }
}