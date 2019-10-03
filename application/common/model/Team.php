<?php


namespace app\common\model;


use think\Model;

class Team extends Model
{
    protected $table = 'team';

    public static function getTeamList($page, $pageNum)
    {
        return self::paginate($pageNum, false, [
            'page' => $page
        ])->hidden(['create_at','images','detail'])->each(function ($item, $key) {
            try {
                $item->image = json_decode($item['images'])[0];
            } catch (\Exception $e) {
                $item->image = '';
            }
        });
    }

    public static function getTeamDetail($teamId)
    {
        return self::get($teamId)->hidden(['create_at','id']);
    }
}
