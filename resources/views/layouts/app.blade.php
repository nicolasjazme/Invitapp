@php
    // 1. Recuperamos de la sesión el evento activo y el usuario
    $eventoActivo = Session::get('evento_activo') ?? [];
    $tipoEvento = $eventoActivo['tipo_evento'] ?? 'personalizado';
    $modulosActivos = $eventoActivo['modulos_activos'] ?? []; 
    
    // Si no es personalizado y no tiene módulos explícitos, inyectamos la plantilla por defecto
    if (empty($modulosActivos) && $tipoEvento !== 'personalizado') {
        $plantillas = [
            'baby_shower' => ['regalos', 'itinerario', 'menu', 'checkin'],
            'matrimonio'  => ['regalos', 'mesas', 'itinerario', 'menu', 'galeria', 'checkin'],
            'cumpleanos'  => ['itinerario', 'avisos', 'musica', 'galeria', 'checkin'],
            'asado'       => ['cuotas', 'itinerario', 'insumos', 'musica', 'checkin'],
            'fiesta'      => ['itinerario', 'avisos', 'musica', 'checkin'],
        ];
        $modulosActivos = $plantillas[$tipoEvento] ?? [];
    }

    // 2. Paleta de colores dinámica según el tipo de evento
    $configuracionTemas = [
        'baby_shower' => [
            'primario'    => 'bg-blue-500',
            'hex'         => '#3b82f6',
            'texto_hover' => 'hover:bg-blue-50 hover:text-blue-700',
            'badge'       => 'bg-blue-100 text-blue-800',
            'emoji'       => '🍼'
        ],
        'matrimonio' => [
            'primario'    => 'bg-rose-500',
            'hex'         => '#f43f5e',
            'texto_hover' => 'hover:bg-rose-50 hover:text-rose-700',
            'badge'       => 'bg-rose-100 text-rose-800',
            'emoji'       => '💍'
        ],
        'cumpleanos' => [
            'primario'    => 'bg-purple-600',
            'hex'         => '#9333ea',
            'texto_hover' => 'hover:bg-purple-50 hover:text-purple-700',
            'badge'       => 'bg-purple-100 text-purple-800',
            'emoji'       => '🎂'
        ],
        'asado' => [
            'primario'    => 'bg-orange-600',
            'hex'         => '#ea580c',
            'texto_hover' => 'hover:bg-orange-50 hover:text-orange-700',
            'badge'       => 'bg-orange-100 text-orange-800',
            'emoji'       => '🥩'
        ],
        'fiesta' => [
            'primario'    => 'bg-fuchsia-600',
            'hex'         => '#c026d3',
            'texto_hover' => 'hover:bg-fuchsia-50 hover:text-fuchsia-700',
            'badge'       => 'bg-fuchsia-100 text-fuchsia-800',
            'emoji'       => '🎉'
        ],
        'personalizado' => [
            'primario'    => 'bg-indigo-600',
            'hex'         => '#4f46e5',
            'texto_hover' => 'hover:bg-indigo-50 hover:text-indigo-700',
            'badge'       => 'bg-indigo-100 text-indigo-800',
            'emoji'       => '⚙️'
        ]
    ];

    $temaActual = $configuracionTemas[$tipoEvento] ?? $configuracionTemas['personalizado'];
    $colorTemaCustom = $eventoActivo['configVisual']['colorTema'] ?? $temaActual['hex'];

// Forzar siempre la activación del escáner, sin importar la base de datos
    if (!in_array('checkin', $modulosActivos) && !in_array('check_in', $modulosActivos)) {
        $modulosActivos[] = 'checkin';
    }
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invita App - Panel de Control</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    {{-- Inyección de variables CSS dinámicas según la configuración del evento --}}
    <style>
        :root {
            --color-acento: {{ $colorTemaCustom }};
        }
        .bg-acento { background-color: var(--color-acento) !important; }
        .text-acento { color: var(--color-acento) !important; }
        .border-acento { border-color: var(--color-acento) !important; }
        
        /* Barra de desplazamiento suave (Scrollbar) */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased selection:bg-indigo-500 selection:text-white flex flex-col md:flex-row min-h-screen w-full overflow-x-hidden">

    {{-- ========================================== --}}
    {{-- BARRA NAVEGACIÓN MÓVIL (TOP BAR)           --}}
    {{-- ========================================== --}}
    <div class="md:hidden bg-white/95 backdrop-blur-md border-b border-slate-200 px-5 py-4 flex items-center justify-between sticky top-0 z-40 shadow-sm w-full">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 {{ $temaActual['primario'] }} text-white rounded-xl flex items-center justify-center text-xl shadow-md">
                {{ $temaActual['emoji'] }}
            </div>
            <span class="font-black text-xl tracking-tight text-slate-900">Invita App</span>
        </div>
        <button id="btn-toggle-mobile" onclick="toggleSidebar(true)" class="p-2 text-slate-600 hover:text-slate-900 focus:outline-none rounded-xl hover:bg-slate-100 transition-colors">
            <i class="fa-solid fa-bars text-2xl"></i>
        </button>
    </div>

    {{-- OVERLAY FONDO OSCURO PARA MÓVIL --}}
    <div id="sidebar-overlay" onclick="toggleSidebar(false)" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-40 md:hidden transition-opacity"></div>

    {{-- ========================================== --}}
    {{-- SIDEBAR LATERAL                            --}}
    {{-- ========================================== --}}
    <aside id="sidebar-menu" class="fixed md:static inset-y-0 left-0 w-[85%] max-w-[320px] md:w-80 bg-white border-r border-slate-200 flex flex-col justify-between shrink-0 z-50 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out shadow-2xl md:shadow-none h-screen">
        
        <div class="p-6 md:p-8 overflow-y-auto flex-1 flex flex-col">
            
            {{-- CABECERA SIDEBAR --}}
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 {{ $temaActual['primario'] }} text-white rounded-2xl flex items-center justify-center text-2xl shadow-lg transform transition-transform hover:scale-105 cursor-pointer">
                        {{ $temaActual['emoji'] }}
                    </div>
                    <div>
                        <h2 class="font-black text-2xl tracking-tight text-slate-900 leading-none">Invita App</h2>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mt-1">Panel de Control</span>
                    </div>
                </div>
                {{-- Botón para cerrar en móviles --}}
                <button onclick="toggleSidebar(false)" class="md:hidden text-slate-400 hover:text-slate-600 bg-slate-50 hover:bg-slate-100 p-2 rounded-full transition-colors">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            {{-- NAVEGACIÓN GLOBAL: MIS CELEBRACIONES --}}
            <div class="mb-6">
                <a href="/anfitrion/mis-eventos" class="flex items-center gap-4 px-5 py-3.5 text-sm font-bold rounded-2xl text-indigo-700 bg-indigo-50 border border-indigo-100 hover:bg-indigo-100 hover:border-indigo-200 transition-all shadow-sm group">
                    <i class="fa-solid fa-layer-group w-5 text-center text-lg group-hover:scale-110 transition-transform"></i>
                    <span>Mis Celebraciones</span>
                </a>
            </div>

            {{-- TARJETA DE CELEBRACIÓN ACTIVA --}}
            @if(!empty($eventoActivo))
                <div class="bg-gradient-to-br from-slate-50 to-slate-100 p-5 rounded-3xl mb-6 border border-slate-200/60 shadow-sm relative overflow-hidden group">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Gestionando Evento</span>
                    <h3 class="font-black text-slate-800 text-base truncate group-hover:text-indigo-600 transition-colors">{{ $eventoActivo['titulo'] ?? 'Sin título' }}</h3>
                    <span class="inline-block mt-3 text-[10px] font-black px-3 py-1 rounded-full {{ $temaActual['badge'] }} uppercase tracking-wider">
                        {{ str_replace('_', ' ', $tipoEvento) }}
                    </span>
                </div>
            @endif

            {{-- MENÚ DE NAVEGACIÓN DINÁMICO --}}
            <nav class="space-y-1.5 pb-4">
                
                <a href="{{ route('anfitrion.index') }}" class="flex items-center gap-4 px-5 py-3.5 text-sm font-bold rounded-2xl text-slate-600 border border-transparent transition-all {{ $temaActual['texto_hover'] }} hover:bg-slate-50">
                    <i class="fa-solid fa-chart-pie w-5 text-center text-lg"></i>
                    <span>Resumen Dashboard</span>
                </a>

                <a href="{{ route('anfitrion.guests.index') }}" class="flex items-center justify-between px-5 py-3.5 text-sm font-bold rounded-2xl text-slate-600 border border-transparent transition-all {{ $temaActual['texto_hover'] }} hover:bg-slate-50">
                    <div class="flex items-center gap-4">
                        <i class="fa-solid fa-users w-5 text-center text-lg text-blue-500"></i>
                        <span>Lista de Invitados</span>
                    </div>
                </a>

                <div class="pt-6 pb-2">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-5 block border-t border-slate-100 pt-6">Módulos Activos</span>
                </div>

                {{-- RENDERIZADO CONDICIONAL SEGÚN MODULOS_ACTIVOS --}}
                @if(in_array('regalos', $modulosActivos))
                    <a href="/eventos/modulos/regalos" class="flex items-center gap-4 px-5 py-3.5 text-sm font-bold rounded-2xl text-slate-600 border border-transparent transition-all {{ $temaActual['texto_hover'] }} hover:bg-slate-50">
                        <i class="fa-solid fa-gift w-5 text-center text-lg text-rose-500"></i>
                        <span>Lista de Deseos</span>
                    </a>
                @endif

                @if(in_array('cuotas', $modulosActivos))
                    <a href="/eventos/modulos/cuotas" class="flex items-center gap-4 px-5 py-3.5 text-sm font-bold rounded-2xl text-slate-600 border border-transparent transition-all {{ $temaActual['texto_hover'] }} hover:bg-slate-50">
                        <i class="fa-solid fa-piggy-bank w-5 text-center text-lg text-emerald-500"></i>
                        <span>Cuotas (La Vaca)</span>
                    </a>
                @endif

                @if(in_array('mesas', $modulosActivos))
                    <a href="/eventos/modulos/mesas" class="flex items-center gap-4 px-5 py-3.5 text-sm font-bold rounded-2xl text-slate-600 border border-transparent transition-all {{ $temaActual['texto_hover'] }} hover:bg-slate-50">
                        <i class="fa-solid fa-chair w-5 text-center text-lg text-indigo-500"></i>
                        <span>Asignación de Mesas</span>
                    </a>
                @endif

                @if(in_array('itinerario', $modulosActivos))
                    <a href="/eventos/modulos/itinerario" class="flex items-center gap-4 px-5 py-3.5 text-sm font-bold rounded-2xl text-slate-600 border border-transparent transition-all {{ $temaActual['texto_hover'] }} hover:bg-slate-50">
                        <i class="fa-solid fa-clock-rotate-left w-5 text-center text-lg text-amber-500"></i>
                        <span>Itinerario Cronológico</span>
                    </a>
                @endif

                @if(in_array('menu', $modulosActivos))
                    <a href="/eventos/modulos/menu" class="flex items-center gap-4 px-5 py-3.5 text-sm font-bold rounded-2xl text-slate-600 border border-transparent transition-all {{ $temaActual['texto_hover'] }} hover:bg-slate-50">
                        <i class="fa-solid fa-utensils w-5 text-center text-lg text-teal-500"></i>
                        <span>Menú & Alergias</span>
                    </a>
                @endif

                @if(in_array('avisos', $modulosActivos))
                    <a href="/eventos/modulos/avisos" class="flex items-center gap-4 px-5 py-3.5 text-sm font-bold rounded-2xl text-slate-600 border border-transparent transition-all {{ $temaActual['texto_hover'] }} hover:bg-slate-50">
                        <i class="fa-solid fa-bullhorn w-5 text-center text-lg text-sky-500"></i>
                        <span>Tablón de Anuncios</span>
                    </a>
                @endif

                @if(in_array('musica', $modulosActivos))
                    <a href="/eventos/modulos/musica" class="flex items-center gap-4 px-5 py-3.5 text-sm font-bold rounded-2xl text-slate-600 border border-transparent transition-all {{ $temaActual['texto_hover'] }} hover:bg-slate-50">
                        <i class="fa-solid fa-music w-5 text-center text-lg text-violet-500"></i>
                        <span>Playlist Colaborativa</span>
                    </a>
                @endif

                @if(in_array('insumos', $modulosActivos))
                    <a href="/eventos/modulos/insumos" class="flex items-center gap-4 px-5 py-3.5 text-sm font-bold rounded-2xl text-slate-600 border border-transparent transition-all {{ $temaActual['texto_hover'] }} hover:bg-slate-50">
                        <i class="fa-solid fa-basket-shopping w-5 text-center text-lg text-orange-500"></i>
                        <span>Lista de Insumos</span>
                    </a>
                @endif

                @if(in_array('galeria', $modulosActivos))
                    <a href="/eventos/modulos/galeria" class="flex items-center gap-4 px-5 py-3.5 text-sm font-bold rounded-2xl text-slate-600 border border-transparent transition-all {{ $temaActual['texto_hover'] }} hover:bg-slate-50">
                        <i class="fa-solid fa-images w-5 text-center text-lg text-pink-500"></i>
                        <span>Muro de Galería</span>
                    </a>
                @endif

                @if(in_array('presupuesto', $modulosActivos))
                    <a href="/eventos/modulos/presupuesto" class="flex items-center gap-4 px-5 py-3.5 text-sm font-bold rounded-2xl text-slate-600 border border-transparent transition-all {{ $temaActual['texto_hover'] }} hover:bg-slate-50">
                        <i class="fa-solid fa-wallet w-5 text-center text-lg text-slate-500"></i>
                        <span>Presupuesto Tracker</span>
                    </a>
                @endif

                @if(in_array('checkin', $modulosActivos) || in_array('check_in', $modulosActivos))
                    <a href="/eventos/modulos/checkin" class="flex items-center gap-4 px-5 py-3.5 text-sm font-bold rounded-2xl text-slate-600 border border-transparent transition-all {{ $temaActual['texto_hover'] }} hover:bg-slate-50">
                        <i class="fa-solid fa-qrcode w-5 text-center text-lg text-cyan-500"></i>
                        <span>Check-In de Invitados</span>
                    </a>
                @endif

            </nav>
        </div>

        {{-- PIE DE SIDEBAR: PERFIL Y CERRAR SESIÓN --}}
        <div class="p-6 border-t border-slate-100 bg-slate-50/80 shrink-0">
            <div class="flex items-center justify-between bg-white p-3 rounded-2xl border border-slate-200 shadow-sm">
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 group truncate flex-1">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 font-black flex items-center justify-center text-sm shrink-0 group-hover:bg-indigo-100 group-hover:text-indigo-600 transition-colors">
                        {{ strtoupper(substr(Session::get('usuario_logueado')['nombre'] ?? 'A', 0, 1)) }}
                    </div>
                    <div class="truncate">
                        <p class="text-sm font-black text-slate-900 group-hover:text-indigo-600 transition-colors truncate leading-tight">
                            {{ Session::get('usuario_logueado')['nombre'] ?? 'Anfitrión' }}
                        </p>
                        <p class="text-[10px] text-slate-400 font-bold uppercase mt-0.5 tracking-wider">Mi Perfil</p>
                    </div>
                </a>
                <form action="/logout" method="POST" class="inline shrink-0 ml-2 pl-2 border-l border-slate-100">
                    @csrf
                    <button type="submit" class="w-10 h-10 rounded-xl bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white transition-all cursor-pointer flex items-center justify-center shadow-sm" title="Cerrar Sesión">
                        <i class="fa-solid fa-power-off text-base"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ========================================== --}}
    {{-- CONTENIDO PRINCIPAL DE LA VISTA            --}}
    {{-- ========================================== --}}
    <main class="flex-1 bg-slate-50 overflow-y-auto h-screen relative">
        <div class="w-full max-w-full">
            @yield('contenido')
            @yield('content')
        </div>
    </main>

    {{-- SCRIPT PARA CONTROLAR EL MENÚ RESPONSIVE EN MÓVILES --}}
    <script>
        function toggleSidebar(open) {
            const sidebar = document.getElementById('sidebar-menu');
            const overlay = document.getElementById('sidebar-overlay');
            
            if (open) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        }
    </script>
</body>
</html>