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
@php
    $eventoId = $evento['_id'] ?? $evento['id'] ?? '';
    $tipoEvento = $evento['tipo_evento'] ?? 'baby_shower';
    $modulosActivos = $evento['modulos_activos'] ?? [];
    if (empty($modulosActivos) && $tipoEvento !== 'personalizado') {
        $plantillas = [
            'baby_shower' => ['regalos', 'itinerario', 'menu'],
            'babyshower'  => ['regalos', 'itinerario', 'menu'],
            'matrimonio'  => ['regalos', 'mesas', 'itinerario', 'menu', 'galeria'],
            'cumpleanos'  => ['itinerario', 'avisos', 'musica', 'galeria'],
            'asado'       => ['cuotas', 'itinerario', 'insumos', 'musica'],
            'fiesta'      => ['itinerario', 'avisos', 'musica', 'checkin'],
        ];
        $modulosActivos = $plantillas[$tipoEvento] ?? ['regalos'];
    }
    $mostrarRegalos = in_array('regalos', $modulosActivos) || in_array($tipoEvento, ['baby_shower', 'babyshower', 'matrimonio']);
@endphp
<body class="bg-gradient-to-br {{ $estiloTema['bg_gradient'] ?? 'from-slate-100 to-slate-200' }} min-h-screen flex flex-col items-center justify-center py-10 px-4 font-sans antialiased space-y-6">

    <!-- 1. Tarjeta Principal de Invitación y RSVP -->
    <div class="bg-white max-w-md w-full rounded-[2.5rem] shadow-2xl overflow-hidden relative animate-fade-in-up">
        
        <!-- Cabecera de Color Temático -->
        <div class="h-32 {{ preg_replace('/hover:bg-[a-z]+-[0-9]+ text-white/', '', $estiloTema['color_boton'] ?? 'bg-indigo-600') }} relative flex items-center justify-center">
            <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(#fff 2px, transparent 2px); background-size: 20px 20px;"></div>
            
            <!-- Círculo del Emoji -->
            <div class="absolute -bottom-10 bg-white w-24 h-24 flex items-center justify-center rounded-full shadow-lg border-4 border-white text-5xl z-10">
                {{ $estiloTema['emoji'] ?? ($evento['configVisual']['iconoEmoji'] ?? '🎉') }}
            </div>
        </div>

        <div class="px-8 pt-14 pb-8 text-center relative z-0">
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">¡Estás Invitado!</span>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight mb-3 leading-tight">
                {{ $evento['titulo'] ?? 'Mi Celebración' }}
            </h1>
            <p class="text-slate-500 text-sm mb-6 leading-relaxed">
                {{ $evento['mensajeBienvenida'] ?? $evento['mensaje_bienvenida'] ?? 'Nos encantaría contar con tu presencia en este día tan especial.' }}
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
                        <p class="text-sm font-bold text-slate-700">{{ $evento['lugar'] ?? $evento['ubicacion'] ?? 'Por definir' }}</p>
                        <p class="text-xs text-slate-500">{{ $evento['direccion'] ?? '' }}</p>
                    </div>
                </div>
            </div>

            <!-- Zona dinámica: Formulario RSVP o Ticket QR -->
            @if(session('ticket_generado'))
                <!-- VISTA 2: EL INVITADO YA CONFIRMÓ (MUESTRA EL QR) -->
                <div class="animate-fade-in-up">
                    <div class="inline-block p-2 bg-white border-2 border-slate-100 rounded-2xl shadow-sm mb-4">
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
                        <input type="hidden" name="evento_id" value="{{ $eventoId }}">
                        <input type="hidden" name="slug" value="{{ $evento['slug'] ?? '' }}">

                        <div>
                            <input type="text" id="rsvp-nombre" name="nombre" required placeholder="Tu nombre y apellido" class="w-full border-2 border-slate-200 p-3.5 rounded-2xl outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 bg-white text-sm font-medium transition-all shadow-sm">
                        </div>
                        <div>
                            <input type="email" id="rsvp-correo" name="correo" required placeholder="Tu correo electrónico" class="w-full border-2 border-slate-200 p-3.5 rounded-2xl outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 bg-white text-sm font-medium transition-all shadow-sm">
                        </div>

                        <div class="grid grid-cols-2 gap-3 pt-2">
                            <button type="submit" name="estadoConfirmacion" value="confirmado" class="w-full {{ $estiloTema['color_boton'] ?? 'bg-indigo-600 text-white' }} font-bold py-3.5 rounded-2xl transition-all shadow-md hover:-translate-y-1 active:scale-95 text-sm flex justify-center items-center gap-2 cursor-pointer">
                                Sí, voy ✅
                            </button>
                            <button type="submit" name="estadoConfirmacion" value="rechazado" class="w-full bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-3.5 rounded-2xl transition-all active:scale-95 text-sm cursor-pointer">
                                No podré ❌
                            </button>
                        </div>
                    </form>
                </div>
            @endif

        </div>
    </div>

    <!-- 2. Sección de Lista de Deseos (Regalos) para el Invitado -->
    @if($mostrarRegalos && !empty($eventoId))
    <div class="bg-white max-w-2xl w-full rounded-[2.5rem] shadow-2xl p-6 sm:p-8 animate-fade-in-up">
        <div class="text-center mb-6">
            <span class="text-3xl block mb-1">🎁</span>
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">Lista de Deseos</h2>
            <p class="text-slate-500 text-xs mt-1 max-w-md mx-auto">
                Si deseas hacernos un presente, puedes elegir uno de esta lista para que no se repita.
            </p>
        </div>

        <div id="contenedor-regalos-invitado" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="col-span-full text-center py-10">
                <i class="fa-solid fa-spinner fa-spin text-rose-500 text-2xl mb-2"></i>
                <p class="text-xs text-slate-400 font-medium">Cargando lista de deseos...</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal para que el invitado reserve su regalo -->
    <div id="modal-reservar-invitado" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full p-8 shadow-2xl border border-slate-100 relative">
            <button onclick="cerrarModalReservaInvitado()" class="absolute top-6 right-6 text-slate-400 hover:text-slate-600 cursor-pointer">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>

            <div class="text-center mb-6">
                <span class="text-4xl block mb-2">🎁</span>
                <h3 class="text-xl font-black text-slate-900">Reservar este Regalo</h3>
                <p id="nombre-regalo-modal" class="text-sm font-bold text-rose-500 mt-1"></p>
            </div>

            <form id="form-reservar-invitado" onsubmit="confirmarReservaInvitado(event)" class="space-y-4">
                <input type="hidden" id="regalo-id-modal">

                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Tu Nombre y Apellido *</label>
                    <input type="text" id="guest-name-modal" required placeholder="Ej. María González"
                           class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-rose-200 text-sm font-medium">
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Tu Correo Electrónico *</label>
                    <input type="email" id="guest-email-modal" required placeholder="Para enviarte el comprobante del regalo"
                           class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-rose-200 text-sm font-medium">
                </div>

                <div class="pt-3 flex gap-3">
                    <button type="button" onclick="cerrarModalReservaInvitado()" class="w-1/2 py-3 rounded-2xl border border-slate-200 text-slate-500 font-bold text-xs uppercase hover:bg-slate-50 cursor-pointer">
                        Cancelar
                    </button>
                    <button type="submit" id="btn-submit-reserva" class="w-1/2 py-3 rounded-2xl bg-rose-500 hover:bg-rose-600 text-white font-extrabold text-xs uppercase tracking-wider shadow-md cursor-pointer">
                        ¡Yo lo regalo!
                    </button>
                </div>
            </form>
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

    @if($mostrarRegalos && !empty($eventoId))
    <script>
        const EVENTO_ID = '{{ $eventoId }}';
        const TOKEN = '{{ Session::get("token_jwt") }}';
        const API_REGALOS_URL = `http://localhost:3000/api/eventos/${EVENTO_ID}/regalos`;

        document.addEventListener('DOMContentLoaded', cargarRegalosInvitado);

        function cargarRegalosInvitado() {
            fetch(API_REGALOS_URL)
                .then(res => res.json())
                .then(data => renderizarRegalosInvitado(data))
                .catch(err => {
                    console.error("Error cargando regalos:", err);
                    document.getElementById('contenedor-regalos-invitado').innerHTML = `
                        <div class="col-span-full text-center py-6 text-slate-400 text-xs">
                            No se pudo cargar la lista de regalos en este momento.
                        </div>
                    `;
                });
        }

        function renderizarRegalosInvitado(response) {
            const contenedor = document.getElementById('contenedor-regalos-invitado');
            const lista = Array.isArray(response) ? response : (response.regalos || []);

            if (!lista || lista.length === 0) {
                contenedor.innerHTML = `
                    <div class="col-span-full text-center py-8 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                        <p class="text-slate-400 text-xs font-medium">El anfitrión aún no ha agregado regalos a la lista.</p>
                    </div>
                `;
                return;
            }

            contenedor.innerHTML = lista.map(regalo => {
                const id = regalo._id || regalo.id;
                const solicitada = regalo.cantidad_solicitada || 1;
                const disponible = regalo.cantidad_disponible ?? solicitada;
                const agotado = regalo.estado === 'reservado' || disponible <= 0;
                const link = regalo.link_referencia || regalo.link_externo || '';
                const imagen = regalo.url_imagen || regalo.imagen || '';
                const nombreSeguro = (regalo.nombre || '').replace(/'/g, "\\'");

                const badgeStock = agotado
                    ? `<span class="text-[10px] font-bold px-2.5 py-1 bg-slate-200 text-slate-600 rounded-full uppercase">🔒 Ya Reservado</span>`
                    : `<span class="text-[10px] font-bold px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-full uppercase">✨ ${solicitada > 1 ? `${disponible} disponibles` : 'Disponible'}</span>`;

                const bloqueImagen = imagen
                    ? `<div class="w-full h-40 rounded-xl overflow-hidden bg-white mb-3 border border-slate-100 flex items-center justify-center">
                           <img src="${imagen}" alt="${regalo.nombre}" referrerpolicy="no-referrer" class="w-full h-full object-contain p-2" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\\'w-full h-full flex items-center justify-center text-3xl bg-rose-50/60\\'>🎁</div>';">
                       </div>`
                    : `<div class="w-full h-32 rounded-xl bg-rose-50/50 border border-rose-100/60 flex items-center justify-center text-3xl mb-3">
                           🎁
                       </div>`;

                return `
                    <div class="border border-slate-100 rounded-2xl p-4 bg-slate-50/60 flex flex-col justify-between hover:border-rose-200 transition-all shadow-xs">
                        <div>
                            <div class="flex justify-between items-center mb-3">
                                ${badgeStock}
                                ${link ? `
                                    <a href="${link}" target="_blank" rel="noopener noreferrer" class="text-[11px] font-bold text-rose-500 hover:text-rose-600 flex items-center gap-1">
                                        <i class="fa-solid fa-arrow-up-right-from-square"></i> Ver Tienda
                                    </a>
                                ` : ''}
                            </div>

                            ${bloqueImagen}

                            <h3 class="font-bold text-slate-800 text-base line-clamp-1">${regalo.nombre}</h3>
                            <p class="text-xs text-slate-500 mt-1 line-clamp-2">${regalo.descripcion || 'Sin descripción adicional.'}</p>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-100">
                            ${agotado ? `
                                <button disabled class="w-full py-2.5 rounded-xl bg-slate-200 text-slate-500 font-bold text-xs uppercase cursor-not-allowed">
                                    Regalo Comprometido
                                </button>
                            ` : `
                                <button onclick="abrirModalReservaInvitado('${id}', '${nombreSeguro}')" class="w-full py-2.5 rounded-xl bg-rose-500 hover:bg-rose-600 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-sm cursor-pointer">
                                    🎁 ¡Yo lo regalo!
                                </button>
                            `}
                        </div>
                    </div>
                `;
            }).join('');
        }

        function abrirModalReservaInvitado(id, nombre) {
            document.getElementById('regalo-id-modal').value = id;
            document.getElementById('nombre-regalo-modal').innerText = nombre;

            // Si ya escribió su nombre/correo arriba en el formulario de asistencia, se lo autocompletamos
            const rsvpNombre = document.getElementById('rsvp-nombre')?.value || '';
            const rsvpCorreo = document.getElementById('rsvp-correo')?.value || '';
            if (rsvpNombre && !document.getElementById('guest-name-modal').value) {
                document.getElementById('guest-name-modal').value = rsvpNombre;
            }
            if (rsvpCorreo && !document.getElementById('guest-email-modal').value) {
                document.getElementById('guest-email-modal').value = rsvpCorreo;
            }

            document.getElementById('modal-reservar-invitado').classList.remove('hidden');
        }

        function cerrarModalReservaInvitado() {
            document.getElementById('modal-reservar-invitado').classList.add('hidden');
            document.getElementById('form-reservar-invitado').reset();
        }

        function confirmarReservaInvitado(e) {
            e.preventDefault();
            const id = document.getElementById('regalo-id-modal').value;
            const guest_name = document.getElementById('guest-name-modal').value;
            const guest_email = document.getElementById('guest-email-modal').value;

            const headers = {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            };
            if (TOKEN) headers['Authorization'] = `Bearer ${TOKEN}`;

            fetch(`${API_REGALOS_URL}/${id}/reservar`, {
                method: 'PUT',
                headers: headers,
                body: JSON.stringify({ guest_name, guest_email })
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) throw new Error(data.mensaje || 'No se pudo reservar el regalo');
                return data;
            })
            .then(() => {
                cerrarModalReservaInvitado();
                cargarRegalosInvitado();
                Swal.fire({
                    icon: 'success',
                    title: '¡Muchas gracias! 💖',
                    text: 'El regalo quedó reservado a tu nombre y te enviamos un correo de confirmación.',
                    confirmButtonColor: '#f43f5e',
                    customClass: { popup: 'rounded-3xl' }
                });
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Atención', err.message, 'warning');
            });
        }
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