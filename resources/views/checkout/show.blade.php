@extends('layouts.livewire')

@section('content')
    <div class="mb-4">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Finalizar Compra') }}
        </h2>
    </div>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl p-8 border border-gray-100">

                <div class="text-center mb-8">
                    <span class="text-4xl">🎓</span>
                    <h3 class="text-2xl font-black text-gray-900 mt-3 mb-2">{{ $course->title }}</h3>
                    <p class="text-gray-500">{{ __('Por favor, completa tu pago para obtener acceso inmediato de por vida.') }}</p>
                </div>

                <div class="bg-indigo-50 p-6 rounded-2xl mb-8 flex justify-between items-center border border-indigo-100">
                    <span class="font-bold text-gray-700 uppercase tracking-wider text-sm">{{ __('Total a pagar') }}</span>
                    <span class="text-3xl font-black text-indigo-600">${{ number_format($course->price, 2) }}</span>
                </div>

                <div class="text-center">
                    <a href="{{ $checkoutUrl }}"
                       class="inline-block w-full bg-indigo-600 hover:bg-indigo-700 text-white font-black py-4 px-6 rounded-2xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200 tracking-wide text-center">
                        💳 {{ __('Pagar con Paddle') }} &nbsp; →
                    </a>
                    <p class="text-xs text-gray-400 mt-3">{{ __('Serás redirigido a la pasarela de pago segura de Paddle') }}</p>
                </div>

                <div class="mt-6 border-t border-gray-100 pt-4 text-center">
                    <a href="{{ route('courses.enroll', $course->slug) }}" class="text-sm text-indigo-500 hover:underline">
                        ← {{ __('Volver') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
