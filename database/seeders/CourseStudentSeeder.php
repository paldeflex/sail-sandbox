<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Student;
use Illuminate\Database\Seeder;

class CourseStudentSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $students = Student::factory(10)->create();
        $courses = Course::factory(10)->create();

        foreach ($students as $student) {
            $student->courses()->attach(
                $courses->random(rand(1, 5))->pluck('id')->toArray()
            );
        }

    }
}
