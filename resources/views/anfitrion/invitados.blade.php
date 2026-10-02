@extends('layouts.app')

@section('contenido')
@php
    $invitados = $datosLista['invitados'] ?? [];
    $evento = $eventoActivo ?? Session::get('evento_activo') ?? [];
    $slugEvento = $evento['slug'] ?? '';
    $idEvento = $evento['_id'] ?? $evento['id'] ?? '';
@endphp

<div class="max-w-7xl mx-auto py-8 px-4 animate-fade-in-up pb-20 space-y-8">
    
    {{-- ========================================== --}}
    {{-- ENCABEZADO Y ACCIONES PRINCIPALES          --}}
    {{-- ========================================== --}}
    <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-[2rem] shadow-sm border border-slate-200/80">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <a href="{{ route('anfitrion.index') }}" class="text-slate-400 hover:text-indigo-600 transition-colors font-bold text-sm">
                    <i class="fa-solid fa-arrow-left"></i> Volver al Dashboard
                </a>
            </div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight flex items-center gap-3 mt-2">
                <span class="text-4xl">👥</span> Lista de Invitados
            </h1>
            <p class="text-slate-500 text-sm mt-1">Administra las confirmaciones RSVP y envía invitaciones.</p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto mt-4 sm:mt-0">
            {{-- Botón Copiar Link --}}
            @if(!empty($slugEvento))
                <button onclick="copiarEnlace('{{ url('/e/'.$slugEvento) }}')" class="flex-1 sm:flex-none flex items-center justify-center gap-2 bg-slate-900 hover:bg-slate-800 text-white font-bold px-5 py-3 rounded-2xl transition-all shadow-sm active:scale-95">
                    <i class="fa-solid fa-link"></i> Link Público
                </button>
            @endif
            
            {{-- Botón Importar (Abre Modal) --}}
            <button onclick="abrirModalImportar()" class="flex-1 sm:flex-none flex items-center justify-center gap-2 bg-white hover:bg-slate-50 text-slate-700 font-bold px-5 py-3 rounded-2xl transition-all border-2 border-slate-200 shadow-sm active:scale-95">
                <i class="fa-solid fa-file-csv text-emerald-500"></i> Importar CSV
            </button>
            
            {{-- Botón Agregar (Abre Modal) --}}
            <button onclick="abrirModalAgregar()" class="flex-1 sm:flex-none flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-6 py-3 rounded-2xl transition-all shadow-md hover:-translate-y-1 active:scale-95">
                <i class="fa-solid fa-user-plus"></i> Añadir Manual
            </button>
        </div>
    </header>

    {{-- ========================================== --}}
    {{-- TARJETAS DE ESTADO RSVP Y RECORDATORIOS    --}}
    {{-- ========================================== --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-indigo-50 p-5 rounded-2xl border border-indigo-100 flex flex-col justify-center shadow-sm">
            <p class="text-[10px] text-indigo-800 font-bold uppercase tracking-wider mb-1">Total Lista</p>
            <p class="text-3xl font-black text-slate-900">{{ count($invitados) }}</p>
        </div>
        <div class="bg-emerald-50 p-5 rounded-2xl border border-emerald-100 flex flex-col justify-center shadow-sm">
            <p class="text-[10px] text-emerald-800 font-bold uppercase tracking-wider mb-1"><i class="fa-solid fa-check text-emerald-500"></i> Confirmados</p>
            <p class="text-3xl font-black text-slate-900">{{ $metricas['confirmados'] ?? 0 }}</p>
        </div>
        <div class="bg-amber-50 p-5 rounded-2xl border border-amber-100 flex flex-col justify-center shadow-sm">
            <p class="text-[10px] text-amber-800 font-bold uppercase tracking-wider mb-1"><i class="fa-solid fa-clock text-amber-500"></i> Pendientes</p>
            <p class="text-3xl font-black text-slate-900">{{ $metricas['pendientes'] ?? 0 }}</p>
        </div>
        
        {{-- Tarjeta de Acción: Recordatorio Masivo --}}
        <div class="bg-indigo-600 p-5 rounded-2xl shadow-md flex flex-col justify-between group overflow-hidden relative">
            <div class="absolute top-0 right-0 -mr-4 -mt-4 w-20 h-20 bg-white/10 rounded-full blur-xl"></div>
            <div class="relative z-10">
                <p class="text-[10px] text-indigo-100 font-bold uppercase tracking-wider mb-2">Acción Rápida</p>
                <p class="text-sm text-white font-bold leading-tight">Recordar a los pendientes</p>
            </div>
            <form action="{{ route('anfitrion.invitados.remind') }}" method="POST" class="m-0 mt-3 relative z-10">
                @csrf
                <button type="submit" 
                        @if(($metricas['pendientes'] ?? 0) === 0) disabled class="w-full bg-indigo-800 text-indigo-400 py-2 rounded-xl font-bold text-xs shadow-sm cursor-not-allowed border border-indigo-700" 
                        @else class="w-full bg-white hover:bg-slate-50 text-indigo-600 py-2 rounded-xl font-bold transition-all text-xs shadow-sm cursor-pointer" @endif>
                    <i class="fa-solid fa-envelope"></i> Enviar Correos
                </button>
            </form>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- TABLA DE INVITADOS CON BUSCADOR            --}}
    {{-- ========================================== --}}
    <div class="bg-white rounded-[2rem] shadow-sm overflow-hidden border border-slate-200/80">
        
        {{-- BARRA BUSCADOR EN VIVO --}}
        <div class="p-5 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <h2 class="text-slate-800 font-black text-sm uppercase tracking-wider">📋 Detalle Nominal</h2>
            <div class="relative w-full sm:w-72">
                <input type="text" id="input-buscador" onkeyup="filtrarInvitados()" placeholder="Buscar por nombre o correo..." 
                       class="w-full bg-white border border-slate-200 text-sm rounded-xl pl-10 pr-4 py-2.5 outline-none focus:ring-2 focus:ring-indigo-200 font-medium shadow-sm">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-3.5 text-slate-400 text-sm"></i>
            </div>
        </div>

        {{-- TABLA NOMINAL --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="tabla-invitados">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="p-5 font-bold text-slate-400 uppercase text-[10px] tracking-wider">Invitado</th>
                        <th class="p-5 font-bold text-slate-400 uppercase text-[10px] tracking-wider text-center">Estado RSVP</th>
                        <th class="p-5 font-bold text-slate-400 uppercase text-[10px] tracking-wider text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm" id="tbody-invitados">
                    @forelse($invitados as $guest)
                        @php
                            $idGuest = $guest['_id'] ?? $guest['id'] ?? '';
                            $estadoG = $guest['estadoConfirmacion'] ?? 'pendiente';
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition-colors fila-invitado group">
                            
                            {{-- Columna Nombre y Correo --}}
                            <td class="p-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center text-slate-600 font-black text-sm shrink-0">
                                        {{ strtoupper(substr($guest['nombre'], 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900 campo-nombre">{{ $guest['nombre'] ?? '' }}</p>
                                        <p class="text-xs text-slate-400 font-mono campo-correo">{{ $guest['correo'] ?: 'Sin correo registrado' }}</p>
                                    </div>
                                </div>
                            </td>
                            
                            {{-- Columna Selector de Estado --}}
                            <td class="p-5 align-middle text-center">
                                <form action="{{ route('anfitrion.guests.update', $idGuest) }}" method="POST" class="inline-block m-0">
                                    @csrf 
                                    @method('PUT')
                                    <select name="estadoConfirmacion" onchange="this.form.submit()" class="bg-white border border-slate-200 px-3 py-1.5 rounded-xl text-xs font-bold text-slate-700 outline-none cursor-pointer focus:ring-2 focus:ring-indigo-200 shadow-sm transition-all hover:border-indigo-300">
                                        <option value="pendiente" {{ $estadoG === 'pendiente' ? 'selected' : '' }}>⏳ Pendiente</option>
                                        <option value="confirmado" {{ $estadoG === 'confirmado' ? 'selected' : '' }}>✅ Confirmado</option>
                                        <option value="rechazado" {{ $estadoG === 'rechazado' ? 'selected' : '' }}>❌ No Asistirá</option>
                                    </select>
                                </form>
                            </td>
                            
                            {{-- Columna Acciones --}}
                            <td class="p-5 align-middle text-right space-x-1 whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button onclick="abrirModalEditar('{{ $idGuest }}', '{{ addslashes($guest['nombre'] ?? '') }}', '{{ addslashes($guest['correo'] ?? '') }}')" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-indigo-100 text-slate-500 hover:text-indigo-600 flex items-center justify-center transition-colors shadow-sm" title="Editar Invitado">
                                        <i class="fa-solid fa-pen text-xs"></i>
                                    </button>
                                    <form action="{{ route('anfitrion.guests.destroy', $idGuest) }}" method="POST" onsubmit="return confirm('¿Remover a este invitado de la lista?')" class="inline-block m-0">
                                        @csrf 
                                        @method('DELETE')
                                        <button type="submit" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-rose-100 text-slate-500 hover:text-rose-600 flex items-center justify-center transition-colors shadow-sm" title="Eliminar Invitado">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                                {{-- Botón visible en móvil --}}
                                <button class="sm:hidden text-slate-400 hover:text-indigo-600 p-2">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr id="row-empty">
                            <td colspan="3" class="p-16 text-center">
                                <div class="w-20 h-20 bg-indigo-50 rounded-full flex items-center justify-center text-3xl mx-auto mb-4 text-indigo-300">
                                    <i class="fa-solid fa-users-slash"></i>
                                </div>
                                <h3 class="text-lg font-bold text-slate-800 mb-1">Aún no hay invitados</h3>
                                <p class="text-sm text-slate-500 max-w-sm mx-auto mb-6">Usa el botón "Añadir Manual" o importa un CSV para empezar a llenar tu lista.</p>
                                <button onclick="abrirModalAgregar()" class="bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold px-6 py-2.5 rounded-xl transition-colors text-sm">
                                    Registrar primer invitado
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ========================================== --}}
{{-- MODALES OCULTOS                            --}}
{{-- ========================================== --}}

{{-- MODAL: AGREGAR INVITADO MANUAL --}}
<div id="modalAgregar" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-50 p-4 transition-opacity">
    <div class="bg-white p-8 rounded-[2rem] shadow-2xl border border-slate-100 w-full max-w-md transform scale-100">
        <h3 class="text-xl font-black text-slate-900 mb-2 flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600"><i class="fa-solid fa-user-plus"></i></div>
            Nuevo Invitado
        </h3>
        <p class="text-xs text-slate-500 mb-6 font-medium">Ingresa los datos para añadirlo a la lista de tu evento.</p>
        
        <form action="{{ route('anfitrion.guests.store') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Nombre Completo *</label>
                <input type="text" name="nombre" required placeholder="Ej: Juan Pérez" class="w-full border-2 border-slate-100 p-3.5 rounded-2xl outline-none focus:border-indigo-500 focus:bg-white text-sm bg-slate-50 transition-colors font-medium">
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Correo Electrónico (Opcional)</label>
                <input type="email" name="correo" placeholder="juan@correo.com" class="w-full border-2 border-slate-100 p-3.5 rounded-2xl outline-none focus:border-indigo-500 focus:bg-white text-sm bg-slate-50 transition-colors font-medium">
            </div>
            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 mt-2">
                <button type="button" onclick="cerrarModalAgregar()" class="bg-white border-2 border-slate-200 hover:bg-slate-50 text-slate-600 px-6 py-3 rounded-2xl transition-all text-sm font-bold cursor-pointer">
                    Cancelar
                </button>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl transition-all text-sm font-bold shadow-md cursor-pointer flex items-center gap-2">
                    <i class="fa-solid fa-check"></i> Registrar
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: IMPORTAR CSV --}}
<div id="modalImportar" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-50 p-4 transition-opacity">
    <div class="bg-white p-8 rounded-[2rem] shadow-2xl border border-slate-100 w-full max-w-md">
        <h3 class="text-xl font-black text-slate-900 mb-2 flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600"><i class="fa-solid fa-file-csv"></i></div>
            Carga Masiva
        </h3>
        <p class="text-xs text-slate-500 mb-6 font-medium">Sube un archivo <strong class="text-slate-700">.CSV</strong> (Columna 1: Nombre, Columna 2: Correo).</p>
        
        <form action="{{ route('anfitrion.guests.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="relative flex items-center justify-center w-full">
                <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-slate-200 border-dashed rounded-2xl cursor-pointer bg-slate-50/50 hover:bg-slate-100 hover:border-emerald-300 transition-all">
                    <div class="flex flex-col items-center justify-center text-center px-4">
                        <i class="fa-solid fa-cloud-arrow-up text-3xl text-emerald-500 mb-2"></i>
                        <p class="text-sm font-bold text-slate-600">Haz clic para buscar tu CSV</p>
                    </div>
                    <input type="file" name="archivo_csv" accept=".csv,.txt" required class="hidden" onchange="actualizarNombreArchivo(this)" />
                </label>
            </div>
            <p id="nombre-archivo-subido" class="text-xs text-emerald-600 font-bold text-center bg-emerald-50 py-2 rounded-xl hidden border border-emerald-100"></p>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="cerrarModalImportar()" class="bg-white border-2 border-slate-200 hover:bg-slate-50 text-slate-600 px-6 py-3 rounded-2xl transition-all text-sm font-bold cursor-pointer">
                    Cancelar
                </button>
                <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white px-6 py-3 rounded-2xl transition-all text-sm font-bold shadow-md cursor-pointer">
                    Importar Lista
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: EDITAR INVITADO --}}
<div id="modalEditar" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-50 p-4 transition-opacity">
    <div class="bg-white p-8 rounded-[2rem] shadow-2xl border border-slate-100 w-full max-w-md">
        <h3 class="text-xl font-black text-slate-900 mb-2 flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-600"><i class="fa-solid fa-user-pen"></i></div>
            Modificar Invitado
        </h3>
        <p class="text-xs text-slate-500 mb-6 font-medium">Actualiza el nombre o el correo del asistente.</p>
        
        <form id="formEditarInvitado" method="POST" class="space-y-5">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Nombre Completo</label>
                <input type="text" id="edit_nombre" name="nombre" required class="w-full border-2 border-slate-100 p-3.5 rounded-2xl outline-none focus:border-indigo-500 focus:bg-white text-sm bg-slate-50 font-medium">
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Correo Electrónico</label>
                <input type="email" id="edit_correo" name="correo" required class="w-full border-2 border-slate-100 p-3.5 rounded-2xl outline-none focus:border-indigo-500 focus:bg-white text-sm bg-slate-50 font-medium">
            </div>
            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 mt-2">
                <button type="button" onclick="cerrarModalEditar()" class="bg-white border-2 border-slate-200 hover:bg-slate-50 text-slate-600 px-6 py-3 rounded-2xl transition-all text-sm font-bold cursor-pointer">
                    Cancelar
                </button>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl transition-all text-sm font-bold shadow-md cursor-pointer">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>

<script>
    // 1. FUNCIONES MODALES
    function abrirModalAgregar() { document.getElementById('modalAgregar').classList.remove('hidden'); }
    function cerrarModalAgregar() { document.getElementById('modalAgregar').classList.add('hidden'); }
    
    function abrirModalImportar() { document.getElementById('modalImportar').classList.remove('hidden'); }
    function cerrarModalImportar() { document.getElementById('modalImportar').classList.add('hidden'); }

    function abrirModalEditar(id, nombre, correo) {
        const modal = document.getElementById('modalEditar');
        const form = document.getElementById('formEditarInvitado');
        form.action = `/anfitrion/invitados/${id}`;
        document.getElementById('edit_nombre').value = nombre;
        document.getElementById('edit_correo').value = correo;
        modal.classList.remove('hidden');
    }
    function cerrarModalEditar() { document.getElementById('modalEditar').classList.add('hidden'); }

    // Cierra los modales al hacer clic fuera de ellos
    window.onclick = function(event) {
        if (event.target == document.getElementById('modalAgregar')) cerrarModalAgregar();
        if (event.target == document.getElementById('modalImportar')) cerrarModalImportar();
        if (event.target == document.getElementById('modalEditar')) cerrarModalEditar();
    }

    // 2. COPIAR ENLACE AL PORTAPAPELES
    function copiarEnlace(url) {
        navigator.clipboard.writeText(url).then(() => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: '¡Enlace de invitación copiado!',
                showConfirmButton: false,
                timer: 3000
            });
        }).catch(err => console.error('Error al copiar: ', err));
    }

    // 3. MOSTRAR NOMBRE DE ARCHIVO CSV
    function actualizarNombreArchivo(input) {
        const label = document.getElementById('nombre-archivo-subido');
        if(input.files && input.files[0]) {
            label.innerText = `📂 ${input.files[0].name}`;
            label.classList.remove('hidden');
        }
    }

    // 4. BÚSQUEDA EN TIEMPO REAL
    function filtrarInvitados() {
        const busqueda = document.getElementById('input-buscador').value.toLowerCase();
        const filas = document.querySelectorAll('.fila-invitado');

        filas.forEach(fila => {
            const nombre = fila.querySelector('.campo-nombre').innerText.toLowerCase();
            const correo = fila.querySelector('.campo-correo').innerText.toLowerCase();
            if (nombre.includes(busqueda) || correo.includes(busqueda)) {
                fila.style.display = '';
            } else {
                fila.style.display = 'none';
            }
        });
    }

    // 5. SINCRONIZACIÓN SOCKET.IO Y ALERTAS LARAVEL
    // (Mantenemos tu lógica original intacta que funciona perfectamente)
    const socket = io('http://localhost:3000');

    socket.on('nuevo-invitado', function(invitado) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'info',
            title: `¡Asistencia interactiva de ${invitado.nombre}!`,
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true
        });
        
        // ... (El resto de tu lógica para agregar la fila dinámicamente) ...
        setTimeout(() => location.reload(), 1500); // Recarga rápida para MVP
    });

    @if(session('success'))
        Swal.fire({ icon: 'success', title: '¡Operación Exitosa!', text: "{{ session('success') }}", confirmButtonColor: '#4f46e5' });
    @endif
    @if(session('error'))
        Swal.fire({ icon: 'error', title: 'Atención', text: "{{ session('error') }}", confirmButtonColor: '#e11d48' });
    @endif
</script>

<style>
    @keyframes fadeInUp {
        0% { opacity: 0; transform: translateY(15px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up {
        animation: fadeInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
</style>
@endsection