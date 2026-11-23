<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Métricas Globales - Admin InvitApp</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#FDFBF7] text-stone-800 font-sans antialiased selection:bg-amber-500 selection:text-white min-h-screen flex flex-col">

    {{-- Barra de Navegación Lúdica --}}
    <nav class="w-full bg-white/80 backdrop-blur-md border-b border-stone-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="/admin" class="flex items-center gap-2 group cursor-pointer hover:text-emerald-600 transition-colors">
                    <span class="text-xl group-hover:-translate-x-1 transition-transform">👈</span>
                    <span class="font-black text-sm tracking-tight text-stone-500 group-hover:text-emerald-600">Volver al Mando</span>
                </a>
                
                <div class="flex items-center gap-2">
                    <span class="text-2xl hover:animate-spin cursor-default transition-all">📊</span>
                    <span class="font-black text-xl tracking-tight text-stone-900 hidden sm:block">Métricas Globales</span>
                </div>
            </div>
        </div>
    </nav>

    {{-- Contenido Principal --}}
    <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto">
        <div class="max-w-6xl mx-auto space-y-12">
            
            <div class="text-center mb-10 mt-4">
                <h1 class="text-3xl md:text-4xl font-black text-stone-900 tracking-tight">
                    Radiografía del Sistema
                </h1>
                <p class="text-stone-500 mt-2 text-sm font-medium">Monitorea la salud de la plataforma, el inventario de regalos y las confirmaciones en tiempo real.</p>
            </div>

            {{-- SECCIÓN 1: Estado de Celebraciones --}}
            <section>
                <h3 class="text-lg font-black text-stone-800 flex items-center gap-2 mb-6">
                    <span>🍼</span> Estado de Celebraciones en la Plataforma
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div class="bg-white p-6 sm:p-8 rounded-[2.5rem] border-2 border-stone-100 shadow-sm hover:shadow-xl hover:border-emerald-200 hover:-translate-y-1 transition-all duration-300 flex items-center gap-5 group">
                        <div class="w-16 h-16 bg-emerald-50 text-emerald-500 rounded-3xl flex items-center justify-center text-3xl group-hover:scale-110 transition-transform">
                            🌐
                        </div>
                        <div>
                            <p class="text-[10px] text-stone-400 font-black uppercase tracking-wider mb-1">Eventos Publicados</p>
                            <p class="text-4xl font-black text-stone-800">{{ $metricasEvents['publicados'] ?? 0 }}</p>
                        </div>
                    </div>

                    <div class="bg-white p-6 sm:p-8 rounded-[2.5rem] border-2 border-stone-100 shadow-sm hover:shadow-xl hover:border-amber-200 hover:-translate-y-1 transition-all duration-300 flex items-center gap-5 group">
                        <div class="w-16 h-16 bg-amber-50 text-amber-500 rounded-3xl flex items-center justify-center text-3xl group-hover:scale-110 transition-transform">
                            👁️‍🗨️
                        </div>
                        <div>
                            <p class="text-[10px] text-stone-400 font-black uppercase tracking-wider mb-1">Eventos Ocultos</p>
                            <p class="text-4xl font-black text-stone-800">{{ $metricasEvents['ocultos'] ?? 0 }}</p>
                        </div>
                    </div>

                    <div class="bg-white p-6 sm:p-8 rounded-[2.5rem] border-2 border-stone-100 shadow-sm hover:shadow-xl hover:border-rose-200 hover:-translate-y-1 transition-all duration-300 flex items-center gap-5 group">
                        <div class="w-16 h-16 bg-rose-50 text-rose-500 rounded-3xl flex items-center justify-center text-3xl group-hover:scale-110 transition-transform">
                            🔒
                        </div>
                        <div>
                            <p class="text-[10px] text-stone-400 font-black uppercase tracking-wider mb-1">Eventos Cerrados</p>
                            <p class="text-4xl font-black text-stone-800">{{ $metricasEvents['cerrados'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- SECCIÓN 2: Control de Confirmaciones --}}
            <section>
                <h3 class="text-lg font-black text-stone-800 flex items-center gap-2 mb-6">
                    <span>📈</span> Control Global de Asistencias
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div class="bg-white p-6 sm:p-8 rounded-[2.5rem] border-2 border-stone-100 shadow-sm hover:shadow-xl hover:border-blue-200 hover:-translate-y-1 transition-all duration-300 flex items-center gap-5 group">
                        <div class="w-16 h-16 bg-blue-50 text-blue-500 rounded-3xl flex items-center justify-center text-3xl group-hover:scale-110 transition-transform">
                            ✅
                        </div>
                        <div>
                            <p class="text-[10px] text-stone-400 font-black uppercase tracking-wider mb-1">Confirmados</p>
                            <p class="text-4xl font-black text-stone-800">{{ $metricas['confirmados'] ?? 0 }}</p>
                        </div>
                    </div>

                    <div class="bg-white p-6 sm:p-8 rounded-[2.5rem] border-2 border-stone-100 shadow-sm hover:shadow-xl hover:border-red-200 hover:-translate-y-1 transition-all duration-300 flex items-center gap-5 group">
                        <div class="w-16 h-16 bg-red-50 text-red-500 rounded-3xl flex items-center justify-center text-3xl group-hover:scale-110 transition-transform">
                            ❌
                        </div>
                        <div>
                            <p class="text-[10px] text-stone-400 font-black uppercase tracking-wider mb-1">No asistirán</p>
                            <p class="text-4xl font-black text-stone-800">{{ $metricas['rechazados'] ?? 0 }}</p>
                        </div>
                    </div>

                    <div class="bg-white p-6 sm:p-8 rounded-[2.5rem] border-2 border-stone-100 shadow-sm hover:shadow-xl hover:border-orange-200 hover:-translate-y-1 transition-all duration-300 flex items-center gap-5 group">
                        <div class="w-16 h-16 bg-orange-50 text-orange-500 rounded-3xl flex items-center justify-center text-3xl group-hover:scale-110 transition-transform">
                            ⏳
                        </div>
                        <div>
                            <p class="text-[10px] text-stone-400 font-black uppercase tracking-wider mb-1">En Espera</p>
                            <p class="text-4xl font-black text-stone-800">{{ $metricas['pendientes'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- SECCIÓN 3: Estadísticas del Catálogo --}}
            <section>
                <h3 class="text-lg font-black text-stone-800 flex items-center gap-2 mb-6">
                    <span>📦</span> Estadísticas del Catálogo de Regalos
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div class="bg-white p-6 sm:p-8 rounded-[2.5rem] border-2 border-stone-100 shadow-sm hover:shadow-xl hover:border-indigo-200 hover:-translate-y-1 transition-all duration-300 flex items-center gap-5 group">
                        <div class="w-16 h-16 bg-indigo-50 text-indigo-500 rounded-3xl flex items-center justify-center text-3xl group-hover:scale-110 transition-transform">
                            🎁
                        </div>
                        <div>
                            <p class="text-[10px] text-stone-400 font-black uppercase tracking-wider mb-1">Total Catálogo Global</p>
                            <p class="text-4xl font-black text-stone-800">{{ isset($gifts) ? $gifts->count() : 0 }}</p>
                        </div>
                    </div>

                    <div class="bg-white p-6 sm:p-8 rounded-[2.5rem] border-2 border-stone-100 shadow-sm hover:shadow-xl hover:border-teal-200 hover:-translate-y-1 transition-all duration-300 flex items-center gap-5 group">
                        <div class="w-16 h-16 bg-teal-50 text-teal-500 rounded-3xl flex items-center justify-center text-3xl group-hover:scale-110 transition-transform">
                            🛍️
                        </div>
                        <div>
                            <p class="text-[10px] text-stone-400 font-black uppercase tracking-wider mb-1">Total Reservados</p>
                            <p class="text-4xl font-black text-stone-800">{{ isset($gifts) ? $gifts->where('estado', 'reservado')->count() : 0 }}</p>
                        </div>
                    </div>

                    <div class="bg-white p-6 sm:p-8 rounded-[2.5rem] border-2 border-stone-100 shadow-sm hover:shadow-xl hover:border-sky-200 hover:-translate-y-1 transition-all duration-300 flex items-center gap-5 group">
                        <div class="w-16 h-16 bg-sky-50 text-sky-500 rounded-3xl flex items-center justify-center text-3xl group-hover:scale-110 transition-transform">
                            ✨
                        </div>
                        <div>
                            <p class="text-[10px] text-stone-400 font-black uppercase tracking-wider mb-1">Total Disponibles</p>
                            <p class="text-4xl font-black text-stone-800">{{ isset($gifts) ? $gifts->where('estado', '!=', 'reservado')->count() : 0 }}</p>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </main>

</body>
</html>