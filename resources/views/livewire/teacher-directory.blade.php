<div class="py-12 bg-gray-50/50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Modern Header -->
        <div class="relative bg-gradient-to-r from-violet-600 to-indigo-700 rounded-3xl p-8 md:p-12 mb-12 overflow-hidden shadow-xl border border-indigo-50/10">
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,rgba(255,255,255,0.15),transparent)] pointer-events-none"></div>
            <div class="relative z-10 max-w-2xl">
                <span class="bg-indigo-500/30 backdrop-blur-sm border border-indigo-400/30 text-white text-xs px-3 py-1 rounded-full font-bold uppercase tracking-wider">{{ __('Teachers') }}</span>
                <h1 class="text-4xl md:text-5xl font-extrabold text-white mt-4 mb-3 tracking-tight">{{ __('Nuestro Profesorado') }}</h1>
                <p class="text-indigo-100 text-base md:text-lg">{{ __('Conoce a los instructores expertos que te guiarán paso a paso en tu camino hacia el éxito profesional.') }}</p>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($teachers as $teacher)
            <div class="group bg-white rounded-2xl border border-gray-100 p-8 text-center shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                <div class="w-24 h-24 bg-gradient-to-tr from-indigo-500 to-purple-500 rounded-2xl mx-auto mb-5 flex items-center justify-center text-white text-3xl font-black shadow-lg transform group-hover:rotate-6 transition-transform duration-300">
                    {{ substr($teacher->user->name, 0, 1) }}
                </div>
                <h3 class="text-xl font-extrabold text-gray-900 group-hover:text-indigo-600 transition-colors duration-200">
                    {{ $teacher->user->name }}
                </h3>
                <p class="text-gray-500 text-sm mt-3 mb-4 leading-relaxed line-clamp-3">
                    {{ $teacher->bio ?: __('Profesor e Instructor experto de nuestra academia profesional.') }}
                </p>
                <div class="pt-4 border-t border-gray-50 mt-auto flex flex-col gap-2">
                    @if($teacher->website_url)
                    <a href="{{ $teacher->website_url }}" target="_blank" class="inline-flex items-center gap-1.5 text-indigo-600 hover:text-indigo-500 text-sm font-bold tracking-wide transition-colors">
                        🌐 {{ __('Ver Sitio Web') }} &rarr;
                    </a>
                    @endif
                    <a href="{{ route('pdf.teacher-report', $teacher->id) }}" target="_blank" class="inline-flex items-center justify-center gap-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-bold py-2 px-3 rounded-lg shadow-sm transition-all mt-1">
                        📄 {{ __('Descargar Reporte del Profesor') }}
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
