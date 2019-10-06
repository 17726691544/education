<?php


namespace app\common\model;


use think\Model;

class CenterLogs extends Model
{

    protected $table = 'center_logs';

    protected $type = [
        'create_at' => 'timestamp'
    ];
}