@extends('layouts.app')

@section('contenido')
@php
    $eventoActivo = Session::get('evento_activo') ?? [];$idEvento = $eventoActivo['_id'] ?? $eventoActivo['id'] ?? '';
@endphp

<div class="max-w-4xl mx-auto py-8 px-4 animate-fade-in-up pb-20">
    
    {{-- CABECERA --}}
    <header class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8 bg-white p-6 rounded-[2rem] shadow-sm border border-slate-200/80">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <a href="{{ route('anfitrion.index') }}" class="text-slate-400 hover:text-cyan-600 transition-colors font-bold text-sm">
                    <i class="fa-solid fa-arrow-left"></i> Volver al Dashboard
                </a>
            </div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight flex items-center gap-3 mt-2">
                <span class="text-4xl text-cyan-500">🎟️</span> Control de Acceso
            </h1>
            <p class="text-slate-500 text-sm mt-1">Escanea los códigos QR de tus invitados para registrar su ingreso.</p>
        </div>
        
        <div class="flex items-center gap-3 bg-cyan-50 text-cyan-800 px-5 py-3 rounded-2xl font-bold border border-cyan-100">
            <i class="fa-solid fa-users"></i> Ingresados: <span id="contador-ingresos" class="text-xl font-black ml-1">0</span>
        </div>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        
        {{-- COLUMNA 1: EL ESCÁNER (CÁMARA) --}}
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-200/80 flex flex-col items-center">
            <h2 class="text-sm font-black text-slate-400 uppercase tracking-wider mb-6 flex items-center gap-2 w-full">
                <i class="fa-solid fa-camera text-cyan-500"></i> Escáner en vivo
            </h2>

            <!-- Contenedor donde se montará la cámara -->
            <div id="reader" class="w-full max-w-sm rounded-2xl overflow-hidden border-4 border-slate-100 shadow-inner bg-slate-50 relative aspect-square">
                <!-- Placeholder antes de iniciar -->
                <div id="reader-placeholder" class="absolute inset-0 flex flex-col items-center justify-center text-slate-400 z-10">
                    <i class="fa-solid fa-qrcode text-6xl mb-4 opacity-50"></i>
                    <p class="font-bold text-sm">Cámara inactiva</p>
                </div>
            </div>

            <!-- Botones de Control de Cámara -->
            <div class="mt-6 flex gap-3 w-full max-w-sm">
                <button id="btn-start" class="flex-1 bg-cyan-600 hover:bg-cyan-700 text-white font-bold py-3.5 rounded-2xl transition-all shadow-md active:scale-95 text-sm flex justify-center items-center gap-2">
                    <i class="fa-solid fa-play"></i> Iniciar Escáner
                </button>
                <button id="btn-stop" class="hidden flex-1 bg-rose-100 hover:bg-rose-200 text-rose-600 font-bold py-3.5 rounded-2xl transition-all active:scale-95 text-sm justify-center items-center gap-2">
                    <i class="fa-solid fa-stop"></i> Detener
                </button>
            </div>
        </div>

        {{-- COLUMNA 2: REGISTRO DE LECTURAS (LOG) --}}
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-200/80 flex flex-col h-full">
            <h2 class="text-sm font-black text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2 w-full">
                <i class="fa-solid fa-clock-rotate-left text-cyan-500"></i> Últimos Ingresos
            </h2>
            
            <div id="log-resultados" class="flex-1 bg-slate-50 rounded-2xl border border-slate-100 p-4 overflow-y-auto max-h-[300px] space-y-3">
                <div class="text-center text-slate-400 text-sm font-medium italic mt-10">
                    Aún no se han registrado ingresos...
                </div>
            </div>
        </div>
    </div>
</div>

<!-- LIBRERÍAS NECESARIAS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://unpkg.com/html5-qrcode"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Inicializamos el objeto del lector
        const html5QrCode = new Html5Qrcode("reader");
        const btnStart = document.getElementById('btn-start');
        const btnStop = document.getElementById('btn-stop');
        const placeholder = document.getElementById('reader-placeholder');
        const logResultados = document.getElementById('log-resultados');
        const eventoId = "{{ $idEvento }}"; 
        
        let isScanning = false;
        let contador = 0;

        // Configuración de la cámara (Usamos la cámara trasera si está en un celular)
        const config = { fps: 10, qrbox: { width: 250, height: 250 } };

        btnStart.addEventListener('click', () => {
            placeholder.style.display = 'none';
            btnStart.classList.add('hidden');
            btnStop.classList.remove('hidden');
            btnStop.classList.add('flex');

            html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess)
                .then(() => { isScanning = true; })
                .catch((err) => {
                    Swal.fire('Error de Cámara', 'Verifica que diste permisos a tu navegador.', 'error');
                    resetBotones();
                });
        });

        btnStop.addEventListener('click', () => {
            if(isScanning) {
                html5QrCode.stop().then(() => {
                    isScanning = false;
                    resetBotones();
                    placeholder.style.display = 'flex';
                }).catch(err => console.log(err));
            }
        });

        function resetBotones() {
            btnStop.classList.add('hidden');
            btnStop.classList.remove('flex');
            btnStart.classList.remove('hidden');
        }

        // 🟢 FUNCIÓN QUE SE EJECUTA AL LEER UN CÓDIGO QR
        function onScanSuccess(decodedText, decodedResult) {
            // 1. Pausamos la cámara de inmediato para no escanear 20 veces por segundo
            html5QrCode.pause();

            // 2. Aquí llamaremos a tu API (Lo simularemos para que pruebes la UI primero)
            verificarTicket(decodedText);
        }

        function verificarTicket(codigoQR) {
            Swal.fire({
                title: 'Verificando...',
                text: 'Consultando la base de datos',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading() }
            });

            // Hacemos la petición real al Backend en Node.js
            fetch(`http://localhost:3000/api/eventos/publico/${eventoId}/checkin`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ qr_data: codigoQR })
            })
            .then(response => response.json().then(data => ({ status: response.status, body: data })))
            .then(({ status, body }) => {
                
                if (status === 200) {
                    // ✅ ÉXITO: PRIMERA VEZ
                    
                    // Actualizamos métricas en pantalla
                    contador++;
                    document.getElementById('contador-ingresos').innerText = contador;
                    
                    // Registramos en el Log visual
                    if(contador === 1) logResultados.innerHTML = ''; // Limpiamos el texto vacío
                    
                    const logItem = `
                        <div class="bg-white border-l-4 border-cyan-500 p-3 rounded-xl shadow-sm flex items-center justify-between animate-fade-in-up">
                            <div>
                                <p class="text-sm font-bold text-slate-800">✅ Ingreso Autorizado</p>
                                <p class="text-[10px] text-slate-400 font-mono">${body.nombre} (${body.correo})</p>
                            </div>
                            <span class="text-[10px] text-slate-400">${new Date().toLocaleTimeString()}</span>
                        </div>
                    `;
                    logResultados.insertAdjacentHTML('afterbegin', logItem);

                    Swal.fire({
                        icon: 'success',
                        title: '¡Adelante!',
                        text: `Ingreso registrado para ${body.nombre}.`,
                        confirmButtonColor: '#0891b2',
                        timer: 2500,
                        showConfirmButton: false,
                        customClass: { popup: 'rounded-3xl' } 
                    }).then(() => {
                        html5QrCode.resume(); // Retomamos la cámara
                    });

                } else if (status === 409) {
                    // ❌ ERROR: TICKET YA UTILIZADO (Doble Check-in)
                    const horaFormateada = new Date(body.horaOriginal).toLocaleTimeString();
                    Swal.fire({
                        icon: 'error',
                        title: '¡Acceso Denegado!',
                        text: `Este ticket ya fue utilizado a las ${horaFormateada}.`,
                        confirmButtonColor: '#e11d48',
                        confirmButtonText: 'Continuar Escaneando',
                        customClass: { popup: 'rounded-3xl' } 
                    }).then(() => {
                        html5QrCode.resume(); 
                    });

                } else {
                    // ❌ OTROS ERRORES (QR inválido, evento incorrecto)
                    Swal.fire({
                        icon: 'warning',
                        title: 'Código Inválido',
                        text: body.mensaje || 'Hubo un problema al verificar este código.',
                        confirmButtonColor: '#e11d48',
                        customClass: { popup: 'rounded-3xl' }
                    }).then(() => {
                        html5QrCode.resume(); 
                    });
                }
            })
            .catch(error => {
                console.error('Error de red:', error);
                Swal.fire({
                     icon: 'error',
                     title: 'Error de conexión', 
                     text: 'No se pudo contactar con el servidor.', 
                     customClass: { popup: 'rounded-3xl' }
                }).then(() => html5QrCode.resume());
            });
        }
    });
</script>
@endsection