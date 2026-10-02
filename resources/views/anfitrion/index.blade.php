@extends('layouts.app')

@section('contenido')
@php
    // Recuperamos el evento activo
    $eventoActivo = $evento ?? Session::get('evento_activo') ?? [];
    $tipoEvento = $eventoActivo['tipo_evento'] ?? 'personalizado';
    $modulosActivos = $eventoActivo['modulos_activos'] ?? [];

    // Fallback de plantillas sincronizado con MongoDB
    if (empty($modulosActivos) && $tipoEvento !== 'personalizado') {
        $plantillas = [
            'babyshower' => ['regalos', 'itinerario', 'menu'],
            'matrimonio' => ['regalos', 'mesas', 'itinerario', 'menu', 'galeria'],
            'cumpleanos' => ['itinerario', 'avisos', 'musica', 'galeria'],
            'asado'      => ['cuota', 'itinerario', 'insumos', 'musica'],
            'fiesta'     => ['itinerario', 'avisos', 'musica', 'checkin'],
        ];
        $modulosActivos = $plantillas[$tipoEvento] ?? [];
    }

    $totalInv = $metricas['total_invitados'] ?? count($invitados ?? []);
    $confirmadosInv = $metricas['confirmados'] ?? 0;
    
    // ID seguro para las rutas
    $eventoId = $eventoActivo['_id'] ?? $eventoActivo['id'] ?? '';
    $slug = $eventoActivo['slug'] ?? '';
    $enlacePublico = url('/e/' . $slug);
@endphp

<div class="space-y-8 animate-fade-in-up pb-20">
    
    {{-- ========================================== --}}
    {{-- CABECERA BIENVENIDA Y ACCIONES RÁPIDAS     --}}
    {{-- ========================================== --}}
    <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-8 bg-white p-6 sm:p-10 rounded-[2.5rem] border border-slate-200/60 shadow-sm relative overflow-hidden">
        
        <div class="absolute -top-20 -right-20 w-64 h-64 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full blur-3xl opacity-70 pointer-events-none"></div>

        <div class="relative z-10">
            <span class="inline-block px-4 py-1.5 bg-indigo-50 text-indigo-600 rounded-full text-[10px] font-black uppercase tracking-widest mb-4">
                🚀 Panel de Control VIP
            </span>
            <h1 class="text-3xl md:text-5xl font-black text-slate-900 tracking-tight leading-tight">
                ¡Hola, {{ Session::get('usuario_logueado')['nombre'] ?? 'Anfitrión' }}! 👋
            </h1>
            <p class="text-slate-500 text-base mt-3 max-w-xl leading-relaxed">
                @if(!empty($eventoActivo))
                    Estás gestionando: <strong class="text-indigo-600 font-bold">{{ $eventoActivo['titulo'] ?? 'Mi Evento' }}</strong>. 
                    Revisa las métricas en tiempo real y configura la experiencia de tus invitados.
                @else
                    Aún no tienes ninguna celebración activa. ¡Comienza creando una para sorprender a tus invitados!
                @endif
            </p>
        </div>
        
        <div class="relative z-10 shrink-0">
            @if(empty($eventoActivo))
                <a href="{{ route('events.create') }}" class="inline-flex items-center justify-center w-full sm:w-auto gap-3 bg-indigo-600 hover:bg-indigo-700 text-white font-black px-8 py-5 rounded-2xl transition-all shadow-xl hover:shadow-indigo-200 hover:-translate-y-1 text-lg">
                    <span class="text-2xl">✨</span> 
                    <span>Crear Mi Primer Evento</span>
                </a>
            @else
                <div class="flex flex-col sm:flex-row gap-3">
                    {{-- Copiar Link Público (NUEVO) --}}
                    <button onclick="copiarLink('{{ $enlacePublico }}')" class="inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-6 py-4 rounded-2xl transition-all shadow-md hover:-translate-y-1 active:scale-95">
                        <span class="text-xl">🔗</span> Copiar Link
                    </button>
                    
                    {{-- Ver Invitación --}}
                    <a href="/e/{{ $slug }}" target="_blank" class="inline-flex items-center justify-center gap-2 bg-white hover:bg-slate-50 text-slate-700 font-bold px-6 py-4 rounded-2xl transition-all border-2 border-slate-200 shadow-sm active:scale-95">
                        <span class="text-xl">👁️</span> Ver Invitación
                    </a>
                    
                    {{-- Editar / Ajustes --}}
                    <a href="/anfitrion/eventos/{{ $eventoId }}/editar" class="inline-flex items-center justify-center gap-2 bg-slate-900 hover:bg-slate-800 text-white font-bold px-6 py-4 rounded-2xl transition-all shadow-md active:scale-95">
                        <span class="text-xl">🛠️</span> Ajustes
                    </a>
                </div>
            @endif
        </div>
    </div>

    @if(empty($eventoActivo))
        {{-- ========================================== --}}
        {{-- ESTADO VACÍO (EMPTY STATE)                 --}}
        {{-- ========================================== --}}
        <a href="/events/create" class="group flex flex-col items-center justify-center py-20 px-6 bg-white border-2 border-dashed border-slate-300 rounded-[2.5rem] hover:bg-indigo-50/50 hover:border-indigo-400 transition-all cursor-pointer text-center">
            <div class="w-24 h-24 rounded-full bg-slate-50 shadow-sm border border-slate-100 flex items-center justify-center text-4xl mb-6 group-hover:scale-110 group-hover:bg-indigo-600 group-hover:text-white transition-all duration-500">
                🚀
            </div>
            <h3 class="text-2xl font-black text-slate-800 group-hover:text-indigo-900 transition-colors">Configura tu Primera Celebración</h3>
            <p class="text-base text-slate-500 mt-2 max-w-md">Diseña invitaciones hermosas para Baby Showers, Matrimonios, Cumpleaños o Asados en menos de 5 minutos.</p>
        </a>
    @else
        {{-- ========================================== --}}
        {{-- TARJETAS KPI (MÉTRICAS)                    --}}
        {{-- ========================================== --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            
            {{-- Métrica 1: Confirmaciones --}}
            <div class="bg-white p-5 sm:p-6 rounded-[2rem] border border-slate-200/60 shadow-sm flex flex-col sm:flex-row items-center sm:items-start text-center sm:text-left gap-4 hover:-translate-y-1 transition-transform">
                <div class="w-16 h-16 rounded-2xl bg-blue-50 text-blue-500 flex items-center justify-center text-3xl shrink-0">
                    ✅
                </div>
                <div>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block mb-1">Confirmados</span>
                    <span class="text-3xl font-black text-slate-900">{{ $confirmadosInv }}<span class="text-base text-slate-400 font-bold">/{{ $totalInv }}</span></span>
                </div>
            </div>

            {{-- Métrica 2 Dinámica: Regalos / Insumos / Cuotas --}}
            @if(in_array('regalos', $modulosActivos))
                <div class="bg-white p-5 sm:p-6 rounded-[2rem] border border-slate-200/60 shadow-sm flex flex-col sm:flex-row items-center sm:text-left text-center gap-4 hover:-translate-y-1 transition-transform">
                    <div class="w-16 h-16 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center text-3xl shrink-0">
                        🎁
                    </div>
                    <div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block mb-1">Lista de Deseos</span>
                        <span class="text-xl font-black text-slate-900">Activa</span>
                    </div>
                </div>
            @elseif(in_array('insumos', $modulosActivos))
                <div class="bg-white p-5 sm:p-6 rounded-[2rem] border border-slate-200/60 shadow-sm flex flex-col sm:flex-row items-center sm:text-left text-center gap-4 hover:-translate-y-1 transition-transform">
                    <div class="w-16 h-16 rounded-2xl bg-orange-50 text-orange-500 flex items-center justify-center text-3xl shrink-0">
                        🛒
                    </div>
                    <div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block mb-1">Insumos</span>
                        <span class="text-xl font-black text-slate-900">En uso</span>
                    </div>
                </div>
            @elseif(in_array('cuota', $modulosActivos))
                <div class="bg-white p-5 sm:p-6 rounded-[2rem] border border-slate-200/60 shadow-sm flex flex-col sm:flex-row items-center sm:text-left text-center gap-4 hover:-translate-y-1 transition-transform">
                    <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-500 flex items-center justify-center text-3xl shrink-0">
                        🐮
                    </div>
                    <div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block mb-1">Pozo Colectivo</span>
                        <span class="text-xl font-black text-slate-900">La Vaca</span>
                    </div>
                </div>
            @else
                <div class="bg-white p-5 sm:p-6 rounded-[2rem] border border-slate-200/60 shadow-sm flex flex-col sm:flex-row items-center sm:text-left text-center gap-4 hover:-translate-y-1 transition-transform">
                    <div class="w-16 h-16 rounded-2xl bg-indigo-50 text-indigo-500 flex items-center justify-center text-3xl shrink-0">
                        ⚙️
                    </div>
                    <div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block mb-1">Módulos</span>
                        <span class="text-2xl font-black text-slate-900">{{ count($modulosActivos) }} <span class="text-base text-slate-400 font-bold">Activos</span></span>
                    </div>
                </div>
            @endif

            {{-- Métrica 3: Tablón de Comunicados --}}
            <div class="bg-white p-5 sm:p-6 rounded-[2rem] border border-slate-200/60 shadow-sm flex flex-col sm:flex-row items-center sm:text-left text-center gap-4 hover:-translate-y-1 transition-transform">
                <div class="w-16 h-16 rounded-2xl bg-sky-50 text-sky-500 flex items-center justify-center text-3xl shrink-0">
                    📢
                </div>
                <div>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block mb-1">Notificaciones</span>
                    <span class="text-xl font-black text-slate-900">Anuncios</span>
                </div>
            </div>

            {{-- Métrica 4: Modo Check-In --}}
            <div class="bg-white p-5 sm:p-6 rounded-[2rem] border border-slate-200/60 shadow-sm flex flex-col sm:flex-row items-center sm:text-left text-center gap-4 hover:-translate-y-1 transition-transform">
                <div class="w-16 h-16 rounded-2xl bg-cyan-50 text-cyan-500 flex items-center justify-center text-3xl shrink-0">
                    🎟️
                </div>
                <div>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block mb-1">Control Puerta</span>
                    <span class="text-xl font-black text-slate-900">Check-In</span>
                </div>
            </div>
        </div>

        {{-- ========================================== --}}
        {{-- GRID DE ACCESOS RÁPIDOS A MÓDULOS ACTIVOS  --}}
        {{-- ========================================== --}}
        <div class="mt-12">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-12 h-12 rounded-full bg-slate-900 text-white flex items-center justify-center text-xl shadow-md">
                    ⚡
                </div>
                <h2 class="text-3xl font-black text-slate-900 tracking-tight">Tus Accesos Rápidos</h2>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
                
                {{-- NUEVO: Módulo base: Lista de Invitados (Siempre visible) --}}
                <div class="group bg-indigo-600 p-6 rounded-[2rem] shadow-md hover:shadow-xl hover:-translate-y-1 transition-all flex flex-col justify-between duration-300 relative overflow-hidden">
                    <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 bg-white/10 rounded-full blur-2xl transition-transform group-hover:scale-150"></div>
                    <div class="relative z-10">
                        <div class="text-5xl mb-4 group-hover:scale-110 transition-transform origin-left text-white">👥</div>
                        <h3 class="font-black text-white text-xl mb-2">Invitados</h3>
                        <p class="text-sm text-indigo-100 mb-8 font-medium">Añade personas, edita asistencia y manda recordatorios.</p>
                    </div>
                    <a href="{{ route('anfitrion.guests.index') }}" class="relative z-10 w-full text-center py-4 px-4 bg-white/20 hover:bg-white text-white hover:text-indigo-700 font-black text-sm rounded-2xl transition-colors block backdrop-blur-sm">
                        Gestionar Lista
                    </a>
                </div>

                @if(in_array('regalos', $modulosActivos))
                    <div class="group bg-white p-6 rounded-[2rem] border-2 border-slate-100 shadow-sm hover:border-rose-200 hover:shadow-xl transition-all flex flex-col justify-between hover:-translate-y-1 duration-300">
                        <div>
                            <div class="text-5xl mb-4 group-hover:scale-110 transition-transform origin-left">🎁</div>
                            <h3 class="font-black text-slate-800 text-xl mb-2">Lista de Deseos</h3>
                            <p class="text-sm text-slate-500 mb-8 font-medium">Administra tu catálogo de regalos y revisa quién ha reservado.</p>
                        </div>
                        <a href="/eventos/modulos/regalos" class="w-full text-center py-4 px-4 bg-slate-50 hover:bg-rose-600 text-slate-700 hover:text-white font-black text-sm rounded-2xl transition-colors block">
                            Gestionar Regalos
                        </a>
                    </div>
                @endif

                @if(in_array('cuota', $modulosActivos))
                    <div class="group bg-white p-6 rounded-[2rem] border-2 border-slate-100 shadow-sm hover:border-emerald-200 hover:shadow-xl transition-all flex flex-col justify-between hover:-translate-y-1 duration-300">
                        <div>
                            <div class="text-5xl mb-4 group-hover:scale-110 transition-transform origin-left">🐮</div>
                            <h3 class="font-black text-slate-800 text-xl mb-2">La Vaca (Cuotas)</h3>
                            <p class="text-sm text-slate-500 mb-8 font-medium">Revisa los aportes bancarios y el pozo acumulado para la fiesta.</p>
                        </div>
                        <a href="/eventos/modulos/cuotas" class="w-full text-center py-4 px-4 bg-slate-50 hover:bg-emerald-600 text-slate-700 hover:text-white font-black text-sm rounded-2xl transition-colors block">
                            Ver Aportes
                        </a>
                    </div>
                @endif

                @if(in_array('insumos', $modulosActivos))
                    <div class="group bg-white p-6 rounded-[2rem] border-2 border-slate-100 shadow-sm hover:border-orange-200 hover:shadow-xl transition-all flex flex-col justify-between hover:-translate-y-1 duration-300">
                        <div>
                            <div class="text-5xl mb-4 group-hover:scale-110 transition-transform origin-left">🛒</div>
                            <h3 class="font-black text-slate-800 text-xl mb-2">Lista de Insumos</h3>
                            <p class="text-sm text-slate-500 mb-8 font-medium">Organiza el asado. Revisa quién llevará el hielo y la carne.</p>
                        </div>
                        <a href="/eventos/modulos/insumos" class="w-full text-center py-4 px-4 bg-slate-50 hover:bg-orange-500 text-slate-700 hover:text-white font-black text-sm rounded-2xl transition-colors block">
                            Revisar Insumos
                        </a>
                    </div>
                @endif

                @if(in_array('mesas', $modulosActivos))
                    <div class="group bg-white p-6 rounded-[2rem] border-2 border-slate-100 shadow-sm hover:border-indigo-200 hover:shadow-xl transition-all flex flex-col justify-between hover:-translate-y-1 duration-300">
                        <div>
                            <div class="text-5xl mb-4 group-hover:scale-110 transition-transform origin-left">🪑</div>
                            <h3 class="font-black text-slate-800 text-xl mb-2">Mesas</h3>
                            <p class="text-sm text-slate-500 mb-8 font-medium">Distribuye a los invitados confirmados en sus asientos.</p>
                        </div>
                        <a href="/eventos/modulos/mesas" class="w-full text-center py-4 px-4 bg-slate-50 hover:bg-indigo-600 text-slate-700 hover:text-white font-black text-sm rounded-2xl transition-colors block">
                            Asignar Mesas
                        </a>
                    </div>
                @endif

                @if(in_array('itinerario', $modulosActivos))
                    <div class="group bg-white p-6 rounded-[2rem] border-2 border-slate-100 shadow-sm hover:border-amber-200 hover:shadow-xl transition-all flex flex-col justify-between hover:-translate-y-1 duration-300">
                        <div>
                            <div class="text-5xl mb-4 group-hover:scale-110 transition-transform origin-left">⏳</div>
                            <h3 class="font-black text-slate-800 text-xl mb-2">Itinerario</h3>
                            <p class="text-sm text-slate-500 mb-8 font-medium">Establece los horarios clave del evento para los invitados.</p>
                        </div>
                        <a href="/eventos/modulos/itinerario" class="w-full text-center py-4 px-4 bg-slate-50 hover:bg-amber-500 text-slate-700 hover:text-white font-black text-sm rounded-2xl transition-colors block">
                            Editar Cronograma
                        </a>
                    </div>
                @endif

                @if(in_array('menu', $modulosActivos))
                    <div class="group bg-white p-6 rounded-[2rem] border-2 border-slate-100 shadow-sm hover:border-teal-200 hover:shadow-xl transition-all flex flex-col justify-between hover:-translate-y-1 duration-300">
                        <div>
                            <div class="text-5xl mb-4 group-hover:scale-110 transition-transform origin-left">🍽️</div>
                            <h3 class="font-black text-slate-800 text-xl mb-2">Menú & Alergias</h3>
                            <p class="text-sm text-slate-500 mb-8 font-medium">Revisa las restricciones alimentarias enviadas.</p>
                        </div>
                        <a href="/eventos/modulos/menu" class="w-full text-center py-4 px-4 bg-slate-50 hover:bg-teal-500 text-slate-700 hover:text-white font-black text-sm rounded-2xl transition-colors block">
                            Ver Opciones
                        </a>
                    </div>
                @endif

                @if(in_array('avisos', $modulosActivos))
                    <div class="group bg-white p-6 rounded-[2rem] border-2 border-slate-100 shadow-sm hover:border-sky-200 hover:shadow-xl transition-all flex flex-col justify-between hover:-translate-y-1 duration-300">
                        <div>
                            <div class="text-5xl mb-4 group-hover:scale-110 transition-transform origin-left">📢</div>
                            <h3 class="font-black text-slate-800 text-xl mb-2">Avisos</h3>
                            <p class="text-sm text-slate-500 mb-8 font-medium">Publica anuncios urgentes en la cartelera digital.</p>
                        </div>
                        <a href="/eventos/modulos/avisos" class="w-full text-center py-4 px-4 bg-slate-50 hover:bg-sky-500 text-slate-700 hover:text-white font-black text-sm rounded-2xl transition-colors block">
                            Publicar Aviso
                        </a>
                    </div>
                @endif

                @if(in_array('musica', $modulosActivos))
                    <div class="group bg-white p-6 rounded-[2rem] border-2 border-slate-100 shadow-sm hover:border-violet-200 hover:shadow-xl transition-all flex flex-col justify-between hover:-translate-y-1 duration-300">
                        <div>
                            <div class="text-5xl mb-4 group-hover:scale-110 transition-transform origin-left">🎵</div>
                            <h3 class="font-black text-slate-800 text-xl mb-2">Playlist</h3>
                            <p class="text-sm text-slate-500 mb-8 font-medium">Descubre las canciones que los asistentes quieren escuchar.</p>
                        </div>
                        <a href="/eventos/modulos/musica" class="w-full text-center py-4 px-4 bg-slate-50 hover:bg-violet-600 text-slate-700 hover:text-white font-black text-sm rounded-2xl transition-colors block">
                            Ver Sugerencias
                        </a>
                    </div>
                @endif

                @if(in_array('galeria', $modulosActivos))
                    <div class="group bg-white p-6 rounded-[2rem] border-2 border-slate-100 shadow-sm hover:border-pink-200 hover:shadow-xl transition-all flex flex-col justify-between hover:-translate-y-1 duration-300">
                        <div>
                            <div class="text-5xl mb-4 group-hover:scale-110 transition-transform origin-left">📸</div>
                            <h3 class="font-black text-slate-800 text-xl mb-2">Galería</h3>
                            <p class="text-sm text-slate-500 mb-8 font-medium">Modera y disfruta las fotos capturadas durante la celebración.</p>
                        </div>
                        <a href="/eventos/modulos/galeria" class="w-full text-center py-4 px-4 bg-slate-50 hover:bg-pink-500 text-slate-700 hover:text-white font-black text-sm rounded-2xl transition-colors block">
                            Moderar Fotos
                        </a>
                    </div>
                @endif

                @if(in_array('presupuesto', $modulosActivos))
                    <div class="group bg-white p-6 rounded-[2rem] border-2 border-slate-100 shadow-sm hover:border-slate-300 hover:shadow-xl transition-all flex flex-col justify-between hover:-translate-y-1 duration-300">
                        <div>
                            <div class="text-5xl mb-4 group-hover:scale-110 transition-transform origin-left">💰</div>
                            <h3 class="font-black text-slate-800 text-xl mb-2">Presupuesto</h3>
                            <p class="text-sm text-slate-500 mb-8 font-medium">Maneja internamente los gastos de tu evento.</p>
                        </div>
                        <a href="/eventos/modulos/presupuesto" class="w-full text-center py-4 px-4 bg-slate-50 hover:bg-slate-700 text-slate-700 hover:text-white font-black text-sm rounded-2xl transition-colors block">
                            Ver Gastos
                        </a>
                    </div>
                @endif

                @if(in_array('checkin', $modulosActivos) || in_array('check_in', $modulosActivos))
                    <div class="group bg-white p-6 rounded-[2rem] border-2 border-slate-100 shadow-sm hover:border-cyan-200 hover:shadow-xl transition-all flex flex-col justify-between hover:-translate-y-1 duration-300">
                        <div>
                            <div class="text-5xl mb-4 group-hover:scale-110 transition-transform origin-left">🎟️️</div>
                            <h3 class="font-black text-slate-800 text-xl mb-2">Check-In</h3>
                            <p class="text-sm text-slate-500 mb-8 font-medium">Escanea códigos QR en puerta y lleva registro de quién ingresa.</p>
                        </div>
                        <a href="/eventos/modulos/checkin" class="w-full text-center py-4 px-4 bg-slate-50 hover:bg-cyan-500 text-slate-700 hover:text-white font-black text-sm rounded-2xl transition-colors block">
                            Iniciar Escáner
                        </a>
                    </div>
                @endif

            </div>
        </div>
    @endif
</div>

<!-- Script para copiar al portapapeles -->
<script>
    function copiarLink(texto) {
        navigator.clipboard.writeText(texto).then(() => {
            alert('¡Enlace público copiado al portapapeles! Ahora puedes enviarlo a tus invitados.');
        }).catch(err => {
            console.error('Error al copiar: ', err);
            alert('No se pudo copiar el enlace.');
        });
    }
</script>

<style>
    @keyframes fadeInUp {
        0% { opacity: 0; transform: translateY(15px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up {
        animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
</style>
@endsection