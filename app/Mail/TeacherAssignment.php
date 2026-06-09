<?php

namespace App\Mail;

use App\Models\Course;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TeacherAssignment extends Mailable
{
    use Queueable, SerializesModels;

    public $teacher;
    public $course;

    /**
     * Create a new message instance.
     */
    public function __construct(User $teacher, Course $course)
    {
        $this->teacher = $teacher;
        $this->course = $course;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Nueva Asignación de Curso')
                    ->view('emails.teacher-assignment');
    }
}
