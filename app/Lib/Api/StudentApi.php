<?php

namespace App\Lib\Api;

use App\Model\Student;
use App\Service\StudentService;

class StudentApi extends BaseApi
{
    protected $route = 'api_v1_student_find';

    protected function transform(Student $model)
    {
        return [
            'id' => $model->id,
            'name' => $model->name,
            'code' => $model->code,
            'gender' => $model->gender,
            'phone' => $model->phone,
            'address' => $model->address,
            'email' => $model->email,
            'birthday' => $model->birthday,
            'note' => $model->note,
            'status_id' => $model->status_id,
            'status' => $this->get(StudentService::class)->getStatusById($model->status_id),
            'created_at' => $this->dateFormat($model->created_at, 'Y-m-d H:i:s'),
            'updated_at' => $this->dateFormat($model->updated_at, 'Y-m-d H:i:s'),
        ];
    }
}
