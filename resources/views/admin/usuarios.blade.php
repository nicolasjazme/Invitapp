<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - Admin Invitapp</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-[#FDFBF7] text-stone-800 font-sans antialiased selection:bg-sky-500 selection:text-white min-h-screen flex flex-col">

    {{-- Barra de Navegación Lúdica --}}
    <nav class="w-full bg-white/80 backdrop-blur-md border-b border-stone-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="/admin" class="flex items-center gap-2 group cursor-pointer hover:text-sky-600 transition-colors">
                    <span class="text-xl group-hover:-translate-x-1 transition-transform">👈</span>
                    <span class="font-black text-sm tracking-tight text-stone-500 group-hover:text-sky-600">Volver al Mando</span>
                </a>
                
                <div class="flex items-center gap-2">
                    <span class="text-2xl hover:scale-110 transition-transform cursor-default">👥</span>
                    <span class="font-black text-xl tracking-tight text-stone-900 hidden sm:block">Comunidad</span>
                </div>
            </div>
        </div>
    </nav>

    {{-- Contenido Principal --}}
    <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto">
        <div class="max-w-7xl mx-auto space-y-8">
            
            <div class="text-center mb-10 mt-4">
                <h1 class="text-3xl md:text-4xl font-black text-stone-900 tracking-tight">
                    Directorio de Usuarios
                </h1>
                <p class="text-stone-500 mt-2 text-sm font-medium">Administra los accesos y revisa quién forma parte de Invitapp.</p>
            </div>

            {{-- Grid de Tarjetas de Perfil --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                
                @forelse($usuarios ?? [] as $usuario)
                    <div class="bg-white p-6 rounded-[2.5rem] border-2 border-stone-100 shadow-sm hover:shadow-xl hover:border-sky-200 hover:-translate-y-1 transition-all duration-300 flex flex-col items-center text-center group relative">
                        
                        {{-- Identificador de Rol Visual --}}
                        <div class="absolute top-4 right-4">
                            @if(($usuario['rol'] ?? '') === 'administrador')
                                <span class="text-xl" title="Administrador">👑</span>
                            @elseif(($usuario['rol'] ?? '') === 'anfitrion')
                                <span class="text-xl" title="Anfitrión">📋</span>
                            @else
                                <span class="text-xl" title="Invitado">👥</span>
                            @endif
                        </div>

                        {{-- Avatar Dinámico (Letra Inicial) --}}
                        <div class="w-20 h-20 rounded-[1.5rem] flex items-center justify-center text-3xl font-black mb-4 group-hover:scale-110 transition-transform shadow-inner
                            {{ ($usuario['rol'] ?? '') === 'administrador' ? 'bg-indigo-50 text-indigo-600' : 
                               (($usuario['rol'] ?? '') === 'anfitrion' ? 'bg-amber-50 text-amber-600' : 'bg-sky-50 text-sky-600') }}">
                            {{ strtoupper(substr($usuario['nombre'] ?? 'U', 0, 1)) }}
                        </div>
                        
                        <h3 class="text-lg font-black text-stone-800 leading-tight w-full truncate px-2">
                            {{ $usuario['nombre'] ?? 'Usuario Sin Nombre' }}
                        </h3>
                        <p class="text-xs text-stone-400 font-bold mb-4 truncate w-full px-2">
                            {{ $usuario['correo'] ?? 'correo@oculto.com' }}
                        </p>
                        
                        <div class="w-full space-y-2 mb-5">
                            <span class="inline-block px-3 py-1 bg-stone-50 border border-stone-100 text-stone-600 rounded-lg text-[10px] font-black uppercase tracking-wider w-full">
                                Rol: {{ $usuario['rol'] ?? 'invitado' }}
                            </span>
                        </div>

                        <button onclick="gestionarUsuario('{{ $usuario['_id'] ?? $usuario['id'] }}', '{{ addslashes($usuario['nombre'] ?? '') }}')" 
                                class="w-full py-3 bg-stone-900 hover:bg-stone-800 text-white font-extrabold text-xs uppercase tracking-wider rounded-xl transition-colors shadow-sm active:scale-95 mt-auto">
                            ⚙️ Gestionar
                        </button>
                    </div>
                @empty
                    <div class="col-span-full flex flex-col items-center justify-center p-12 bg-white rounded-[3rem] border-2 border-dashed border-stone-200 text-center">
                        <span class="text-6xl mb-4 opacity-50 hover:animate-bounce transition-all">👻</span>
                        <h3 class="text-2xl font-black text-stone-800">No hay usuarios</h3>
                        <p class="text-stone-500 text-sm mt-2 font-medium">Parece que la base de datos aún no tiene registros.</p>
                    </div>
                @endforelse
                
            </div>

        </div>
    </main>

    {{-- Script de Microinteracciones --}}
    <script>
    function gestionarUsuario(id, nombre) {
        Swal.fire({
            title: `⚙️ Opciones de Cuenta`,
            html: `
                <div class="mt-2 text-stone-600 text-sm font-medium">
                    ¿Qué deseas hacer con la cuenta de <strong class="text-stone-900">${nombre}</strong>?
                </div>
                <div class="mt-4 text-left text-xs bg-stone-50 p-3 rounded-2xl border border-stone-100">
                    ID del Usuario: <code class="bg-white px-2 py-1 rounded text-sky-600 font-bold block mt-1">${id}</code>
                </div>
            `,
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: '🔓 Restablecer Clave',
            denyButtonText: '🛑 Suspender',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#0ea5e9',
            denyButtonColor: '#ef4444',
            cancelButtonColor: '#78716c',
            customClass: { popup: 'rounded-[2.5rem]' }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    icon: 'success',
                    title: 'Clave Restablecida',
                    text: 'Se enviarán las instrucciones al correo del usuario.',
                    customClass: { popup: 'rounded-[2.5rem]' },
                    confirmButtonColor: '#0ea5e9'
                });
            } else if (result.isDenied) {
                Swal.fire({
                    icon: 'info',
                    title: 'Cuenta Suspendida',
                    text: 'El usuario ya no podrá acceder al sistema.',
                    customClass: { popup: 'rounded-[2.5rem]' },
                    confirmButtonColor: '#ef4444'
                });
            }
        });
    }
    </script>
</body>
</html>