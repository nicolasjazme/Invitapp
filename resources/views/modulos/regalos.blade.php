@extends('layouts.app')

@section('contenido')
@php
    $eventoActivo = Session::get('evento_activo') ?? [];
    $eventoId = $eventoActivo['_id'] ?? $eventoActivo['id'] ?? null;
@endphp

<div class="space-y-8">
    
    {{-- Cabecera --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
        <div>
            <div class="flex items-center gap-3">
                <span class="text-2xl">🎁</span>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Lista de Deseos (Regalos)</h1>
            </div>
            <p class="text-slate-500 text-sm mt-1">
                Agrega los productos que te gustaría recibir, edita sus detalles o libera reservas si algún invitado cambia de opinión.
            </p>
        </div>
        
        <button onclick="abrirModalCrear()" class="inline-flex items-center gap-2 bg-rose-500 hover:bg-rose-600 text-white font-bold px-5 py-3 rounded-2xl transition-all shadow-sm hover:shadow-md text-xs uppercase tracking-wider cursor-pointer">
            <i class="fa-solid fa-plus"></i> Añadir Regalo
        </button>
    </div>

    {{-- Contenedor del Listado --}}
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
        <h2 class="text-lg font-bold text-slate-800 mb-6 flex items-center justify-between">
            <span class="flex items-center gap-2">🛒 Tus Regalos Añadidos</span>
            <span id="badge-total-regalos" class="text-xs bg-rose-100 text-rose-800 px-2.5 py-1 rounded-full font-bold font-mono">0 ítems</span>
        </h2>

        {{-- JS Inyectará las tarjetas de regalos aquí --}}
        <div id="contenedor-regalos" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="col-span-full text-center py-16">
                <i class="fa-solid fa-spinner fa-spin text-rose-500 text-2xl mb-2"></i>
                <p class="text-xs text-slate-400 font-medium">Cargando lista de regalos...</p>
            </div>
        </div>
    </div>
</div>

{{-- Modal Crear / Editar Regalo --}}
<div id="modal-regalo" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-8 shadow-2xl border border-slate-100 relative animate-in fade-in zoom-in-95 duration-200 max-h-[90vh] overflow-y-auto">
        
        <button onclick="toggleModal('modal-regalo', false)" class="absolute top-6 right-6 text-slate-400 hover:text-slate-600 cursor-pointer">
            <i class="fa-solid fa-xmark text-xl"></i>
        </button>

        <h3 id="modal-titulo" class="text-xl font-black text-slate-900 mb-2">🎁 Añadir Producto a tu Lista</h3>
        <p class="text-xs text-slate-500 mb-6">Completa la información para que tus invitados sepan exactamente qué necesitas.</p>

        <form id="form-regalo" onsubmit="guardarRegalo(event)" class="space-y-4">
            <input type="hidden" id="input-regalo-id" value="">

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="sm:col-span-2">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nombre del Regalo *</label>
                    <input type="text" id="input-nombre" required placeholder="Ej. Mamadera, Pañales, etc."
                           class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-rose-200 transition-all text-sm font-medium">
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Cantidad *</label>
                    <input type="number" id="input-cantidad" min="1" max="99" value="1" required
                           class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-rose-200 transition-all text-sm font-bold text-center">
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Descripción o Especificaciones</label>
                <textarea id="input-descripcion" rows="2" placeholder="Ej. Grande y Azul, marca específica, talla..."
                          class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-rose-200 transition-all text-sm font-medium"></textarea>
            </div>

            {{-- Campo de Imagen con Previsualización en Vivo y Subida Local --}}
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Imagen del Producto (Link o Subir foto)</label>
                <div class="flex gap-2">
                    <input type="text" id="input-imagen" oninput="previsualizarImagen(this.value)" placeholder="https://ejemplo.com/mamadera.jpg"
                           class="flex-1 px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-rose-200 transition-all text-sm font-medium">
                    
                    <label class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-2xl text-xs font-bold cursor-pointer flex items-center gap-1.5 shrink-0 transition-all" title="Subir imagen desde tu computador">
                        <i class="fa-solid fa-upload"></i> Subir
                        <input type="file" id="input-archivo-imagen" accept="image/*" class="hidden" onchange="convertirArchivoAImagen(event)">
                    </label>
                </div>
                <p id="aviso-imagen" class="text-[11px] text-slate-400 mt-1">💡 Tip: Pega la dirección de la imagen o usa el botón <b>Subir</b>.</p>

                <div id="preview-contenedor" class="hidden mt-3 w-full h-40 rounded-2xl bg-slate-50 border border-slate-200 overflow-hidden flex items-center justify-center relative">
                    <img id="preview-img" src="" alt="Vista previa" referrerpolicy="no-referrer" class="w-full h-full object-contain p-2">
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Enlace Externo de la Tienda (Opcional)</label>
                <input type="url" id="input-link" placeholder="https://tienda.com/producto-especifico"
                       class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-rose-200 transition-all text-sm font-medium">
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <button type="button" onclick="toggleModal('modal-regalo', false)" class="px-5 py-3 rounded-2xl border border-slate-200 text-slate-500 font-bold text-xs uppercase hover:bg-slate-50 transition-all cursor-pointer">
                    Cancelar
                </button>
                <button type="submit" id="btn-guardar-regalo" class="px-6 py-3 rounded-2xl bg-rose-500 hover:bg-rose-600 text-white font-extrabold text-xs uppercase tracking-wider shadow-md transition-all cursor-pointer">
                    Guardar Regalo
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const EVENTO_ID = '{{ $eventoId }}';
    const TOKEN = '{{ Session::get("token_jwt") }}';
    const API_URL = `http://localhost:3000/api/eventos/${EVENTO_ID}/regalos`;
    let REGALOS_CACHE = [];

    document.addEventListener('DOMContentLoaded', cargarRegalos);

    function toggleModal(id, show) {
        const modal = document.getElementById(id);
        if (show) modal.classList.remove('hidden');
        else {
            modal.classList.add('hidden');
            document.getElementById('form-regalo').reset();
            document.getElementById('input-regalo-id').value = '';
            document.getElementById('preview-contenedor').classList.add('hidden');
            document.getElementById('aviso-imagen').innerHTML = '💡 Tip: Pega la dirección de la imagen o usa el botón <b>Subir</b>.';
            document.getElementById('aviso-imagen').className = 'text-[11px] text-slate-400 mt-1';
        }
    }

    function abrirModalCrear() {
        document.getElementById('modal-titulo').innerText = '🎁 Añadir Producto a tu Lista';
        document.getElementById('btn-guardar-regalo').innerText = 'Guardar Regalo';
        document.getElementById('input-regalo-id').value = '';
        document.getElementById('input-cantidad').value = 1;
        toggleModal('modal-regalo', true);
    }

    function abrirModalEditar(id) {
        const regalo = REGALOS_CACHE.find(r => (r._id || r.id) === id);
        if (!regalo) return;

        document.getElementById('modal-titulo').innerText = '✏️ Editar Regalo';
        document.getElementById('btn-guardar-regalo').innerText = 'Actualizar Cambios';
        document.getElementById('input-regalo-id').value = id;
        document.getElementById('input-nombre').value = regalo.nombre || '';
        document.getElementById('input-cantidad').value = regalo.cantidad_solicitada || 1;
        document.getElementById('input-descripcion').value = regalo.descripcion || '';
        document.getElementById('input-imagen').value = regalo.url_imagen || '';
        document.getElementById('input-link').value = regalo.link_referencia || '';

        toggleModal('modal-regalo', true);
        if (regalo.url_imagen) previsualizarImagen(regalo.url_imagen);
    }

    function previsualizarImagen(url) {
        const contenedor = document.getElementById('preview-contenedor');
        const img = document.getElementById('preview-img');
        const aviso = document.getElementById('aviso-imagen');

        if (!url || url.trim() === '') {
            contenedor.classList.add('hidden');
            return;
        }

        contenedor.classList.remove('hidden');
        img.src = url.trim();
        img.onload = () => {
            aviso.innerHTML = '✅ ¡Imagen válida! Así se verá en tu tarjeta.';
            aviso.className = 'text-[11px] text-emerald-600 font-bold mt-1';
        };
        img.onerror = () => {
            contenedor.classList.add('hidden');
            aviso.innerHTML = '⚠️ No es un enlace directo de imagen. Usa "Copiar dirección de imagen" o el botón Subir.';
            aviso.className = 'text-[11px] text-rose-500 font-bold mt-1';
        };
    }

    function convertirArchivoAImagen(event) {
        const file = event.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            const img = new Image();
            img.onload = function() {
                const canvas = document.createElement('canvas');
                const maxSize = 500;
                let width = img.width;
                let height = img.height;

                if (width > height && width > maxSize) {
                    height *= maxSize / width;
                    width = maxSize;
                } else if (height > maxSize) {
                    width *= maxSize / height;
                    height = maxSize;
                }

                canvas.width = width;
                canvas.height = height;
                canvas.getContext('2d').drawImage(img, 0, 0, width, height);

                const dataUrl = canvas.toDataURL('image/jpeg', 0.8);
                document.getElementById('input-imagen').value = dataUrl;
                previsualizarImagen(dataUrl);
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }

    // --- LEER REGALOS (GET) ---
    function cargarRegalos() {
        if (!EVENTO_ID) return;

        fetch(API_URL, {
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${TOKEN}`
            }
        })
            .then(async res => {
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                return res.json();
            })
            .then(data => renderizarRegalos(data))
            .catch(err => {
                console.error("Error al cargar regalos:", err);
                document.getElementById('contenedor-regalos').innerHTML = `
                    <div class="col-span-full text-center py-8 text-rose-500 font-medium text-xs">
                        Error al conectar con el servidor para recuperar la lista de deseos.
                    </div>
                `;
            });
    }

    // --- DIBUJAR TARJETAS DE REGALOS ---
    function renderizarRegalos(response) {
        const contenedor = document.getElementById('contenedor-regalos');
        const badge = document.getElementById('badge-total-regalos');
        
        const listaRegalos = Array.isArray(response) ? response : (response.regalos || []);
        REGALOS_CACHE = listaRegalos;
        badge.innerText = `${listaRegalos.length} ítems`;

        if (!listaRegalos || listaRegalos.length === 0) {
            contenedor.innerHTML = `
                <div class="col-span-full text-center py-16">
                    <div class="w-16 h-16 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center text-2xl mx-auto mb-4">
                        <i class="fa-solid fa-box-open"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-700">No hay regalos en tu lista</h3>
                    <p class="text-slate-400 text-xs mt-1 max-w-sm mx-auto">Comienza agregando ideas o enlaces externos de tiendas para orientar a tus invitados.</p>
                </div>
            `;
            return;
        }

        contenedor.innerHTML = listaRegalos.map(regalo => {
            const id = regalo._id || regalo.id;
            const solicitada = regalo.cantidad_solicitada || 1;
            const disponible = regalo.cantidad_disponible ?? solicitada;
            const esReservado = regalo.estado === 'reservado' || disponible <= 0;
            const tieneReservas = disponible < solicitada || esReservado;
            
            const link = regalo.link_referencia || regalo.link_externo || '';
            const imagen = regalo.url_imagen || regalo.imagen || '';

            // RF-43: Factor Sorpresa (Nunca mostramos el nombre del invitado al anfitrión)
            const badgeEstado = esReservado
                ? `<span class="text-[10px] font-bold px-2.5 py-1 bg-rose-100 text-rose-800 rounded-full uppercase">🔒 Reservado (🎁 Sorpresa)</span>`
                : (tieneReservas 
                    ? `<span class="text-[10px] font-bold px-2.5 py-1 bg-amber-100 text-amber-800 rounded-full uppercase">🎁 Parcialmente Reservado</span>`
                    : `<span class="text-[10px] font-bold px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-full uppercase">✨ Disponible</span>`);

            const badgeStock = solicitada > 1
                ? `<span class="text-[10px] font-bold px-2 py-0.5 bg-slate-200/70 text-slate-700 rounded-lg font-mono">${disponible}/${solicitada} disp.</span>`
                : '';

            const bloqueImagen = imagen
                ? `<div class="w-full h-44 rounded-xl overflow-hidden bg-white mb-3 border border-slate-100 flex items-center justify-center">
                       <img src="${imagen}" alt="${regalo.nombre}" referrerpolicy="no-referrer" class="w-full h-full object-contain p-2" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\\'w-full h-full flex items-center justify-center text-3xl bg-rose-50/60\\'>🎁</div>';">
                   </div>`
                : `<div class="w-full h-36 rounded-xl bg-rose-50/50 border border-rose-100/60 flex items-center justify-center text-3xl mb-3">
                       🎁
                   </div>`;

            return `
                <div class="border border-slate-100 rounded-2xl p-4 bg-slate-50/50 flex flex-col justify-between hover:border-rose-200 transition-all shadow-xs">
                    <div>
                        <div class="flex justify-between items-center gap-2 mb-3">
                            ${badgeEstado}
                            ${badgeStock}
                        </div>

                        ${bloqueImagen}

                        <h3 class="font-bold text-slate-800 text-base line-clamp-1">${regalo.nombre}</h3>
                        <p class="text-xs text-slate-500 mt-1 line-clamp-2">${regalo.descripcion || 'Sin descripción adicional.'}</p>
                    </div>

                    <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between gap-2">
                        ${link ? `
                            <a href="${link}" target="_blank" rel="noopener noreferrer" class="text-xs font-bold text-rose-500 hover:text-rose-600 flex items-center gap-1.5 truncate">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i> Ver Tienda
                            </a>
                        ` : `
                            <span class="text-xs text-slate-400 italic">Sin enlace</span>
                        `}

                        <div class="flex items-center gap-1 shrink-0">
                            ${tieneReservas ? `
                                <span class="text-[10px] font-bold text-slate-400 italic px-2" title="Contacta a soporte del Administrador si necesitas liberar una unidad">
                                    🔒 Protegido
                                </span>
                            ` : `
                                <button onclick="abrirModalEditar('${id}')" class="p-2 text-slate-400 hover:text-indigo-600 transition-colors cursor-pointer" title="Editar regalo">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button onclick="eliminarRegalo('${id}')" class="p-2 text-slate-400 hover:text-red-500 transition-colors cursor-pointer" title="Eliminar regalo">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            `}
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    // --- GUARDAR O EDITAR REGALO (POST / PUT) ---
    function guardarRegalo(e) {
        e.preventDefault();

        const regaloId = document.getElementById('input-regalo-id').value;
        const nombreVal = document.getElementById('input-nombre').value;
        const cantVal = parseInt(document.getElementById('input-cantidad').value) || 1;
        const descVal = document.getElementById('input-descripcion').value;
        const imgVal = document.getElementById('input-imagen').value;
        const linkVal = document.getElementById('input-link').value;

        const payload = {
            nombre: nombreVal,
            tipo: cantVal > 1 ? 'repetible' : 'unico',
            cantidad_solicitada: cantVal,
            cantidad_disponible: cantVal,
            descripcion: descVal,
            url_imagen: imgVal,
            link_referencia: linkVal
        };

        const esEdicion = Boolean(regaloId);
        const urlPeticion = esEdicion ? `${API_URL}/${regaloId}/editar` : API_URL;
        const metodo = esEdicion ? 'PUT' : 'POST';

        fetch(urlPeticion, {
            method: metodo,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': `Bearer ${TOKEN}`
            },
            body: JSON.stringify(payload)
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) throw new Error(data.mensaje || 'Error al guardar en el servidor');
            return data;
        })
        .then(() => {
            toggleModal('modal-regalo', false);
            cargarRegalos();
            Swal.fire({
                icon: 'success',
                title: esEdicion ? '¡Regalo Actualizado!' : '¡Regalo Añadido!',
                text: 'Los cambios ya están visibles para tus invitados.',
                confirmButtonColor: '#f43f5e',
                customClass: { popup: 'rounded-3xl' }
            });
        })
        .catch(err => {
            console.error(err);
            Swal.fire('Atención', err.message || 'No se pudo guardar el regalo.', 'error');
        });
    }

    // --- LIBERAR RESERVA DE UN REGALO (PUT /reservar) ---
    function liberarReserva(id) {
        Swal.fire({
            title: '¿Liberar este regalo?',
            text: "Volverá a estar disponible para que otro invitado lo elija.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#f59e0b',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Sí, liberar',
            cancelButtonText: 'Cancelar',
            customClass: { popup: 'rounded-3xl' }
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`${API_URL}/${id}/reservar`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${TOKEN}`
                    },
                    body: JSON.stringify({ estado: 'disponible' })
                })
                .then(async res => {
                    const data = await res.json();
                    if (!res.ok) throw new Error(data.mensaje || 'No se pudo liberar');
                    cargarRegalos();
                    Swal.fire({
                        icon: 'success',
                        title: '¡Regalo Liberado!',
                        text: 'El stock vuelve a estar disponible.',
                        timer: 1800,
                        showConfirmButton: false,
                        customClass: { popup: 'rounded-3xl' }
                    });
                })
                .catch(err => Swal.fire('Error', err.message, 'error'));
            }
        });
    }

    // --- ELIMINAR REGALO (DELETE) ---
    function eliminarRegalo(id) {
        Swal.fire({
            title: '¿Quitar este regalo?',
            text: "Desaparecerá permanentemente de tu lista.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            customClass: { popup: 'rounded-3xl' }
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`${API_URL}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${TOKEN}`
                    }
                })
                .then(async res => {
                    const data = await res.json();
                    if (!res.ok) throw new Error(data.mensaje || 'No se pudo eliminar');
                    cargarRegalos();
                    Swal.fire({
                        icon: 'success',
                        title: 'Eliminado',
                        showConfirmButton: false,
                        timer: 1500,
                        customClass: { popup: 'rounded-3xl' }
                    });
                })
                .catch(err => {
                    console.error("Error al borrar regalo:", err);
                    Swal.fire('Atención', err.message, 'warning');
                });
            }
        });
    }
</script>
@endsection