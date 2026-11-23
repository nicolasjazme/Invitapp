<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InvitApp - Organiza tus Eventos</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased selection:bg-indigo-500 selection:text-white">

    <!-- Navbar -->
    <nav class="w-full bg-white/80 backdrop-blur-md border-b border-slate-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <span class="text-2xl">✨</span>
                    <span class="font-black text-xl tracking-tight text-slate-900">Invitapp</span>
                </a>

                <div class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-600">
                    <a href="#como-funciona" class="hover:text-indigo-600 transition-colors">¿Cómo funciona?</a>
                    <a href="#herramientas" class="hover:text-indigo-600 transition-colors">Herramientas</a>
                    <a href="#vista-previa" class="hover:text-indigo-600 transition-colors">Vista previa</a>
                    <a href="#celebraciones" class="hover:text-indigo-600 transition-colors">Celebraciones</a>
                </div>

                <!-- Botón de Login o Panel Dinámico -->
                <div class="flex items-center gap-3">
                    @if(Session::has('usuario_logueado'))
                        @if(Session::get('usuario_logueado')['rol'] === 'administrador')
                            <a href="/admin" class="text-sm font-bold text-indigo-600 hover:text-indigo-800 transition-colors bg-indigo-50 px-4 py-2 rounded-full border border-indigo-100 flex items-center gap-2">
                                Panel Admin ⚙️
                            </a>
                        @elseif(Session::get('usuario_logueado')['rol'] === 'anfitrion')
                            <a href="/anfitrion" class="text-sm font-bold text-indigo-600 hover:text-indigo-800 transition-colors bg-indigo-50 px-4 py-2 rounded-full border border-indigo-100 flex items-center gap-2">
                                Mi Panel 🚀
                            </a>
                        @else
                            <a href="/perfil" class="text-sm font-bold text-indigo-600 hover:text-indigo-800 transition-colors bg-indigo-50 px-4 py-2 rounded-full border border-indigo-100 flex items-center gap-2">
                                Mi Perfil 👤
                            </a>
                        @endif
                    @else
                        <a href="/login" class="text-sm font-bold text-slate-600 hover:text-indigo-600 transition-colors px-3 py-2">
                            Iniciar Sesión
                        </a>
                        <button type="button" onclick="abrirMenuEventos()" class="hidden sm:inline-flex text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition-colors px-4 py-2 rounded-full shadow-sm">
                            Empezar gratis
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Principal -->
    <section class="relative overflow-hidden py-16 sm:py-24 px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-700 text-xs font-bold uppercase tracking-wider mb-6">
                🎉 Invitaciones digitales e interactivas
            </span>
            <h1 class="text-4xl sm:text-6xl font-extrabold text-slate-900 tracking-tight mb-6 leading-tight">
                Organiza tu evento y sorprende a tus invitados con <span class="text-indigo-600">Invitapp</span>
            </h1>
            <p class="text-lg sm:text-xl text-slate-600 mb-10 leading-relaxed">
                Crea invitaciones con enlace único, recibe confirmaciones en tiempo real y coordina listas de regalos sin duplicados ni complicaciones.
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <button type="button" onclick="abrirMenuEventos()" class="w-full sm:w-auto inline-flex items-center justify-center gap-3 bg-indigo-600 text-white px-8 py-4 rounded-full font-bold shadow-xl hover:bg-indigo-700 hover:shadow-2xl transition-all transform hover:-translate-y-0.5 active:scale-95 text-lg">
                    <span class="text-2xl">✨</span>
                    <span>Crear Nuevo Evento</span>
                </button>
                <a href="#como-funciona" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-white text-slate-700 border border-slate-200 px-8 py-4 rounded-full font-bold hover:bg-slate-100 transition-colors text-base">
                    Ver cómo funciona
                </a>
            </div>
        </div>
    </section>

    <!-- Cómo funciona -->
    <section id="como-funciona" class="py-16 bg-white border-y border-slate-200/70 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <h2 class="text-3xl font-extrabold text-slate-900 mb-3">Todo listo en 3 pasos simples</h2>
                <p class="text-slate-600">Pensado para que el anfitrión tenga el control y los invitados confirmen con un solo clic.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="p-6 rounded-3xl bg-slate-50 border border-slate-100">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-600 text-white font-black flex items-center justify-center text-lg mb-4">1</div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Elige tu tipo de evento</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">Selecciona entre Baby Shower, Matrimonio, Cumpleaños, Asado o Fiesta y configura los detalles clave en minutos.</p>
                </div>

                <div class="p-6 rounded-3xl bg-slate-50 border border-slate-100">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-600 text-white font-black flex items-center justify-center text-lg mb-4">2</div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Comparte tu enlace mágico</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">Tus invitados entran directo desde su celular sin crear contraseñas para confirmar asistencia o reservar un regalo.</p>
                </div>

                <div class="p-6 rounded-3xl bg-slate-50 border border-slate-100">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-600 text-white font-black flex items-center justify-center text-lg mb-4">3</div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Monitorea en tiempo real</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">Revisa quién confirmó, el stock disponible de regalos y organiza mesas o cuotas desde un mismo panel.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Herramientas / Módulos -->
    <section id="herramientas" class="py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <h2 class="text-3xl font-extrabold text-slate-900 mb-3">¿Qué puedes gestionar dentro?</h2>
                <p class="text-slate-600">Herramientas diseñadas para evitar planillas eternas y mensajes repetidos.</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
                    <span class="text-3xl block mb-3">💌</span>
                    <h3 class="font-bold text-slate-900 mb-1">Invitación Mágica</h3>
                    <p class="text-sm text-slate-600">Acceso directo para invitados con información del lugar, fecha y confirmación inmediata.</p>
                </div>

                <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
                    <span class="text-3xl block mb-3">🎁</span>
                    <h3 class="font-bold text-slate-900 mb-1">Lista de Regalos</h3>
                    <p class="text-sm text-slate-600">Control de stock automático para que nadie repita regalo y opción de factor sorpresa.</p>
                </div>

                <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
                    <span class="text-3xl block mb-3">📋</span>
                    <h3 class="font-bold text-slate-900 mb-1">Lista de Invitados</h3>
                    <p class="text-sm text-slate-600">Seguimiento en vivo de confirmados, pendientes y control de acceso el día del evento.</p>
                </div>

                <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
                    <span class="text-3xl block mb-3">🪑</span>
                    <h3 class="font-bold text-slate-900 mb-1">Mesas y Cuotas</h3>
                    <p class="text-sm text-slate-600">Asigna ubicaciones por mesa o coordina aportes compartidos de forma transparente.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Vista Previa Interactiva de Invitación -->
    <section id="vista-previa" class="py-16 bg-white border-y border-slate-200/70 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto">
            <div class="text-center max-w-2xl mx-auto mb-10">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-rose-50 border border-rose-100 text-rose-600 text-xs font-bold mb-3">
                    👀 Vista previa para invitados
                </span>
                <h2 class="text-3xl font-extrabold text-slate-900 mb-3">Así luce una invitación en el celular</h2>
                <p class="text-slate-600">Cambia de celebración para ver cómo se presenta la información y la reserva de regalos.</p>
            </div>

            <!-- Selector de temática en la vista previa -->
            <div class="flex flex-wrap justify-center gap-2 mb-8">
                <button type="button" onclick="cambiarPreview('baby')" id="tab-baby"
                        class="preview-tab px-4 py-2 rounded-full text-sm font-bold bg-indigo-600 text-white shadow-sm transition-all">
                    🍼 Baby Shower
                </button>
                <button type="button" onclick="cambiarPreview('boda')" id="tab-boda"
                        class="preview-tab px-4 py-2 rounded-full text-sm font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 transition-all">
                    💍 Matrimonio
                </button>
                <button type="button" onclick="cambiarPreview('cumple')" id="tab-cumple"
                        class="preview-tab px-4 py-2 rounded-full text-sm font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 transition-all">
                    🎂 Cumpleaños
                </button>
            </div>

            <!-- Marco tipo teléfono -->
            <div class="max-w-md mx-auto bg-slate-900 rounded-[2.75rem] p-3 shadow-2xl border-4 border-slate-800">
                <div id="preview-card" class="bg-blue-50 rounded-[2.25rem] p-6 transition-colors duration-300">
                    <div class="w-16 h-1.5 bg-slate-900/15 rounded-full mx-auto mb-5"></div>

                    <div class="text-center mb-5">
                        <span id="preview-badge" class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700 mb-2">
                            ¡Viene en camino!
                        </span>
                        <h3 id="preview-titulo" class="text-2xl font-extrabold text-slate-900">Baby Shower de Mateo</h3>
                        <p id="preview-fecha" class="text-xs font-semibold text-slate-600 mt-1">Sábado 14 de Diciembre · 16:30 hrs</p>
                        <p id="preview-lugar" class="text-xs text-slate-500 mt-0.5">📍 Salón Los Aromos, Vitacura</p>
                    </div>

                    <!-- Simulación RSVP -->
                    <div class="bg-white/90 rounded-2xl p-4 mb-4 border border-white shadow-sm">
                        <p class="text-xs font-bold text-slate-700 mb-2">Confirma tu asistencia</p>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" onclick="simularConfirmacion('confirmado')"
                                    class="py-2 px-3 rounded-xl text-xs font-bold bg-emerald-600 text-white hover:bg-emerald-700 transition-colors">
                                ✓ Asistiré
                            </button>
                            <button type="button" onclick="simularConfirmacion('rechazado')"
                                    class="py-2 px-3 rounded-xl text-xs font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors">
                                No podré ir
                            </button>
                        </div>
                        <p id="preview-rsvp-estado" class="text-[11px] text-slate-500 mt-2 text-center">
                            Toca una opción para probar el estado
                        </p>
                    </div>

                    <!-- Simulación Lista de Regalos -->
                    <div class="bg-white/90 rounded-2xl p-4 border border-white shadow-sm">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-slate-700">Lista de regalos</span>
                            <span class="text-[11px] font-semibold text-indigo-600">Stock en vivo</span>
                        </div>
                        <div class="space-y-2 text-xs">
                            <div class="flex items-center justify-between bg-slate-50 px-3 py-2 rounded-xl">
                                <span id="regalo-1-nombre" class="font-semibold text-slate-800">Pañales RN (3 packs)</span>
                                <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 font-bold text-[10px]">Disponible</span>
                            </div>
                            <div class="flex items-center justify-between bg-slate-50 px-3 py-2 rounded-xl">
                                <span id="regalo-2-nombre" class="font-semibold text-slate-800">Manta de apego</span>
                                <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 font-bold text-[10px]">Reservado</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Celebraciones -->
    <section id="celebraciones" class="py-16 bg-indigo-600 text-white px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-3xl font-extrabold mb-4">¿Listo para armar tu próxima celebración?</h2>
            <p class="text-indigo-100 mb-8 text-lg">Elige la temática y comienza a configurar tu evento en menos de un minuto.</p>
            <button type="button" onclick="abrirMenuEventos()" class="inline-flex items-center gap-3 bg-white text-indigo-700 px-8 py-4 rounded-full font-extrabold shadow-lg hover:bg-indigo-50 transition-all transform hover:-translate-y-0.5">
                <span>✨ Elegir temática y comenzar</span>
            </button>
        </div>
    </section>

    <!-- Footer simple -->
    <footer class="bg-white border-t border-slate-200 py-8 px-4 text-center text-sm text-slate-500">
        <p class="font-semibold text-slate-700">Invitapp</p>
        <p class="mt-1">Invitaciones interactivas y gestión inteligente de eventos.</p>
    </footer>

    <!-- Overlay y Bottom Sheet de Eventos -->
    <div id="overlay-eventos"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[60] hidden transition-opacity duration-300 opacity-0"
         onclick="cerrarMenuEventos()">
    </div>

    <div id="bottom-sheet-eventos"
         class="fixed bottom-0 left-0 right-0 bg-white rounded-t-[2.5rem] shadow-[0_-10px_40px_rgba(0,0,0,0.15)] z-[70] transform translate-y-full transition-transform duration-300 ease-out p-6 sm:p-8 max-h-[85vh] overflow-y-auto w-full max-w-2xl mx-auto">

        <div class="w-16 h-1.5 bg-slate-200 rounded-full mx-auto mb-8"></div>

        <h3 class="text-2xl font-extrabold text-slate-800 text-center mb-8">¿Qué vas a celebrar hoy? 🎉</h3>

        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">

            <a href="{{ route('register', ['tipo' => 'baby_shower']) }}" class="flex flex-col items-center justify-center p-5 bg-blue-50 rounded-3xl border border-blue-100 hover:bg-blue-100 hover:border-blue-300 hover:scale-105 transition-all text-center group">
                <span class="text-4xl mb-3 group-hover:scale-110 transition-transform">🍼</span>
                <span class="font-bold text-blue-700 text-sm">Baby Shower</span>
            </a>

            <a href="{{ route('register', ['tipo' => 'matrimonio']) }}" class="flex flex-col items-center justify-center p-5 bg-rose-50 rounded-3xl border border-rose-100 hover:bg-rose-100 hover:border-rose-300 hover:scale-105 transition-all text-center group">
                <span class="text-4xl mb-3 group-hover:scale-110 transition-transform">💍</span>
                <span class="font-bold text-rose-700 text-sm">Matrimonio</span>
            </a>

            <a href="{{ route('register', ['tipo' => 'cumpleanos']) }}" class="flex flex-col items-center justify-center p-5 bg-amber-50 rounded-3xl border border-amber-100 hover:bg-amber-100 hover:border-amber-300 hover:scale-105 transition-all text-center group">
                <span class="text-4xl mb-3 group-hover:scale-110 transition-transform">🎂</span>
                <span class="font-bold text-amber-700 text-sm">Cumpleaños</span>
            </a>

            <a href="{{ route('register', ['tipo' => 'asado']) }}" class="flex flex-col items-center justify-center p-5 bg-orange-50 rounded-3xl border border-orange-100 hover:bg-orange-100 hover:border-orange-300 hover:scale-105 transition-all text-center group">
                <span class="text-4xl mb-3 group-hover:scale-110 transition-transform">🥩</span>
                <span class="font-bold text-orange-700 text-sm">Asado</span>
            </a>

            <a href="{{ route('register', ['tipo' => 'fiesta']) }}" class="flex flex-col items-center justify-center p-5 bg-purple-50 rounded-3xl border border-purple-100 hover:bg-purple-100 hover:border-purple-300 hover:scale-105 transition-all text-center group">
                <span class="text-4xl mb-3 group-hover:scale-110 transition-transform">🎉</span>
                <span class="font-bold text-purple-700 text-sm">Fiesta</span>
            </a>

        </div>
    </div>

    <script>
        function abrirMenuEventos() {
            const overlay = document.getElementById('overlay-eventos');
            const sheet = document.getElementById('bottom-sheet-eventos');

            overlay.classList.remove('hidden');
            setTimeout(() => {
                overlay.classList.remove('opacity-0');
                sheet.classList.remove('translate-y-full');
            }, 10);
        }

        function cerrarMenuEventos() {
            const overlay = document.getElementById('overlay-eventos');
            const sheet = document.getElementById('bottom-sheet-eventos');

            sheet.classList.add('translate-y-full');
            overlay.classList.add('opacity-0');

            setTimeout(() => {
                overlay.classList.add('hidden');
            }, 300);
        }

        const demosInvitacion = {
            baby: {
                cardClass: 'bg-blue-50 rounded-[2.25rem] p-6 transition-colors duration-300',
                badgeClass: 'inline-block px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700 mb-2',
                badge: '¡Viene en camino!',
                titulo: 'Baby Shower de Patricio',
                fecha: 'Sábado 14 de Diciembre · 16:30 hrs',
                lugar: '📍 Salón Los Aromos, Vitacura',
                regalo1: 'Pañales RN (3 packs)',
                regalo2: 'Manta de apego'
            },
            boda: {
                cardClass: 'bg-rose-50 rounded-[2.25rem] p-6 transition-colors duration-300',
                badgeClass: 'inline-block px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-700 mb-2',
                badge: '¡Nos casamos!',
                titulo: 'Nicole y Nicolás',
                fecha: 'Viernes 20 de Febrero · 18:00 hrs',
                lugar: '📍 Viña Santa Carolina, Santiago',
                regalo1: 'Set copas de cristal',
                regalo2: 'Cafetera espresso'
            },
            cumple: {
                cardClass: 'bg-amber-50 rounded-[2.25rem] p-6 transition-colors duration-300',
                badgeClass: 'inline-block px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700 mb-2',
                badge: '¡Celebremos juntos!',
                titulo: 'Cumpleaños de Hector',
                fecha: 'Domingo 11 de Enero · 17:00 hrs',
                lugar: '📍 Terraza Parque Araucano',
                regalo1: 'Giftcard librería',
                regalo2: 'Juego de mesa'
            }
        };

        function cambiarPreview(tipo) {
            const d = demosInvitacion[tipo];
            if (!d) return;

            document.getElementById('preview-card').className = d.cardClass;
            document.getElementById('preview-badge').className = d.badgeClass;
            document.getElementById('preview-badge').textContent = d.badge;
            document.getElementById('preview-titulo').textContent = d.titulo;
            document.getElementById('preview-fecha').textContent = d.fecha;
            document.getElementById('preview-lugar').textContent = d.lugar;
            document.getElementById('regalo-1-nombre').textContent = d.regalo1;
            document.getElementById('regalo-2-nombre').textContent = d.regalo2;
            document.getElementById('preview-rsvp-estado').textContent = 'Toca una opción para probar el estado';
            document.getElementById('preview-rsvp-estado').className = 'text-[11px] text-slate-500 mt-2 text-center';

            document.querySelectorAll('.preview-tab').forEach(btn => {
                btn.className = 'preview-tab px-4 py-2 rounded-full text-sm font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 transition-all';
            });
            const activa = document.getElementById('tab-' + tipo);
            if (activa) {
                activa.className = 'preview-tab px-4 py-2 rounded-full text-sm font-bold bg-indigo-600 text-white shadow-sm transition-all';
            }
        }

        function simularConfirmacion(estado) {
            const label = document.getElementById('preview-rsvp-estado');
            if (estado === 'confirmado') {
                label.textContent = '✨ ¡Asistencia marcada como Confirmada en esta demo!';
                label.className = 'text-[11px] font-semibold text-emerald-700 mt-2 text-center';
            } else {
                label.textContent = 'Estado marcado como No asistirá en esta demo.';
                label.className = 'text-[11px] font-semibold text-slate-600 mt-2 text-center';
            }
        }
    </script>
</body>
</html>