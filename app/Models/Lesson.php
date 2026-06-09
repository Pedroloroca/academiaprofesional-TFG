<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Lesson extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;
    protected $fillable = ['course_id', 'title', 'slug', 'content', 'position', 'is_published', 'video_url'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}
