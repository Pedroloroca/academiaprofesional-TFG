# Plan de Desarrollo - Pasos a Seguir

Este documento define la hoja de ruta para el desarrollo de la aplicación Academia Profesional, basada en los requisitos establecidos en `Guía.md`.

El desarrollo se divide en 13 fases secuenciales para asegurar una implementación ordenada y sólida.

## ✅ Fase 1: Base de Datos y Fundamentos (Completado)
Establecimiento del núcleo del sistema.
- [x] Crear Migraciones (7 modelos principales + tablas de sistema).
- [x] Crear Modelos con relaciones (1:N, N:M) y atributos.
- [x] Crear Factories para pruebas.
- [x] Instalar y configurar Spatie Permissions.
- [x] Instalar y configurar Spatie MediaLibrary.
- [x] Sembrar base de datos (Admin inicial y roles).

## ✅ Fase 2: Estructura y Andamiaje (Completado)
Organización del código y sistema de autenticación.
- [x] Crear estructura de carpetas (`Web/`, `Api/` controllers, `Livewire/`).
- [x] Integrar Autenticación (Inertia + Fortify stack existente).

## ✅ Fase 3: Roles, Permisos y Políticas (Completado)
Control de acceso y seguridad lógica.
- [x] Configurar los 5 roles: `admin`, `manager`, `teacher`, `student`, `api_client`.
- [x] Definir permisos específicos (ej: `manage courses`).
- [x] Crear `CoursePolicy` y `EnrollmentPolicy` con lógica real.

## ✅ Fase 4: Rutas (Públicas y Privadas) (Completado)
Definición de los puntos de entrada de la aplicación web.
- [x] Definir rutas públicas (Home, Catálogo, Contacto).
- [x] Definir rutas privadas con middleware `auth` y organización por prefijos.

## ✅ Fase 5: API REST (Completado)
Desarrollo de la API para consumo externo o aplicaciones móviles.
- [x] Implementar 5 controladores CRUD (Course, Student, Teacher, Lesson, Enrollment).
- [x] Configurar Laravel Sanctum para autenticación por tokens.
- [x] Generar documentación funcional de endpoints.

## ✅ Fase 6: Lógica de Negocio y Validación (Completado)
Refinamiento de la calidad de datos y consultas complejas.
- [x] Crear FormRequests para validaciones complejas (>2 campos).
- [x] Implementar Scopes (ej: `scopeActive`, `scopeWithOpenEnrollments`).

## ✅ Fase 7: Frontend y Livewire (Completado)
Implementación de la interfaz dinámica.
- [x] Crear 7 componentes Livewire (incluyendo 2 CRUDs completos).
- [x] Crear componentes Blade reutilizables (Input, fechas, selects).

## ✅ Fase 8: Eventos y Procesos Asíncronos (Completado)
Automatización de flujos de trabajo.
- [x] Crear Eventos (`StudentEnrolled`, `PaymentReceived`, etc.).
- [x] Crear Listeners (`SendWelcomeEmail`, etc.).
- [x] Implementar 5 Jobs (3 en cola, 2 síncronos).

## ✅ Fase 9: Comandos de Consola
Herramientas de mantenimiento y automatización interna.
- [x] Crear 7 comandos Artisan personalizados.
- [x] Implementar invocación interna de comandos desde código.

## ✅ Fase 10: Emails y Notificaciones
Comunicación con los usuarios.
- [x] Crear 4 clases Mailables (Bienvenida, Factura, etc.).
- [x] Configurar plantillas Blade para emails.

## ✅ Fase 11: Generación de PDF
Documentos digitales.
- [x] Instalar `laravel-dompdf`.
- [x] Generar 5 tipos de reportes PDF (Carta de Bienvenida, Factura, Certificado, Catálogo, Reporte Profesor).

## ✅ Fase 12: Traducciones
Internacionalización del sistema.
- [x] Configurar soporte para 5 idiomas (`es`, `en`, `fr`, `de`, `it`) mediante `laravel-lang/common`.
- [x] Externalizar cadenas de texto en archivos de recursos JSON y Blade.

## ✅ Fase 13: Testing (Completado)
Aseguramiento de calidad.
- [x] Configurar framework Pest y base de datos de testing.
- [x] Reorganizar tests por categorías: `Api`, `Web` (Auth, Settings, General), `Authorization`.
- [x] Corregir tests pre-existentes del starter kit (rutas, CSRF, redirecciones).
- [x] Alcanzar >85% de cobertura.

---

## ✅ Fase 14: Pasarela de Pagos (Paddle) (Completado)
Integración de pagos para la matriculación en cursos de pago.
- [x] Instalación y configuración de `laravel/cashier-paddle`.
- [x] Integración de Paddle Hosted Checkout en la vista de compra.
- [x] Configuración del Webhook para recibir eventos `transaction.completed`.
- [x] Listener para procesar transacciones: registrar en base de datos y activar matrícula automáticamente.

## 🔜 Fase 15: Exportación a Excel (Laravel Excel)
Herramienta de reportes para administradores.
- [ ] Exportar lista de todos los estudiantes (ID, Nombre, Email, Fecha de Registro).
- [ ] Exportar reporte de un curso con sus estudiantes matriculados.

---

**Nota:** Se ha realizado una auditoría completa de las Fases 1-4 corrigiendo errores críticos en modelos, policies y rutas. Ver `docs/correcciones.md` para más detalle.
