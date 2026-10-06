<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $evento['titulo'] ?? 'Invitación Especial' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Incluimos SweetAlert2 para mostrar notificaciones bonitas -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gradient-to-br {{ $estiloTema['bg_gradient'] ?? 'from-slate-100 to-slate-200' }} min-h-screen flex items-center justify-center p-4 font-sans antialiased">

    <!-- Tarjeta Principal (Mobile First) -->
    <div class="bg-white max-w-md w-full rounded-[2.5rem] shadow-2xl overflow-hidden relative animate-fade-in-up">
        
        <!-- Cabecera de Color Temático -->
        <div class="h-32 {{ preg_replace('/hover:bg-[a-z]+-[0-9]+ text-white/', '', $estiloTema['color_boton'] ?? 'bg-indigo-600') }} relative flex items-center justify-center">
            <!-- Patrón de fondo opcional -->
            <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(#fff 2px, transparent 2px); background-size: 20px 20px;"></div>
            
            <!-- Círculo del Emoji -->
            <div class="absolute -bottom-10 bg-white w-24 h-24 flex items-center justify-center rounded-full shadow-lg border-4 border-white text-5xl z-10">
                {{ $estiloTema['emoji'] ?? '🎉' }}
            </div>
        </div>

        <div class="px-8 pt-14 pb-8 text-center relative z-0">
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">¡Estás Invitado!</span>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight mb-3 leading-tight">
                {{ $evento['titulo'] ?? 'Mi Celebración' }}
            </h1>
            <p class="text-slate-500 text-sm mb-6 leading-relaxed">
                {{ $evento['mensajeBienvenida'] ?? 'Nos encantaría contar con tu presencia en este día tan especial.' }}
            </p>

            <!-- Detalles del Evento (Fecha y Lugar) -->
            <div class="bg-slate-50 rounded-3xl p-5 mb-8 text-left space-y-4 border border-slate-100">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-slate-500 shadow-sm shrink-0">
                        <i class="fa-solid fa-calendar-day"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Cuándo</p>
                        <p class="text-sm font-bold text-slate-700">
                            {{ isset($evento['fecha']) ? \Carbon\Carbon::parse($evento['fecha'])->locale('es')->isoFormat('D [de] MMMM, YYYY') : 'Fecha por definir' }}
                            <span class="font-normal text-slate-500">a las {{ $evento['hora'] ?? '12:00' }}</span>
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-slate-500 shadow-sm shrink-0">
                        <i class="fa-solid fa-location-dot"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Dónde</p>
                        <p class="text-sm font-bold text-slate-700">{{ $evento['lugar'] ?? 'Por definir' }}</p>
                        <p class="text-xs text-slate-500">{{ $evento['direccion'] ?? '' }}</p>
                    </div>
                </div>
            </div>

            <!-- Zona dinámica: Formulario RSVP o Ticket QR -->
            @if(session('ticket_generado'))
                <!-- VISTA 2: EL INVITADO YA CONFIRMÓ (MUESTRA EL QR) -->
                <div class="animate-fade-in-up">
                    <div class="inline-block p-2 bg-white border-2 border-slate-100 rounded-2xl shadow-sm mb-4">
                        <!-- Usamos una API gratuita y rápida para generar el QR en base al correo/id del invitado -->
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ session('qr_data') }}" alt="Tu código de acceso" class="w-40 h-40 rounded-xl mx-auto">
                    </div>
                    <h3 class="text-lg font-black text-green-600 mb-1">¡Asistencia Confirmada! ✅</h3>
                    <p class="text-xs text-slate-500">Toma una captura de pantalla de este código QR. Te lo pediremos en la entrada.</p>
                </div>
            @else
                <!-- VISTA 1: FORMULARIO DE CONFIRMACIÓN -->
                <div id="rsvp-section">
                    <h3 class="font-bold text-slate-800 mb-4 text-sm uppercase tracking-wider">Confirma tu asistencia</h3>
                    
                    <form action="{{ route('asistencia.store') }}" method="POST" class="space-y-4 text-left">
                        @csrf
                        <!-- Datos ocultos para el backend -->
                        <input type="hidden" name="evento_id" value="{{ $evento['_id'] ?? $evento['id'] }}">
                        <input type="hidden" name="slug" value="{{ $evento['slug'] ?? '' }}">

                        <div>
                            <input type="text" name="nombre" required placeholder="Tu nombre y apellido" class="w-full border-2 border-slate-200 p-3.5 rounded-2xl outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 bg-white text-sm font-medium transition-all shadow-sm">
                        </div>
                        <div>
                            <input type="email" name="correo" required placeholder="Tu correo electrónico" class="w-full border-2 border-slate-200 p-3.5 rounded-2xl outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 bg-white text-sm font-medium transition-all shadow-sm">
                        </div>

                        <div class="grid grid-cols-2 gap-3 pt-2">
                            <!-- Botón Confirmar (Usa el color de la temática) -->
                            <button type="submit" name="estadoConfirmacion" value="confirmado" class="w-full {{ $estiloTema['color_boton'] ?? 'bg-indigo-600 text-white' }} font-bold py-3.5 rounded-2xl transition-all shadow-md hover:-translate-y-1 active:scale-95 text-sm flex justify-center items-center gap-2">
                                Sí, voy ✅
                            </button>
                            <!-- Botón Rechazar -->
                            <button type="submit" name="estadoConfirmacion" value="rechazado" class="w-full bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-3.5 rounded-2xl transition-all active:scale-95 text-sm">
                                No podré ❌
                            </button>
                        </div>
                    </form>
                </div>
            @endif

        </div>
    </div>

    <!-- Alertas de error desde el controlador -->
    @if(session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '{{ session("error") }}',
                confirmButtonColor: '#4f46e5'
            });
        </script>
    @endif

    <style>
        @keyframes fadeInUp {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
    </style>
</body>
</html>