<?php

namespace App\Policies;

use App\Models\Lesson;
use App\Models\User;

class LessonPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'manager', 'teacher', 'student']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Lesson $lesson): bool
    {
        if ($user->hasRole(['admin', 'manager'])) {
            return true;
        }

        if ($user->hasRole('teacher') && $user->teacher) {
            return $lesson->course && $lesson->course->teacher_id === $user->teacher->id;
        }

        if ($user->hasRole('student') && $user->student) {
            // Student must be enrolled in the course that contains this lesson
            return $user->student->courses()->where('courses.id', $lesson->course_id)->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'manager', 'teacher']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Lesson $lesson): bool
    {
        if ($user->hasRole(['admin', 'manager'])) {
            return true;
        }

        if ($user->hasRole('teacher') && $user->teacher) {
            return $lesson->course && $lesson->course->teacher_id === $user->teacher->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Lesson $lesson): bool
    {
        if ($user->hasRole(['admin', 'manager'])) {
            return true;
        }

        if ($user->hasRole('teacher') && $user->teacher) {
            return $lesson->course && $lesson->course->teacher_id === $user->teacher->id;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Lesson $lesson): bool
    {
        return $user->hasRole(['admin']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Lesson $lesson): bool
    {
        return $user->hasRole(['admin']);
    }
}
