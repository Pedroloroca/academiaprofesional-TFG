<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    /**
     * Helper genérico para exportar cualquier Array o Collection a CSV
     */
    private function generateCsv(string $filename, $data): StreamedResponse
    {
        $response = new StreamedResponse(function () use ($data) {
            $handle = fopen('php://output', 'w');

            // Añadir BOM para que Excel detecte correctamente el UTF-8
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            if (empty($data)) {
                fputcsv($handle, ['No hay datos para exportar'], ';');
                fclose($handle);
                return;
            }

            // Transformar Collection a Array si es necesario
            $dataArray = is_array($data) ? $data : $data->toArray();

            if (count($dataArray) > 0) {
                // Escribir cabeceras (las claves del primer array)
                fputcsv($handle, array_keys((array) $dataArray[0]), ';');

                // Escribir filas
                foreach ($dataArray as $row) {
                    fputcsv($handle, (array) $row, ';');
                }
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response;
    }

    /**
     * [ADMIN] Exportar todos los estudiantes
     */
    public function exportAllStudents()
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Acceso denegado');
        }

        $students = Student::with('user')->get()->map(function ($student) {
            return [
                'ID' => $student->id,
                'Nombre' => $student->user->name ?? 'N/A',
                'Email' => $student->user->email ?? 'N/A',
                'Fecha de Registro' => $student->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return $this->generateCsv('todos_los_estudiantes_' . date('Y-m-d') . '.csv', $students);
    }

    /**
     * [ADMIN] Exportar todos los cursos
     */
    public function exportAllCourses()
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Acceso denegado');
        }

        $courses = Course::with(['teacher.user', 'enrollments'])->get()->map(function ($course) {
            return [
                'ID' => $course->id,
                'Título' => $course->title,
                'Profesor' => $course->teacher->user->name ?? 'Sin asignar',
                'Nº Alumnos Matriculados' => $course->enrollments->count(),
                'Precio' => $course->price,
                'Estado' => ucfirst($course->status),
            ];
        });

        return $this->generateCsv('todos_los_cursos_' . date('Y-m-d') . '.csv', $courses);
    }

    /**
     * [ADMIN, TEACHER] Exportar alumnos de un curso específico
     */
    public function exportCourseStudents(Course $course)
    {
        $user = auth()->user();
        
        // El admin puede, y el profesor solo si es el dueño del curso
        if (!$user->hasRole('admin')) {
            if (!$user->hasRole('teacher') || $course->teacher->user_id !== $user->id) {
                abort(403, 'Solo puedes exportar alumnos de tus propios cursos.');
            }
        }

        $students = $course->enrollments()->with('student.user')->get()->map(function ($enrollment) {
            return [
                'ID Matrícula' => $enrollment->id,
                'Nombre Alumno' => $enrollment->student->user->name ?? 'N/A',
                'Email Alumno' => $enrollment->student->user->email ?? 'N/A',
                'Fecha de Matrícula' => $enrollment->created_at->format('Y-m-d H:i:s'),
            ];
        });

        $safeTitle = preg_replace('/[^A-Za-z0-9\-]/', '_', $course->title);
        return $this->generateCsv('alumnos_curso_' . $safeTitle . '_' . date('Y-m-d') . '.csv', $students);
    }

    /**
     * [STUDENT] Exportar mis cursos matriculados
     */
    public function exportMyCourses()
    {
        $user = auth()->user();

        if (!$user->hasRole('student')) {
            abort(403, 'Acceso denegado');
        }

        $student = Student::where('user_id', $user->id)->firstOrFail();

        $myCourses = $student->enrollments()->with('course.teacher.user')->get()->map(function ($enrollment) {
            return [
                'Título del Curso' => $enrollment->course->title,
                'Profesor' => $enrollment->course->teacher->user->name ?? 'N/A',
                'Modalidad' => $enrollment->course->is_classroom ? 'Presencial' : 'Online',
                'Fecha de Matrícula' => $enrollment->created_at->format('Y-m-d H:i:s'),
                'Estado' => ucfirst($enrollment->status),
            ];
        });

        return $this->generateCsv('mis_cursos_matriculados_' . date('Y-m-d') . '.csv', $myCourses);
    }
}
