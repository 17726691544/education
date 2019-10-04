<?php


namespace app\common\model;


use think\Model;

class Course extends Model
{
    protected $table = 'course';

    public static function getCourseList($page, $pageNum)
    {
        return self::where('status', 0)//
        ->order('create_at', 'desc')//
        ->visible(['id','title','cover'])//
        ->paginate($pageNum, false, ['page' => $page]);

    }

    public function courseItems()
    {
        return $this->hasMany('CourseItem', 'course_id', 'id');
    }

    public function getCourseDetail($id)
    {
       return self::where('id',$id)
           ->where('status',0)//
           ->visible(['id','title','cover','price','tip','grades'])
           ->find();
    }

//    public function test($id)
//    {
//        return self::with(['courseItems' => function ($query) {
//            $query->order('sort desc');
//        }])
//            ->where('id', $id)//
//            ->where('status', 0)//
//            ->hidden(['create_at', ''])//
//            ->find();
//    }


}