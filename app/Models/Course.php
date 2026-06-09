<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;
    
    protected $fillable = ['teacher_id', 'title', 'slug', 'description', 'price', 'status', 'is_classroom', 'schedule', 'classroom_pass_code', 'scope', 'explanation', 'video_url'];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'enrollments')
                    ->using(Enrollment::class)
                    ->withPivot('status', 'final_grade', 'enrolled_at')
                    ->withTimestamps();
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeWithActiveEnrollments($query)
    {
        return $query->whereHas('enrollments', function ($q) {
            $q->where('status', 'active');
        });
    }

    public function scopeClassroom($query)
    {
        return $query->where('is_classroom', true);
    }

    public function scopeWithManyLessons($query, int $count = 5)
    {
        return $query->has('lessons', '>=', $count);
    }
}
