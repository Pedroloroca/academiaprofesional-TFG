<div class="py-6">
    <!-- Header Area -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ __('Gestión de Estudiantes') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Administra el padrón de alumnos de la academia profesional.') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('export.students') }}" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-6 rounded-xl shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
                <span>📊</span> {{ __('Exportar CSV') }}
            </a>
            <button wire:click="create()" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-xl shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
                <span>➕</span> {{ __('Añadir Estudiante') }}
            </button>
        </div>
    </div>

    <!-- Alert Message -->
    @if (session()->has('message'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-xl shadow-sm mb-8 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <span class="text-green-600 text-lg">✔</span>
                <p class="text-sm font-medium text-green-800">{{ session('message') }}</p>
            </div>
            <button class="text-green-500 hover:text-green-700 font-bold" onclick="this.parentElement.remove()">✕</button>
        </div>
    @endif

    <!-- Search and Filter Bar -->
    <div class="flex flex-col sm:flex-row items-center gap-4 mb-6">
        <div class="w-full relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 text-lg select-none">
                🔍
            </span>
            <x-ui.input type="text" wire:model.live="search" placeholder="Buscar estudiante por nombre o correo electrónico..." class="w-full pl-10 rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" />
        </div>
    </div>

    <!-- Premium Table Container -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-xl overflow-hidden mb-12">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50/60 select-none">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('Nombre del Alumno') }}</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('Email') }}</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('F. Nacimiento') }}</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('Acciones') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @foreach($students as $student)
                    <tr class="hover:bg-indigo-50/30 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-black text-sm select-none">
                                    {{ substr($student->user->name ?? 'A', 0, 1) }}
                                </div>
                                <div class="text-sm font-extrabold text-gray-900">{{ $student->user->name ?? 'Estudiante' }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $student->user->email ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            📅 {{ $student->date_of_birth ? $student->date_of_birth->format('d/m/Y') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold">
                            <button wire:click="edit({{ $student->id }})" class="text-indigo-600 hover:text-indigo-800 mr-4 inline-flex items-center gap-1 transition-colors">
                                ✏ {{ __('Editar') }}
                            </button>
                            <button wire:click="delete({{ $student->id }})" class="text-red-600 hover:text-red-800 inline-flex items-center gap-1 transition-colors">
                                🗑 {{ __('Eliminar') }}
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal for Create/Update -->
    @if($isOpen)
    <div class="fixed z-50 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true" wire:click="closeModal()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                <form wire:submit.prevent="store">
                    <div class="bg-white px-6 pt-6 pb-4 sm:p-8 sm:pb-6">
                        <div class="flex justify-between items-center mb-6 border-b pb-4">
                            <h3 class="text-xl font-extrabold text-gray-900">{{ $student_id ? __('Editar Alumno') : __('Registrar Alumno') }}</h3>
                            <button type="button" wire:click="closeModal()" class="text-gray-400 hover:text-gray-600 font-bold">✕</button>
                        </div>
                        
                        <div class="mb-5">
                            <x-ui.label for="name" value="{{ __('Nombre Completo') }}" class="font-bold text-gray-700" />
                            <x-ui.input type="text" id="name" wire:model="name" class="mt-1 block w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Ej: Pedro Loroca" />
                            @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-5">
                            <x-ui.label for="email" value="{{ __('Correo Electrónico') }}" class="font-bold text-gray-700" />
                            <x-ui.input type="email" id="email" wire:model="email" class="mt-1 block w-full rounded-xl border-gray-200" placeholder="correo@ejemplo.com" />
                            @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-5">
                            <x-ui.label for="password" value="{{ __('Contraseña') }} {{ $student_id ? __('(dejar en blanco para no cambiar)') : '' }}" class="font-bold text-gray-700" />
                            <x-ui.input type="password" id="password" wire:model="password" class="mt-1 block w-full rounded-xl border-gray-200" placeholder="••••••••" />
                            @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-5">
                            <x-ui.label for="date_of_birth" value="{{ __('Fecha de Nacimiento') }}" class="font-bold text-gray-700" />
                            <x-ui.date id="date_of_birth" wire:model="date_of_birth" class="mt-1 block w-full rounded-xl border-gray-200" />
                            @error('date_of_birth') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-5">
                            <x-ui.label for="address" value="{{ __('Dirección Postal') }}" class="font-bold text-gray-700" />
                            <x-ui.input type="text" id="address" wire:model="address" class="mt-1 block w-full rounded-xl border-gray-200" placeholder="Calle, Número, Ciudad..." />
                            @error('address') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="bg-gray-50/60 px-6 py-4 sm:px-8 sm:flex sm:flex-row-reverse gap-3 border-t border-gray-100">
                        <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-base font-extrabold text-white sm:w-auto sm:text-sm tracking-wide">
                            {{ __('Guardar Cambios') }}
                        </button>
                        <button type="button" wire:click="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-200 shadow-sm px-6 py-3 bg-white hover:bg-gray-50 text-base font-extrabold text-gray-700 sm:mt-0 sm:w-auto sm:text-sm tracking-wide">
                            {{ __('Cancelar') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
