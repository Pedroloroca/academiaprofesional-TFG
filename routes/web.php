<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Livewire\HomePage;
use App\Livewire\PublicCatalog;
use App\Livewire\TeacherDirectory;
use App\Livewire\CourseManager;
use App\Livewire\StudentManager;
use App\Livewire\EnrollmentForm;
use App\Livewire\LessonViewer;

// Public Routes
Route::name('public.')->group(function () {
    Route::get('/', HomePage::class)->name('home');
    
    // Livewire Public Routes
    Route::get('/catalogo', \App\Livewire\CatalogSelector::class)->name('catalog.selector');
    Route::get('/catalogo/{scope}', PublicCatalog::class)->name('catalog');
    Route::get('/profesores', TeacherDirectory::class)->name('teachers');

});

// Language switcher
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['es', 'en', 'fr', 'de', 'it'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('locale.change');

// Dev Login Route (Temporary)
if (app()->environment('local')) {
    Route::get('/dev/login/{role}', function ($role) {
        $user = \App\Models\User::role($role)->first();
        if ($user) {
            auth()->login($user);
            return redirect('/dashboard');
        }
        return "No user found with role: " . $role;
    });
}

// Private Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Livewire Admin & User Routes
    Route::get('/admin/courses', CourseManager::class)->name('admin.courses');
    Route::get('/admin/students', StudentManager::class)->name('admin.students');
    Route::get('/cursos/{slug}/enroll', EnrollmentForm::class)->name('courses.enroll');
    Route::get('/cursos/{slug}', LessonViewer::class)->name('courses.show');
    
    // Paddle Checkout Routes
    Route::get('/checkout/{course:slug}', [\App\Http\Controllers\CheckoutController::class, 'show'])->name('checkout.show');
    Route::get('/checkout/success/{course:slug}', [\App\Http\Controllers\CheckoutController::class, 'success'])->name('checkout.success');

    // PDF Generation Routes
    Route::get('/pdf/welcome/{user}', [\App\Http\Controllers\Web\PdfController::class, 'welcome'])->name('pdf.welcome');
    Route::get('/pdf/invoice/{enrollment}', [\App\Http\Controllers\Web\PdfController::class, 'invoice'])->name('pdf.invoice');
    Route::get('/pdf/certificate/{enrollment}', [\App\Http\Controllers\Web\PdfController::class, 'certificate'])->name('pdf.certificate');
    Route::get('/pdf/course-catalog', [\App\Http\Controllers\Web\PdfController::class, 'courseCatalog'])->name('pdf.course-catalog');
    Route::get('/pdf/teacher-report/{teacher}', [\App\Http\Controllers\Web\PdfController::class, 'teacherReport'])->name('pdf.teacher-report');
});

require __DIR__.'/settings.php';
