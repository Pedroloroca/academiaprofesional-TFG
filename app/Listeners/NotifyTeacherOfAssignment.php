<?php
namespace App\Listeners;
use App\Events\TeacherAssigned;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\TeacherAssignment as TeacherAssignmentMail;

class NotifyTeacherOfAssignment
{
    public function handle(TeacherAssigned $event): void
    {
        Log::info("Avisando al profesor {$event->teacher->name} de su asignación al curso {$event->course->title}");

        if ($event->teacher && $event->teacher->email) {
            Mail::to($event->teacher->email)->send(new TeacherAssignmentMail($event->teacher, $event->course));
            Log::info("Email de asignación de profesor enviado.");
        }
    }
}