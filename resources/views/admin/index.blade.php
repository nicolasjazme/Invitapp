<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centro de Mando - InvitApp</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#FDFBF7] text-stone-800 font-sans antialiased selection:bg-amber-500 selection:text-white min-h-screen flex flex-col">

    {{-- Barra de Navegación Superior --}}
    <nav class="w-full bg-white/80 backdrop-blur-md border-b border-stone-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-2 group cursor-pointer">
                    <span class="text-2xl group-hover:rotate-12 transition-transform duration-300">👑</span>
                    <span class="font-black text-xl tracking-tight text-stone-900">Admin Central</span>
                </div>
                
                <div class="flex items-center gap-4">
                    <a href="/" class="text-sm font-bold text-stone-500 hover:text-amber-600 transition-colors hidden sm:block">
                        Ir al sitio público
                    </a>
                    <form action="/logout" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="text-xs font-bold text-rose-500 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 px-4 py-2 rounded-xl transition-colors flex items-center gap-2 active:scale-95">
                            <span>🚪</span> Salir
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    {{-- Menú Principal Lúdico --}}
    <main class="flex-1 flex items-center justify-center p-4 sm:p-6">
        <div class="max-w-4xl w-full mx-auto">
            
            {{-- Saludo Amigable --}}
            <div class="text-center mb-12">
                <div class="inline-block text-6xl mb-4 hover:animate-bounce cursor-default transition-transform duration-300">
                    👋
                </div>
                <h1 class="text-4xl md:text-5xl font-black text-stone-900 tracking-tight">
                    ¡Hola, <span class="text-amber-500">{{ explode(' ', Session::get('usuario_logueado')['nombre'] ?? 'Jefe')[0] }}</span>!
                </h1>
                <p class="text-stone-500 mt-3 text-lg font-medium">¿Qué área de la aplicación vamos a revisar hoy?</p>
            </div>

            {{-- Grid de Tarjetas Gigantes --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                
                {{-- Tarjeta 1: Directorio de Eventos --}}
                <a href="/admin/eventos" class="group flex flex-col items-center text-center bg-white p-8 rounded-[3rem] border-2 border-stone-100 shadow-sm hover:shadow-xl hover:border-indigo-200 hover:-translate-y-2 transition-all duration-300">
                    <div class="w-24 h-24 bg-indigo-50 rounded-full flex items-center justify-center text-5xl mb-4 group-hover:scale-110 transition-transform">
                        🎉
                    </div>
                    <h2 class="text-2xl font-black text-stone-800">Celebraciones</h2>
                    <p class="text-stone-500 text-sm mt-2 font-medium">Gestiona y audita todos los eventos activos.</p>
                    <div class="mt-4 px-4 py-1.5 bg-slate-100 text-slate-600 rounded-full text-xs font-bold">
                        {{ count($eventos ?? []) }} eventos
                    </div>
                </a>

                {{-- Tarjeta 2: Tickets de Soporte --}}
                <a href="/admin/soporte" class="group flex flex-col items-center text-center bg-white p-8 rounded-[3rem] border-2 border-stone-100 shadow-sm hover:shadow-xl hover:border-amber-200 hover:-translate-y-2 transition-all duration-300 relative">
                    @if(count($incidencias ?? []) > 0)
                        <span class="absolute top-6 right-6 bg-rose-500 text-white w-8 h-8 flex items-center justify-center rounded-full font-black animate-pulse shadow-md">
                            {{ count($incidencias ?? []) }}
                        </span>
                    @endif
                    <div class="w-24 h-24 bg-amber-50 rounded-full flex items-center justify-center text-5xl mb-4 group-hover:scale-110 transition-transform">
                        🚨
                    </div>
                    <h2 class="text-2xl font-black text-stone-800">Caja de Soporte</h2>
                    <p class="text-stone-500 text-sm mt-2 font-medium">Atiende las solicitudes y reservas de los anfitriones.</p>
                </a>

                {{-- Tarjeta 3: Métricas y Estadísticas --}}
                <a href="/admin/metricas" class="group flex flex-col items-center text-center bg-white p-8 rounded-[3rem] border-2 border-stone-100 shadow-sm hover:shadow-xl hover:border-emerald-200 hover:-translate-y-2 transition-all duration-300">
                    <div class="w-24 h-24 bg-emerald-50 rounded-full flex items-center justify-center text-5xl mb-4 group-hover:scale-110 transition-transform">
                        📊
                    </div>
                    <h2 class="text-2xl font-black text-stone-800">Métricas Globales</h2>
                    <p class="text-stone-500 text-sm mt-2 font-medium">Revisa el rendimiento y estado de la API.</p>
                </a>

                {{-- Tarjeta 4: Usuarios --}}
                <a href="/admin/usuarios" class="group flex flex-col items-center text-center bg-white p-8 rounded-[3rem] border-2 border-stone-100 shadow-sm hover:shadow-xl hover:border-sky-200 hover:-translate-y-2 transition-all duration-300">
                    <div class="w-24 h-24 bg-sky-50 rounded-full flex items-center justify-center text-5xl mb-4 group-hover:scale-110 transition-transform">
                        👥
                    </div>
                    <h2 class="text-2xl font-black text-stone-800">Usuarios</h2>
                    <p class="text-stone-500 text-sm mt-2 font-medium">Administra los accesos de anfitriones e invitados.</p>
                </a>

            </div>
        </div>
    </main>

</body>
</html>