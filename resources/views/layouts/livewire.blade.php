<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Academia Profesional') }} - @yield('title', 'Portal')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />

    @vite(['resources/js/app.ts'])
    @livewireStyles
    @paddleJS
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900 select-none">
    <div class="min-h-screen flex flex-col justify-between">
        
        <!-- Premium Navigation Header -->
        <nav class="bg-white/80 backdrop-blur-md border-b border-gray-100 shadow-sm sticky top-0 z-50 transition-all duration-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-20 items-center">
                    <div class="flex items-center gap-8">
                        <!-- Logo / Brand -->
                        <div class="shrink-0 flex items-center">
                            <a href="/" class="text-2xl font-black tracking-tight flex items-center gap-2 text-gray-900 hover:text-indigo-600 transition-all">
                                <span class="bg-indigo-600 text-white h-10 w-10 rounded-xl flex items-center justify-center font-black shadow-lg shadow-indigo-200 select-none">A</span>
                                <span>Academia</span>
                            </a>
                        </div>
                        
                        <!-- Links (Desktop) -->
                        <div class="hidden md:flex items-center space-x-6">
                            <a href="/" class="text-sm font-extrabold text-gray-600 hover:text-indigo-600 transition-colors py-2 px-3 rounded-xl hover:bg-indigo-50/40 {{ request()->is('/') ? 'text-indigo-600 bg-indigo-50/40 font-black' : '' }}">{{ __('Home') }}</a>
                            
                            <!-- Dropdown for Catalog -->
                            <div class="relative group">
                                <a href="/catalogo" class="text-sm font-extrabold text-gray-600 hover:text-indigo-600 transition-colors py-2 px-3 rounded-xl hover:bg-indigo-50/40 inline-flex items-center gap-1 {{ request()->is('catalogo*') ? 'text-indigo-600 bg-indigo-50/40 font-black' : '' }}">
                                    <span>{{ __('Catalog') }}</span>
                                    <svg class="w-4 h-4 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </a>
                                <div class="absolute left-0 mt-0 pt-2 w-56 opacity-0 translate-y-1 invisible group-hover:opacity-100 group-hover:translate-y-0 group-hover:visible transition-all duration-200 ease-out z-50 pointer-events-none group-hover:pointer-events-auto">
                                    <div class="bg-white rounded-2xl border border-gray-100 shadow-2xl p-2 select-none">
                                        <a href="/catalogo/profesional" class="block text-sm font-bold text-gray-700 hover:text-indigo-600 hover:bg-indigo-50/50 px-4 py-3 rounded-xl transition-all">
                                            💼 {{ __('Catalog') }} Profesional
                                        </a>
                                        <a href="/catalogo/escolar" class="block text-sm font-bold text-gray-700 hover:text-purple-600 hover:bg-purple-50/50 px-4 py-3 rounded-xl transition-all mt-1">
                                            🏫 {{ __('Catalog') }} Escolar
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <a href="/profesores" class="text-sm font-extrabold text-gray-600 hover:text-indigo-600 transition-colors py-2 px-3 rounded-xl hover:bg-indigo-50/40 {{ request()->is('profesores') ? 'text-indigo-600 bg-indigo-50/40 font-black' : '' }}">{{ __('Teachers') }}</a>
                            
                            @auth
                            <a href="/admin/courses" class="text-sm font-extrabold text-gray-600 hover:text-indigo-600 transition-colors py-2 px-3 rounded-xl hover:bg-indigo-50/40 {{ request()->is('admin/courses*') ? 'text-indigo-600 bg-indigo-50/40 font-black' : '' }}">
                                @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'))
                                    {{ __('Courses') }}
                                @else
                                    Mis {{ __('Courses') }}
                                @endif
                            </a>
                            @hasrole('admin|manager')
                            <a href="/admin/students" class="text-sm font-extrabold text-gray-600 hover:text-indigo-600 transition-colors py-2 px-3 rounded-xl hover:bg-indigo-50/40 {{ request()->is('admin/students*') ? 'text-indigo-600 bg-indigo-50/40 font-black' : '' }}">{{ __('Students') }}</a>
                            @endhasrole
                            @endauth
                        </div>
                    </div>

                    <!-- Right user/session section -->
                    <div class="flex items-center gap-4">
                        @auth
                            <div class="hidden md:flex items-center gap-3">
                                <span class="text-xs text-gray-400 font-bold uppercase tracking-wider">{{ __('Hola,') }}</span>
                                <span class="text-sm text-gray-800 font-extrabold">{{ auth()->user()->name }}</span>
                            </div>
                            <a href="/dashboard" class="text-sm font-extrabold text-indigo-600 hover:text-indigo-700 bg-indigo-50/60 hover:bg-indigo-50 px-4 py-2.5 rounded-xl transition-all">
                                {{ __('Panel') }}
                            </a>
                            <form method="POST" action="{{ route('logout') }}" x-data class="inline">
                                @csrf
                                <button type="submit" class="text-sm font-extrabold text-gray-500 hover:text-gray-700 hover:bg-gray-50 px-4 py-2.5 rounded-xl transition-all">
                                    {{ __('Salir') }}
                                </button>
                            </form>
                        @else
                            <a href="/login" class="text-sm font-extrabold text-gray-600 hover:text-gray-900 hover:bg-gray-50 px-4 py-2.5 rounded-xl transition-all">
                                {{ __('Iniciar Sesión') }}
                            </a>
                            <a href="/register" class="text-sm font-black bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-3 rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all">
                                {{ __('Registrarse') }}
                            </a>
                        @endauth

                        <!-- Language Selector -->
                        <div class="relative group">
                            <button class="text-sm font-extrabold text-gray-600 hover:text-indigo-600 transition-colors py-2 px-3 rounded-xl hover:bg-indigo-50/40 inline-flex items-center gap-1 uppercase select-none">
                                <span>{{ app()->getLocale() }}</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div class="absolute right-0 mt-0 pt-2 w-28 opacity-0 translate-y-1 invisible group-hover:opacity-100 group-hover:translate-y-0 group-hover:visible transition-all duration-200 ease-out z-50 pointer-events-none group-hover:pointer-events-auto">
                                <div class="bg-white rounded-2xl border border-gray-100 shadow-2xl p-2 select-none">
                                    <a href="{{ route('locale.change', 'es') }}" class="block text-sm font-bold text-gray-700 hover:text-indigo-600 hover:bg-indigo-50/50 px-3 py-2 rounded-xl transition-all">🇪🇸 ES</a>
                                    <a href="{{ route('locale.change', 'en') }}" class="block text-sm font-bold text-gray-700 hover:text-indigo-600 hover:bg-indigo-50/50 px-3 py-2 rounded-xl transition-all">🇬🇧 EN</a>
                                    <a href="{{ route('locale.change', 'fr') }}" class="block text-sm font-bold text-gray-700 hover:text-indigo-600 hover:bg-indigo-50/50 px-3 py-2 rounded-xl transition-all">🇫🇷 FR</a>
                                    <a href="{{ route('locale.change', 'de') }}" class="block text-sm font-bold text-gray-700 hover:text-indigo-600 hover:bg-indigo-50/50 px-3 py-2 rounded-xl transition-all">🇩🇪 DE</a>
                                    <a href="{{ route('locale.change', 'it') }}" class="block text-sm font-bold text-gray-700 hover:text-indigo-600 hover:bg-indigo-50/50 px-3 py-2 rounded-xl transition-all">🇮🇹 IT</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Dynamic Main Content Area -->
        <main class="py-12 flex-1 flex flex-col">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full flex-1">
                @yield('content', $slot ?? '')
            </div>
        </main>
    </div>

    @livewireScripts
</body>
</html>
