<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nueva Asignación de Curso</title>
</head>
<body style="font-family: sans-serif; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 5px;">
        <h2 style="color: #2b6cb0;">Hola, {{ $teacher->name ?? 'Profesor' }}</h2>
        <p>Te informamos que has sido asignado para impartir el siguiente curso en nuestra Academia Profesional:</p>
        
        <div style="background-color: #f7fafc; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <p style="margin: 5px 0;"><strong>Curso:</strong> {{ $course->title }}</p>
            <p style="margin: 5px 0;"><strong>Horario:</strong> {{ $course->schedule ?? 'No definido' }}</p>
        </div>

        <p>Puedes acceder a la plataforma para ver todos los detalles y empezar a gestionar tus lecciones.</p>
        <p>¡Gracias por tu dedicación!</p>
        
        <hr style="border: none; border-top: 1px solid #eee; margin: 25px 0;">
        <p style="font-size: 0.8em; color: #718096; text-align: center;">Academia Profesional © 2026</p>
    </div>
</body>
</html>
