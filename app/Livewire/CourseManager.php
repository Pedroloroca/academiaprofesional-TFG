<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Course;
use App\Models\Teacher;

class CourseManager extends Component
{
    public $course_id, $title, $description, $price, $teacher_id, $status;
    public $is_classroom = false, $schedule, $classroom_pass_code, $scope = 'profesional', $explanation, $video_url;
    public $isOpen = false;

    public function mount()
    {
    }

    public function create()
    {
        $this->resetInputFields();
        if (auth()->user() && auth()->user()->hasRole('teacher')) {
            $teacher = \App\Models\Teacher::where('user_id', auth()->id())->first();
            if ($teacher) {
                $this->teacher_id = $teacher->id;
            }
        }
        $this->openModal();
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->course_id = null;
        $this->title = '';
        $this->description = '';
        $this->price = '';
        $this->teacher_id = '';
        $this->status = 'draft';
        $this->is_classroom = false;
        $this->schedule = '';
        $this->classroom_pass_code = '';
        $this->scope = 'profesional';
        $this->explanation = '';
        $this->video_url = '';
    }

    public function store()
    {
        $this->validate([
            'title' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'teacher_id' => 'required|exists:teachers,id',
            'status' => 'required|in:draft,published,archived',
            'is_classroom' => 'boolean',
            'schedule' => 'nullable|string',
            'classroom_pass_code' => 'nullable|string',
            'scope' => 'required|in:profesional,escolar',
            'explanation' => 'nullable|string',
            'video_url' => 'nullable|string',
        ]);

        $isNew = !$this->course_id;
        $oldCourse = $this->course_id ? Course::find($this->course_id) : null;

        $course = Course::updateOrCreate(['id' => $this->course_id], [
            'title' => $this->title,
            'slug' => \Illuminate\Support\Str::slug($this->title) . '-' . uniqid(),
            'description' => $this->description,
            'price' => $this->price,
            'teacher_id' => $this->teacher_id,
            'status' => $this->status,
            'is_classroom' => $this->is_classroom,
            'schedule' => $this->schedule,
            'classroom_pass_code' => $this->classroom_pass_code,
            'scope' => $this->scope,
            'explanation' => $this->explanation,
            'video_url' => $this->video_url,
        ]);

        // 1. Dispatch CoursePublished event
        if ($course->status === 'published' && (!$oldCourse || $oldCourse->status !== 'published')) {
            event(new \App\Events\CoursePublished($course));
        }

        // 2. Dispatch TeacherAssigned event
        if ($isNew || ($oldCourse && $oldCourse->teacher_id != $course->teacher_id)) {
            $teacher = \App\Models\Teacher::find($course->teacher_id);
            if ($teacher && $teacher->user) {
                event(new \App\Events\TeacherAssigned($course, $teacher->user));
            }
        }

        session()->flash('message', $this->course_id ? 'Course Updated Successfully.' : 'Course Created Successfully.');

        $this->closeModal();
    }

    public function edit($id)
    {
        $course = Course::findOrFail($id);
        $this->course_id = $id;
        $this->title = $course->title;
        $this->description = $course->description;
        $this->price = $course->price;
        $this->teacher_id = $course->teacher_id;
        $this->status = $course->status;
        $this->is_classroom = $course->is_classroom;
        $this->schedule = $course->schedule;
        $this->classroom_pass_code = $course->classroom_pass_code;
        $this->scope = $course->scope ?: 'profesional';
        $this->explanation = $course->explanation;
        $this->video_url = $course->video_url;
        $this->openModal();
    }

    public function delete($id)
    {
        Course::find($id)->delete();
        session()->flash('message', 'Course Deleted Successfully.');
    }

    public function restore($id)
    {
        $course = Course::onlyTrashed()->findOrFail($id);
        $course->restore();
        session()->flash('message', 'Course Restored Successfully.');
    }

    public function forceDelete($id)
    {
        $course = Course::onlyTrashed()->findOrFail($id);
        $course->forceDelete();
        session()->flash('message', 'Course Deleted Permanently.');
    }

    public $search = '';
    public $sortField = '';
    public $sortDirection = 'asc';
    public $filterStatus = '';

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        $user = auth()->user();
        $courses = collect();
        $deletedCourses = collect();

        $isAdminOrManager = false;
        $isTeacher = false;
        $isStudent = false;

        if ($user) {
            $isAdminOrManager = $user->hasRole('admin') || $user->hasRole('manager');
            $isTeacher = $user->hasRole('teacher');
            $isStudent = $user->hasRole('student');

            $query = Course::query();
            $canView = false;

            if ($isAdminOrManager) {
                $canView = true;
            } elseif ($isTeacher) {
                $teacher = \App\Models\Teacher::where('user_id', $user->id)->first();
                if ($teacher) {
                    $query->where('teacher_id', $teacher->id);
                    $canView = true;
                }
            } elseif ($isStudent) {
                $student = \App\Models\Student::where('user_id', $user->id)->first();
                if ($student) {
                    $query->whereHas('enrollments', function($q) use ($student) {
                        $q->where('student_id', $student->id);
                    });
                    $canView = true;
                }
            }

            if ($canView) {
                if (!empty($this->search)) {
                    $query->where('title', 'like', '%' . $this->search . '%');
                }

                if (!empty($this->filterStatus)) {
                    $query->where('status', $this->filterStatus);
                }

                if (!empty($this->sortField)) {
                    $query->orderBy($this->sortField, $this->sortDirection);
                }

                $courses = $query->with('teacher.user')->get();

                if ($isAdminOrManager) {
                    $deletedQuery = Course::onlyTrashed();
                    if (!empty($this->search)) {
                        $deletedQuery->where('title', 'like', '%' . $this->search . '%');
                    }
                    if (!empty($this->filterStatus)) {
                        $deletedQuery->where('status', $this->filterStatus);
                    }
                    if (!empty($this->sortField)) {
                        $deletedQuery->orderBy($this->sortField, $this->sortDirection);
                    }
                    $deletedCourses = $deletedQuery->with('teacher.user')->get();
                }
            }
        }

        return view('livewire.course-manager', [
            'courses' => $courses,
            'deletedCourses' => $deletedCourses,
            'teachers' => Teacher::with('user')->get(),
            'isAdminOrManager' => $isAdminOrManager,
            'isTeacher' => $isTeacher,
            'isStudent' => $isStudent,
        ])->layout('layouts.livewire');
    }
}
