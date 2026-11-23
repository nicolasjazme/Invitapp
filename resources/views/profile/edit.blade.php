@extends('layouts.app')

@section('contenido')
<div class="max-w-3xl mx-auto py-8 px-4 animate-fade-in-up pb-20">
    
    <header class="mb-8">
        <h1 class="text-3xl font-black text-slate-900 tracking-tight flex items-center gap-3">
            <span class="text-4xl">👤</span> Mi Perfil
        </h1>
        <p class="text-slate-500 text-sm mt-2">Actualiza tus datos personales y credenciales de acceso a Invitapp.</p>
    </header>

    {{-- Alertas de Éxito o Error --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl mb-6 text-sm font-bold flex items-center gap-2 shadow-sm">
            <span>✅</span> {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-700 px-6 py-4 rounded-2xl mb-6 text-sm font-bold shadow-sm">
            <span class="mb-1 block">⚠️ Hubo un problema:</span>
            <ul class="list-disc list-inside ml-4 font-medium">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white p-8 md:p-10 rounded-[2.5rem] shadow-sm border border-slate-100 relative overflow-hidden">
        {{-- Detalle visual de fondo --}}
        <div class="absolute top-0 right-0 w-40 h-40 bg-indigo-50 rounded-bl-full -z-10 opacity-60"></div>

        <form action="{{ route('profile.update') }}" method="POST" class="space-y-8 relative z-10">
            @csrf
            @method('PUT')

            {{-- Sección: Datos Básicos --}}
            <div>
                <h3 class="text-lg font-black text-slate-800 mb-5 border-b border-slate-100 pb-2">Información Personal</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-wider mb-2">Nombre Completo</label>
                        <input type="text" name="nombre" value="{{ old('nombre', Session::get('usuario_logueado')['nombre'] ?? '') }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3.5 text-slate-700 font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all shadow-sm" required>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-wider mb-2">Correo Electrónico</label>
                        <input type="email" name="correo" value="{{ old('correo', Session::get('usuario_logueado')['correo'] ?? '') }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3.5 text-slate-700 font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all shadow-sm" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-wider mb-2">
                            Teléfono <span class="text-slate-300 font-bold ml-1">(Opcional)</span>
                        </label>
                        <input type="text" name="telefono" value="{{ old('telefono', Session::get('usuario_logueado')['telefono'] ?? '') }}" placeholder="+56 9 1234 5678" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3.5 text-slate-700 font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all shadow-sm">
                    </div>
                </div>
            </div>

            {{-- Sección: Seguridad --}}
            <div class="pt-4">
                <h3 class="text-lg font-black text-slate-800 mb-5 border-b border-slate-100 pb-2">Seguridad y Contraseña</h3>
                <div class="space-y-6">
                    <div>
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-wider mb-2">Contraseña Actual</label>
                        <input type="password" name="password_actual" placeholder="Ingresa tu clave actual para guardar cambios" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3.5 text-slate-700 font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all shadow-sm">
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-wider mb-2">
                                Nueva Contraseña <span class="text-slate-300 font-bold ml-1">(Opcional)</span>
                            </label>
                            <input type="password" name="password" placeholder="Mínimo 8 caracteres" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3.5 text-slate-700 font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-wider mb-2">Confirmar Contraseña</label>
                            <input type="password" name="password_confirmation" placeholder="Repítela para confirmar" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3.5 text-slate-700 font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all shadow-sm">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-8 border-t border-slate-100">
                <button type="submit" class="inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-8 py-4 rounded-2xl transition-all shadow-md active:scale-95 hover:-translate-y-1 w-full sm:w-auto">
                    <i class="fa-solid fa-floppy-disk"></i> Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    @keyframes fadeInUp {
        0% { opacity: 0; transform: translateY(15px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up {
        animation: fadeInUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
</style>
@endsection