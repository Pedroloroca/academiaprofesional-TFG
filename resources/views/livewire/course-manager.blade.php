<div class="py-6">
    <!-- Header Area -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            @if($isAdminOrManager)
                <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ __('Cursos') }}</h2>
                <p class="text-sm text-gray-500 mt-1">
                    {{ __('Administra los contenidos, precios y estados de todos los cursos de la academia.') }}</p>
            @elseif($isTeacher)
                <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ __('Mis Cursos') }}</h2>
                <p class="text-sm text-gray-500 mt-1">
                    {{ __('Visualiza y gestiona los cursos en los que impartes clases.') }}</p>
            @else
                <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ __('Mis Cursos') }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ __('Accede a tus cursos matriculados y sigue aprendiendo.') }}</p>
            @endif
        </div>
        <div class="flex flex-wrap items-center gap-3">
            @if($isAdminOrManager)
                <a href="{{ route('export.courses') }}"
                    class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-6 rounded-xl shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
                    <span>📊</span> {{ __('Exportar CSV (Todos)') }}
                </a>
                <button wire:click="create()"
                    class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-xl shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
                    <span>➕</span> {{ __('Crear Nuevo Curso') }}
                </button>
            @elseif($isTeacher)
                <button wire:click="create()"
                    class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-xl shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
                    <span>➕</span> {{ __('Crear Nuevo Curso') }}
                </button>
            @elseif($isStudent)
                <a href="{{ route('export.my-courses') }}"
                    class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-6 rounded-xl shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
                    <span>📊</span> {{ __('Exportar Mis Cursos') }}
                </a>
            @endif
        </div>
    </div>

    <!-- Alert Message -->
    @if (session()->has('message'))
        <div
            class="bg-green-50 border-l-4 border-green-500 p-4 rounded-xl shadow-sm mb-8 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <span class="text-green-600 text-lg">✔</span>
                <p class="text-sm font-medium text-green-800">{{ session('message') }}</p>
            </div>
            <button class="text-green-500 hover:text-green-700 font-bold" onclick="this.parentElement.remove()">✕</button>
        </div>
    @endif

    <!-- Search and Filter Bar -->
    <div class="flex flex-col sm:flex-row items-center gap-4 mb-6">
        <div class="w-full sm:w-2/3 relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 text-lg select-none">
                🔍
            </span>
            <x-ui.input type="text" wire:model.live="search" placeholder="Buscar curso por título..." class="w-full pl-10 rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" />
        </div>
        <div class="w-full sm:w-1/3">
            <x-ui.select wire:model.live="filterStatus" class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                <option value="">Todos los estados</option>
                <option value="published">Publicados</option>
                <option value="draft">Borradores</option>
                <option value="archived">Archivados</option>
            </x-ui.select>
        </div>
    </div>

    <!-- Premium Table Container -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-xl overflow-hidden mb-12">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50/60 select-none">
                    <tr>
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">
                            {{ __('Título del Curso') }}</th>
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">
                            {{ __('Profesor') }}</th>
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors select-none" wire:click="sortBy('price')">
                            <div class="flex items-center gap-1">
                                {{ __('Precio') }}
                                @if($sortField === 'price')
                                    <span class="text-indigo-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @else
                                    <span class="text-gray-300">↕</span>
                                @endif
                            </div>
                        </th>
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">
                            {{ __('Ámbito') }}</th>
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">
                            {{ __('Estado') }}</th>
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">
                            {{ __('Acciones') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @foreach($courses as $course)
                        <tr class="hover:bg-indigo-50/30 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <span class="text-2xl select-none">📘</span>
                                    <div
                                        class="text-sm font-extrabold text-gray-900 group-hover:text-indigo-600 transition-colors">
                                        {{ $course->title }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div
                                        class="h-8 w-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs">
                                        {{ substr($course->teacher->user->name ?? 'P', 0, 1) }}
                                    </div>
                                    <span
                                        class="text-sm font-bold text-gray-700">{{ $course->teacher->user->name ?? 'Instructor' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-extrabold text-indigo-600">
                                ${{ number_format($course->price, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-3 py-1 inline-flex text-xs leading-5 font-black rounded-full select-none {{ $course->scope === 'profesional' ? 'bg-indigo-100 text-indigo-800' : 'bg-purple-100 text-purple-800' }}">
                                    {{ $course->scope === 'profesional' ? __('Profesional') : __('Escolar') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-3 py-1 inline-flex text-xs leading-5 font-black rounded-full select-none {{ $course->status === 'published' ? 'bg-green-100 text-green-800' : ($course->status === 'draft' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ ucfirst($course->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold">
                                @if($isStudent)
                                    <a href="/cursos/{{ $course->slug }}"
                                        class="text-indigo-600 hover:text-indigo-800 mr-4 inline-flex items-center gap-1 transition-colors">
                                        📖 {{ __('Ver contenido') }}
                                    </a>
                                @elseif($isTeacher)
                                    <a href="{{ route('export.course.students', $course->id) }}"
                                        class="text-emerald-600 hover:text-emerald-800 mr-4 inline-flex items-center gap-1 transition-colors"
                                        title="Exportar Alumnos">
                                        📊 {{ __('Exportar Alumnos') }}
                                    </a>
                                    <a href="/cursos/{{ $course->slug }}"
                                        class="text-indigo-600 hover:text-indigo-800 mr-4 inline-flex items-center gap-1 transition-colors">
                                        ✏ {{ __('Gestionar / Ver contenido') }}
                                    </a>
                                    <button wire:click="delete({{ $course->id }})"
                                        class="text-red-600 hover:text-red-800 inline-flex items-center gap-1 transition-colors">
                                        🗑 {{ __('Eliminar') }}
                                    </button>
                                @else
                                    <a href="{{ route('export.course.students', $course->id) }}"
                                        class="text-emerald-600 hover:text-emerald-800 mr-4 inline-flex items-center gap-1 transition-colors"
                                        title="Exportar Alumnos">
                                        📊 {{ __('Exportar Alumnos') }}
                                    </a>
                                    <button wire:click="edit({{ $course->id }})"
                                        class="text-indigo-600 hover:text-indigo-800 mr-4 inline-flex items-center gap-1 transition-colors">
                                        ✏ {{ __('Editar') }}
                                    </button>
                                    <button wire:click="delete({{ $course->id }})"
                                        class="text-red-600 hover:text-red-800 inline-flex items-center gap-1 transition-colors">
                                        🗑 {{ __('Eliminar') }}
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if($isAdminOrManager && count($deletedCourses) > 0)
        <div class="mt-12 bg-white rounded-2xl border border-red-100 shadow-xl overflow-hidden mb-12 animate-fade-in">
            <div class="p-6 bg-red-50/50 border-b border-red-100">
                <h3 class="text-xl font-extrabold text-red-900 tracking-tight flex items-center gap-2">
                    <span>🗑</span> {{ __('Papelera de Cursos Eliminados (Recuperación)') }}
                </h3>
                <p class="text-sm text-red-600/70 mt-1">
                    {{ __('Como Administrador, puedes restaurar o eliminar de forma definitiva los siguientes cursos.') }}
                </p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50/40 select-none">
                        <tr>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">
                                {{ __('Título del Curso') }}</th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">
                                {{ __('Profesor') }}</th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">
                                {{ __('Precio') }}</th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">
                                {{ __('Ámbito') }}</th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">
                                {{ __('Acciones') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach($deletedCourses as $course)
                            <tr class="hover:bg-red-50/30 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <span class="text-2xl select-none">📘</span>
                                        <div class="text-sm font-extrabold text-gray-900">{{ $course->title }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="text-sm font-bold text-gray-700">{{ $course->teacher->user->name ?? 'Instructor' }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-extrabold text-indigo-600">
                                    ${{ number_format($course->price, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-3 py-1 inline-flex text-xs leading-5 font-black rounded-full select-none {{ $course->scope === 'profesional' ? 'bg-indigo-100 text-indigo-800' : 'bg-purple-100 text-purple-800' }}">
                                        {{ $course->scope === 'profesional' ? __('Profesional') : __('Escolar') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold flex items-center gap-4">
                                    <button wire:click="restore({{ $course->id }})"
                                        class="text-green-600 hover:text-green-800 inline-flex items-center gap-1 transition-colors">
                                        🔄 {{ __('Restaurar') }}
                                    </button>
                                    <button wire:click="forceDelete({{ $course->id }})"
                                        onclick="confirm('¿Estás seguro de eliminar este curso de forma permanente?') || event.stopImmediatePropagation()"
                                        class="text-red-600 hover:text-red-800 inline-flex items-center gap-1 transition-colors">
                                        ❌ {{ __('Eliminar Definitivo') }}
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif


    <!-- Modal for Create/Update -->
    @if($isOpen)
        <div class="fixed z-50 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-gray-900/50 transition-opacity" aria-hidden="true"
                    wire:click="closeModal()"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div
                    class="relative z-10 inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                    <form x-data
                        @submit.prevent="$wire.set('explanation', document.getElementById('explanation-input').value); $wire.store()">
                        <div class="bg-white px-6 pt-6 pb-4 sm:p-8 sm:pb-6">
                            <div class="flex justify-between items-center mb-6 border-b pb-4">
                                <h3 class="text-xl font-extrabold text-gray-900">
                                    {{ $course_id ? '✏ Editar Curso' : '➕ Crear Nuevo Curso' }}</h3>
                                <button type="button" wire:click="closeModal()"
                                    class="text-gray-400 hover:text-gray-600 font-bold">✕</button>
                            </div>

                            <div class="mb-5">
                                <x-ui.label for="title" value="Título del Curso" class="font-bold text-gray-700" />
                                <x-ui.input type="text" id="title" wire:model="title"
                                    class="mt-1 block w-full rounded-xl border-2 border-gray-400 bg-gray-50 focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Ej: Introducción a Laravel" />
                                @error('title') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-5">
                                <x-ui.label for="description" value="Descripción" class="font-bold text-gray-700" />
                                <textarea id="description" wire:model="description"
                                    class="border-2 border-gray-400 bg-gray-50 px-3 py-2 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm mt-1 block w-full text-sm leading-relaxed"
                                    rows="4"
                                    placeholder="Escribe un resumen detallado del contenido del curso..."></textarea>
                                @error('description') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-5">
                                <x-ui.label for="price" value="Precio ($)" class="font-bold text-gray-700" />
                                <x-ui.input type="number" step="0.01" id="price" wire:model="price"
                                    class="mt-1 block w-full rounded-xl border-2 border-gray-400 bg-gray-50"
                                    placeholder="0.00" />
                                @error('price') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                            @if($isAdminOrManager)
                                <div class="mb-5">
                                    <x-ui.label for="teacher_id" value="Asignar Profesor" class="font-bold text-gray-700" />
                                    <x-ui.select id="teacher_id" wire:model="teacher_id"
                                        class="mt-1 block w-full rounded-xl border-2 border-gray-400 bg-gray-50">
                                        <option value="">Seleccione un profesor</option>
                                        @foreach($teachers as $teacher)
                                            <option value="{{ $teacher->id }}">{{ $teacher->user->name }}</option>
                                        @endforeach
                                    </x-ui.select>
                                    @error('teacher_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>
                            @else
                                <!-- Field is automatically set for teachers -->
                                <div class="mb-5">
                                    <x-ui.label for="teacher_name" value="Profesor" class="font-bold text-gray-700" />
                                    <x-ui.input type="text" id="teacher_name" value="{{ auth()->user()->name }}" disabled
                                        class="mt-1 block w-full rounded-xl border-gray-200 bg-gray-50/70 text-gray-500 select-none cursor-not-allowed font-medium" />
                                </div>
                            @endif
                            <div class="mb-5">
                                <x-ui.label for="status" value="Estado" class="font-bold text-gray-700" />
                                <x-ui.select id="status" wire:model="status"
                                    class="mt-1 block w-full rounded-xl border-2 border-gray-400 bg-gray-50">
                                    <option value="draft">Borrador</option>
                                    <option value="published">Publicado</option>
                                    <option value="archived">Archivado</option>
                                </x-ui.select>
                                @error('status') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-5">
                                <x-ui.label for="scope" value="Ámbito del Curso / Formación"
                                    class="font-bold text-gray-700" />
                                <x-ui.select id="scope" wire:model="scope"
                                    class="mt-1 block w-full rounded-xl border-2 border-gray-400 bg-gray-50">
                                    <option value="profesional">Profesional</option>
                                    <option value="escolar">Escolar (Colegio / Instituto)</option>
                                </x-ui.select>
                                @error('scope') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-5" wire:ignore>
                                <x-ui.label for="explanation-input" value="Explicación Escrita del Tema"
                                    class="font-bold text-gray-700" />
                                <input id="explanation-input" type="hidden" value="{{ $explanation }}">
                                <trix-editor input="explanation-input"
                                    class="trix-content border-2 border-gray-400 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm mt-1 bg-gray-50 p-3 min-h-[150px] overflow-y-auto leading-relaxed text-sm select-text"></trix-editor>
                                @error('explanation') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-5">
                                <x-ui.label for="video_url" value="URL del Vídeo Explicativo"
                                    class="font-bold text-gray-700" />
                                <x-ui.input type="text" id="video_url" wire:model="video_url"
                                    class="mt-1 block w-full rounded-xl border-2 border-gray-400 bg-gray-50 focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Ej: https://www.youtube.com/embed/..." />
                                @error('video_url') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Extra school/classroom fields -->
                            <div class="mb-5 flex items-center">
                                <input id="is_classroom" type="checkbox" wire:model.live="is_classroom"
                                    class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" />
                                <label for="is_classroom" class="ml-2 block text-sm font-bold text-gray-700 select-none">
                                    ¿Es clase presencial / apoyo escolar?
                                </label>
                                @error('is_classroom') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            @if($is_classroom)
                                <div class="mb-5">
                                    <x-ui.label for="schedule" value="Horario de clase" class="font-bold text-gray-700" />
                                    <x-ui.input type="text" id="schedule" wire:model="schedule"
                                        class="mt-1 block w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Ej: Lunes y Miércoles 17:00 - 18:30" />
                                    @error('schedule') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="mb-5">
                                    <x-ui.label for="classroom_pass_code" value="Pase de asistencia / Código de acceso"
                                        class="font-bold text-gray-700" />
                                    <x-ui.input type="text" id="classroom_pass_code" wire:model="classroom_pass_code"
                                        class="mt-1 block w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Ej: PASS-1234" />
                                    @error('classroom_pass_code') <span
                                    class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            @endif
                        </div>
                        <div
                            class="bg-gray-50/60 px-6 py-4 sm:px-8 sm:flex sm:flex-row-reverse gap-3 border-t border-gray-100">
                            <button type="submit"
                                class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-base font-extrabold text-white sm:w-auto sm:text-sm tracking-wide">
                                Guardar Cambios
                            </button>
                            <button type="button" wire:click="closeModal()"
                                class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-200 shadow-sm px-6 py-3 bg-white hover:bg-gray-50 text-base font-extrabold text-gray-700 sm:mt-0 sm:w-auto sm:text-sm tracking-wide">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.min.js"></script>
    <script>
        document.addEventListener('trix-change', function (e) {
            let inputId = e.target.getAttribute('input');
            if (inputId === 'explanation-input') {
                let hiddenInput = document.getElementById(inputId);
                let val = hiddenInput ? hiddenInput.value : '';
                let wireId = e.target.closest('[wire:id]').getAttribute('wire:id');
                if (window.Livewire) {
                    window.Livewire.find(wireId).set('explanation', val);
                }
            }
        });
    </script>
</div>