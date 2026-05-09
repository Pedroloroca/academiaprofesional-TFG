<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Course;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CourseModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_course_belongs_to_teacher()
    {
        $teacher = Teacher::factory()->create(['user_id' => User::factory()->create()->id]);
        $course = Course::factory()->create(['teacher_id' => $teacher->id]);

        $this->assertInstanceOf(Teacher::class, $course->teacher);
    }

    public function test_course_has_slug_on_creation()
    {
        $teacher = Teacher::factory()->create(['user_id' => User::factory()->create()->id]);
        $course = Course::create([
            'title' => 'Sample Course',
            'slug' => 'sample-course',
            'description' => 'Test description',
            'price' => 10.00,
            'teacher_id' => $teacher->id,
            'status' => 'published'
        ]);

        $this->assertEquals('sample-course', $course->slug);
    }
}
