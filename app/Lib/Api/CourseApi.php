<?php

namespace App\Lib\Api;

use Cocur\Slugify\Slugify;
use App\Model\Course;
use App\Model\Student;
use App\Service\CourseService;
use App\Service\ContactService;
use App\Service\UtilService;

class CourseApi extends BaseApi
{
    protected $route = 'api_v1_course_find';

    protected $include_variables = [

    ];

    protected function transform(Course $model)
    {
        return [
            'id' => $model->id,
            'code' => $model->code,
            'name' => $model->name,
            'area_id' => $model->area_id,
            'area' => $this->get(UtilService::class)->getAreaById($model->area_id),
            'room' => $model->room,
            'duration' => $model->duration,
            'begin_date' => $model->begin_date,
            'end_date' => $model->end_date,
            'status_id' => $model->status_id,
            'status' => $this->get(CourseService::class)->getStatusById($model->status_id),
            'tuition' => $model->tuition,
            'note' => $model->note,
            'number' => $this->getNumber($model),
            'created_at' => $this->dateFormat($model->created_at, 'Y-m-d H:i:s'),
            'updated_at' => $this->dateFormat($model->updated_at, 'Y-m-d H:i:s'),
        ];
    }

    protected function getNumber(Course $course)
    {
        $lead = $course->student->where('status_id', Student::LEAD)->count();
        $main = $course->student->where('status_id', Student::MAIN)->count();
        return $lead . '/' . $main;
    }
}
