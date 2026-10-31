<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caja de Soporte - Admin Invita App</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-[#FDFBF7] text-stone-800 font-sans antialiased selection:bg-amber-500 selection:text-white min-h-screen flex flex-col">

    {{-- Barra de Navegación Lúdica --}}
    <nav class="w-full bg-white/80 backdrop-blur-md border-b border-stone-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="/admin" class="flex items-center gap-2 group cursor-pointer hover:text-amber-600 transition-colors">
                    <span class="text-xl group-hover:-translate-x-1 transition-transform">👈</span>
                    <span class="font-black text-sm tracking-tight text-stone-500 group-hover:text-amber-600">Volver al Mando</span>
                </a>
                
                <div class="flex items-center gap-2">
                    <span class="text-2xl animate-pulse">🚨</span>
                    <span class="font-black text-xl tracking-tight text-stone-900 hidden sm:block">Caja de Soporte</span>
                </div>
            </div>
        </div>
    </nav>

    {{-- Contenido Principal --}}
    <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto">
        <div class="max-w-5xl mx-auto space-y-8">
            
            <div class="text-center mb-10 mt-4">
                <h1 class="text-3xl md:text-4xl font-black text-stone-900 tracking-tight">
                    Bandeja de Entrada
                </h1>
                <p class="text-stone-500 mt-2 text-sm font-medium">Atiende las solicitudes de los anfitriones y mantén las celebraciones a salvo.</p>
            </div>

            @if(session('success'))
                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Resuelto!',
                            text: "{{ session('success') }}",
                            confirmButtonColor: '#f59e0b',
                            customClass: { popup: 'rounded-[2.5rem]' }
                        });
                    });
                </script>
            @endif

            {{-- Grid de Tickets Lúdicos --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if(isset($incidencias) && count($incidencias) > 0)
                    @foreach($incidencias as $incidencia)
                        <div class="bg-white p-6 sm:p-8 rounded-[2.5rem] border-2 border-amber-100 shadow-sm hover:shadow-xl hover:-translate-y-1 hover:border-amber-300 transition-all duration-300 flex flex-col justify-between group relative">
                            
                            {{-- Etiqueta de Espera --}}
                            <div class="absolute -top-3 -right-3 bg-rose-500 text-white text-[10px] font-black uppercase tracking-wider px-4 py-1.5 rounded-full shadow-md animate-bounce">
                                Pendiente
                            </div>

                            <div>
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                                        👤
                                    </div>
                                    <div>
                                        <h3 class="font-black text-stone-800 text-lg leading-tight">{{ $incidencia['anfitrion'] ?? 'Anfitrión' }}</h3>
                                        <p class="text-xs text-stone-400 font-bold">{{ date('d/m/Y - H:i', strtotime($incidencia['createdAt'] ?? now())) }}</p>
                                    </div>
                                </div>
                                
                                <div class="relative bg-amber-50/50 p-5 rounded-3xl border border-amber-100 mb-6">
                                    <span class="absolute -top-3 left-4 text-2xl">💬</span>
                                    <p class="text-stone-700 text-sm font-medium leading-relaxed pt-2 italic">
                                        "{{ $incidencia['mensaje'] ?? 'Sin detalles' }}"
                                    </p>
                                </div>
                            </div>

                            {{-- Botón de Resolución mediante Formulario --}}
                            <form action="{{ route('admin.incidencias.complete', $incidencia['_id'] ?? $loop->index) }}" method="POST" class="m-0" onsubmit="confirmarResolucion(event, this)">
                                @csrf
                                <button type="submit" class="w-full py-3 bg-stone-900 hover:bg-stone-800 text-white font-extrabold text-xs uppercase tracking-wider rounded-xl transition-all shadow-md active:scale-95 flex items-center justify-center gap-2">
                                    <span class="text-emerald-400 text-base">✓</span> Marcar como Resuelto
                                </button>
                            </form>
                        </div>
                    @endforeach
                @else
                    <div class="col-span-full flex flex-col items-center justify-center p-12 bg-white rounded-[3rem] border-2 border-dashed border-stone-200 text-center">
                        <span class="text-6xl mb-4 hover:rotate-12 transition-transform cursor-default">🎉</span>
                        <h3 class="text-2xl font-black text-stone-800">¡Bandeja Limpia!</h3>
                        <p class="text-stone-500 text-sm mt-2 font-medium max-w-md mx-auto">No hay incidencias pendientes. Todos los anfitriones están felices y sin problemas en sus eventos.</p>
                    </div>
                @endif
            </div>

        </div>
    </main>

    <script>
    function confirmarResolucion(event, form) {
        event.preventDefault();
        Swal.fire({
            title: '¿Misión Cumplida?',
            text: "El ticket desaparecerá de la bandeja y el anfitrión quedará tranquilo.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#78716c',
            confirmButtonText: 'Sí, ¡resuelto!',
            cancelButtonText: 'Aún no',
            customClass: { popup: 'rounded-[2.5rem]' }
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }
    </script>
</body>
</html>