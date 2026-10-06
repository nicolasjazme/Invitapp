<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HostController;
use App\Http\Controllers\InvitacionController;

/*
|--------------------------------------------------------------------------
| Web Routes - Invita App Multi-Evento
|--------------------------------------------------------------------------
*/

// ==========================================================================
// 1. RUTAS PÚBLICAS (Acceso Libre Global - No requieren estar logueado)
// ==========================================================================

// Landing Page General con el catálogo de temas (Baby shower, asado, cumpleaños, etc.)
Route::get('/', [EventController::class, 'index'])->name('home');

// Recuperación de Contraseñas
Route::get('/recuperar', [AuthController::class, 'showRecuperarForm']);
Route::post('/recuperar', [AuthController::class, 'recuperarPassword']);

// Autenticación de Usuarios (Anfitriones y Administradores)
Route::get('/registro', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/registro', [AuthController::class, 'register'])->name('register.submit');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

// Enlaces Únicos Dinámicos para Invitados (Mapeados por el Slug del Evento)
Route::get('/e/{slug}', [EventController::class, 'showPublic'])->name('event.public');
Route::post('/asistencia/registrar', [HostController::class, 'registrarAsistencia'])->name('asistencia.store');

// Invitación mágica sin credenciales
Route::get('/invitacion/{token}', [InvitacionController::class, 'mostrarInvitacion'])->name('invitacion.magica');

// Endpoint de Sincronización PÚBLICO para WebSockets / Webhooks de Node.js
Route::post('/api/sincronizar-estado', [HostController::class, 'sincronizarDesdeNode']);


// ==========================================================================
// 2. RUTAS PROTEGIDAS (Requieren Sesión Iniciada via auth.custom)
// ==========================================================================
Route::middleware(['auth.custom'])->group(function () {
    
    // --- Cuenta, Cierre de Sesión y Perfil General ---
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/perfil', [AuthController::class, 'editProfile'])->name('profile.edit');
    Route::put('/perfil', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::post('/perfil/cambiar-contrasena', [AuthController::class, 'actualizarPassword'])->name('perfil.password.update');

    // --- Módulo General de Interacción Interactiva (Regalos, Cooperación, Tablas) ---
    Route::post('/eventos/reservar-item', [EventController::class, 'reserveItem'])->name('items.reserve');

    // --- ACCESOS RÁPIDOS GLOBALES DE EVENTOS ---
    Route::get('/eventos/crear', [EventController::class, 'create'])->name('events.create');
    Route::post('/eventos/guardar', [EventController::class, 'store'])->name('events.store');
    
    // 💡 RUTAS PARA SELECCIONAR EVENTO EN LA PESTAÑA "MIS EVENTOS"
    Route::post('/eventos/seleccionar', [EventController::class, 'seleccionarEvento'])->name('anfitrion.seleccionar_evento');

    // ========================================================
    //   MÓDULO ANFITRIÓN: CONTROL DE SUS EVENTOS PROPIOS
    // ========================================================
    Route::prefix('anfitrion')->name('anfitrion.')->group(function () {
        // Dashboard principal del organizador
        Route::get('/', [HostController::class, 'index'])->name('index');
        
        // Listado de todas mis celebraciones
        Route::get('/mis-eventos', [HostController::class, 'misEventos'])->name('mis_eventos');

        // CRUD de Eventos Dinámicos (Creación según la plantilla elegida)
        Route::get('/eventos/nuevo', [EventController::class, 'create'])->name('events.create');
        Route::post('/eventos/nuevo', [EventController::class, 'store'])->name('events.store');
        Route::get('/eventos/{id}/editar', [EventController::class, 'edit'])->name('events.edit');
        Route::put('/eventos/{id}', [EventController::class, 'update'])->name('events.update');

        // Gestión de Invitados y Envío de Recordatorios por Evento
        Route::get('/invitados', [HostController::class, 'invitadosIndex'])->name('guests.index');
        Route::post('/invitados', [HostController::class, 'invitadosStore'])->name('guests.store');
        Route::put('/invitados/{id}', [HostController::class, 'invitadosUpdate'])->name('guests.update');
        Route::delete('/invitados/{id}', [HostController::class, 'invitadosDestroy'])->name('guests.destroy');
        Route::post('/invitados/importar', [HostController::class, 'invitadosImport'])->name('guests.import');
        Route::post('/invitados/recordatorio', [HostController::class, 'enviarRecordatorioMasivo'])->name('invitados.remind');

        // Logística e Incidencias del Anfitrión
        Route::post('/items/{id}/liberar', [HostController::class, 'liberarItem'])->name('items.restore');
        Route::post('/incidencias', [HostController::class, 'enviarIncidencia'])->name('incidencias.store');
    });


    // ========================================================
    // 🖥️ MÓDULO ADMINISTRADOR: SUPERVISIÓN GLOBAL DE INVITA APP
    // ========================================================
    Route::prefix('admin')->name('admin.')->group(function () {
        // 1. Panel base (Menú de Tarjetas Gigantes)
        Route::get('/', [AdminController::class, 'index'])->name('index');
        
        // 2. Vistas independientes del menú lúdico
        Route::get('/eventos', [AdminController::class, 'eventos'])->name('eventos');
        Route::get('/soporte', [AdminController::class, 'soporte'])->name('soporte');
        Route::get('/metricas', [AdminController::class, 'metricas'])->name('metricas');
        Route::get('/usuarios', [AdminController::class, 'usuarios'])->name('usuarios');

        // 3. Acciones de Control
        Route::post('/eventos/{id}/estado', [AdminController::class, 'updateStatus'])->name('events.status');

        // CRUD Global de Artículos / Requerimientos de Plantillas (Regalos o Insumos base)
        Route::post('/items', [AdminController::class, 'store'])->name('items.store');
        Route::post('/items/liberar/{id}', [AdminController::class, 'restore'])->name('items.restore');
        Route::delete('/items/{id}', [AdminController::class, 'destroy'])->name('items.destroy');
        Route::put('/items/{id}', [AdminController::class, 'update'])->name('items.update');
        Route::post('/items/{id}/liberar-reserva', [AdminController::class, 'liberarReservaItem'])->name('items.liberar_reserva');

        // Cierre de incidentes de soporte escalados
        Route::post('/incidencias/{id}/completar', [AdminController::class, 'completarIncidencia'])->name('incidencias.complete');
    });


    // ========================================================
    // 🧩 RUTAS DE MÓDULOS PREMIUM (Protegidas por sesión)
    // ========================================================
    Route::prefix('eventos/modulos')->group(function () {
        
        // 🎟️ NUEVA RUTA MÓDULO CHECK-IN: Vista del escáner QR en puerta
        // Se coloca arriba para que la ruta maestra no la intercepte como un error.
        Route::get('/checkin', function () {
            return view('anfitrion.modulos.checkin');
        })->name('modulos.checkin');

        // 1. Ruta Maestra para mostrar las vistas (Regalos, Cuotas, etc.)
        Route::get('/{modulo}', [\App\Http\Controllers\ModuloController::class, 'show'])->name('modulos.show');

        // --- ACCIONES MÓDULO: REGALOS ---
        Route::post('/regalos', [\App\Http\Controllers\ModuloController::class, 'storeRegalo']);
        Route::delete('/regalos/{id}', [\App\Http\Controllers\ModuloController::class, 'destroyRegalo']);

        // --- ACCIONES MÓDULO: CUOTAS (LA VACA) ---
        Route::post('/cuotas/configurar', [\App\Http\Controllers\ModuloController::class, 'configurarCuotas']);
        Route::put('/cuotas/pagos/{id}/aprobar', [\App\Http\Controllers\ModuloController::class, 'aprobarPago']);
        Route::delete('/cuotas/pagos/{id}', [\App\Http\Controllers\ModuloController::class, 'rechazarPago']);

        // --- ACCIONES MÓDULO: MESAS ---
        Route::post('/mesas', [\App\Http\Controllers\ModuloController::class, 'storeMesa']);
        Route::delete('/mesas/{id}', [\App\Http\Controllers\ModuloController::class, 'destroyMesa']);
        Route::put('/mesas/asignar', [\App\Http\Controllers\ModuloController::class, 'asignarMesa']);
        Route::put('/mesas/remover', [\App\Http\Controllers\ModuloController::class, 'removerMesa']);
        
    });

});