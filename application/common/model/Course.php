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
        ->field(['id', 'title', 'cover'])//
        ->paginate($pageNum, false, ['page' => $page]);

    }

    public function courseItems()
    {
        return $this->hasMany('CourseItem', 'course_id', 'id');
    }

    public function getCourseDetail($id)
    {
        return self::where('id', $id)
            ->where('status', 0)//
            ->field(['id', 'title', 'cover', 'price', 'tip', 'grades'])
            ->find();
    }

    public function getCourseCentorDetail($id)
    {
        return self::with(['courseItems' => function ($query) {
            $query->order('sort desc')->field(['id','name','course_id']);
        }])
            ->field(['id','title','cover','tip'])
            ->where('id', $id)//
            ->where('status', 0)//
            ->find();

    }

    /**
     * grades_arr
     * @param $value
     * @param $data
     * @return mixed
     */
    public function getGradesArrAttr($value,$data) {
        return json_decode($data['grades'],true);
    }

}