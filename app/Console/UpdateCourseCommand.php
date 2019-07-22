<?php
namespace App\Console;

use Symfony\Component\Console\Output\OutputInterface;
use Psr\Container\ContainerInterface;
use App\Service\CourseService;
use Illuminate\Database\Capsule\Manager;
use App\Model\Course;
use App\Model\Student;

class UpdateCourseCommand
{
    public function __invoke(ContainerInterface $container, OutputInterface $output)
    {
        $current_date = date('Y-m-d');
        $courses = $container->get(CourseService::class);
        $course_datas = $courses->where('status_id', '<>', Course::COMPLETE)->whereNotNull('begin_date')->whereNotNull('end_date')->get()->toArray();
        foreach($course_datas as $course) {
            // Cập nhật khoá học khai giảng / kết thúc
            if ($current_date > $course['end_date'] && $course['status_id'] == Course::ONGOING) {
                $update_course = $courses->find($course['id']);
                $update_course->status_id = Course::COMPLETE;
                $update_course->save();
            }
        }
        // Cập nhật trạng thái khoá học của hv thành đang học
        $course_on_going = $courses->where('status_id', Course::ONGOING)->whereNotNull('begin_date')->whereNotNull('end_date')->pluck('id')->all();
        Manager::table('student_course')->whereIn('course_id', $course_on_going)->where('status_id', Student::PENDING)->update(['status_id' => Student::ONGOING]);

        // Cập nhật trạng thái khoá học của hv thành kết thúc
        $course_complete = $courses->where('status_id', Course::COMPLETE)->whereNotNull('begin_date')->whereNotNull('end_date')->pluck('id')->all();
        Manager::table('student_course')->whereIn('course_id', $course_complete)->where('status_id', Student::ONGOING)->update(['status_id' => Student::COMPLETE]);
        
        $output->writeln('Update successfully!!!');
    }
}
