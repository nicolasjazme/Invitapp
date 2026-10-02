<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class HostController extends Controller
{
    // 🌐 URL Base de la API de Node.js
    private $backendUrl = 'http://localhost:3000/api';

    /**
     * Panel de control principal del Anfitrión / Organizador
     */
  public function index()
{
    // 1. Verificamos si hay un evento activo en la sesión
    $eventoActivo = Session::get('evento_activo');

    // 2. Si NO hay evento activo, lo mandamos a la pantalla de elegir tarjeta
    if (!$eventoActivo) {
        return redirect()->route('anfitrion.mis_eventos');
    }

    // 3. Si SÍ hay un evento activo (ya hizo clic en una tarjeta), le mostramos el Dashboard
    // Aquí puedes simular las métricas o pedir las reales al backend en Node
    $metricas = [
        'total_invitados' => count($eventoActivo['invitados'] ?? []),
        'confirmados' => collect($eventoActivo['invitados'] ?? [])->where('estadoConfirmacion', 'confirmado')->count(),
        'pendientes' => collect($eventoActivo['invitados'] ?? [])->where('estadoConfirmacion', 'pendiente')->count(),
    ];

    // Carga la vista resources/views/anfitrion/index.blade.php
    return view('anfitrion.index', compact('eventoActivo', 'metricas'));
}

    /**
     * Muestra la vista de gestión de invitados del anfitrión
     */
    public function invitadosIndex()
    {
        $token = Session::get('token_jwt');
        $eventoActivo = Session::get('evento_activo');
        
        if (!$token || !$eventoActivo) {
            return redirect('/login')->with('error', 'Sesión expirada o sin evento seleccionado.');
        }

        $eventoId = $eventoActivo['_id'];

        try {
            $response = Http::withToken($token)->get("{$this->backendUrl}/eventos/{$eventoId}/invitados");
            $invitados = $response->successful() ? ($response->json('invitados') ?? $response->json() ?? []) : [];

            $metricas = [
                'confirmados' => count(array_filter($invitados, fn($i) => ($i['estadoConfirmacion'] ?? $i['estadoAsistencia'] ?? '') === 'confirmado')),
                'rechazados'  => count(array_filter($invitados, fn($i) => ($i['estadoConfirmacion'] ?? $i['estadoAsistencia'] ?? '') === 'rechazado')),
                'pendientes'  => count(array_filter($invitados, fn($i) => ($i['estadoConfirmacion'] ?? $i['estadoAsistencia'] ?? 'pendiente') === 'pendiente'))
            ];

            $datosLista = [
                'invitados' => $invitados,
                'listaBloqueada' => false
            ];

            return view('anfitrion.invitados', compact('datosLista', 'metricas', 'eventoActivo'));

        } catch (\Exception $e) {
            Log::error('Error en HostController@invitadosIndex: ' . $e->getMessage());
            return back()->with('error', 'Error al obtener la lista de invitados desde el servidor.');
        }
    }

    /**
     * Agrega un invitado manualmente
     */
    public function invitadosStore(Request $request)
    {
        $token = Session::get('token_jwt');
        $eventoId = Session::get('evento_activo')['_id'] ?? null;

        $datos = [
            'nombre' => $request->input('nombre'),
            'correo' => $request->input('correo'),
            'eventoId' => $eventoId // Inyectamos el ID del evento al crear
        ];

        $response = Http::withToken($token)->post("{$this->backendUrl}/eventos/{$eventoId}/invitados", $datos);

        if ($response->successful()) {
            return back()->with('success', '¡Invitado registrado con éxito!');
        }

        $mensajeError = $response->json('mensaje') ?? 'Error al guardar invitado.';
        return back()->with('error', $mensajeError);
    }

    /**
     * Edita datos o estado de confirmación del invitado
     */
    public function invitadosUpdate(Request $request, $id)
    {
        $token = Session::get('token_jwt');
        $eventoId = Session::get('evento_activo')['_id'] ?? null;

        $datos = array_filter([
            'nombre'           => $request->input('nombre'),
            'correo'           => $request->input('correo'),
            'estadoAsistencia' => $request->input('estadoAsistencia') 
        ], fn($val) => !is_null($val) && $val !== '');

        $response = Http::withToken($token)->put("{$this->backendUrl}/eventos/{$eventoId}/invitados/{$id}", $datos);

        if ($response->successful()) {
            return back()->with('success', 'Datos del invitado actualizados correctamente.');
        }

        return back()->with('error', $response->json('mensaje') ?? 'Error al actualizar invitado.');
    }

    /**
     * Elimina un invitado de la lista
     */
    public function invitadosDestroy($id)
    {
        $token = Session::get('token_jwt');
        $eventoId = Session::get('evento_activo')['_id'] ?? null;

        $response = Http::withToken($token)->delete("{$this->backendUrl}/eventos/{$eventoId}/invitados/{$id}");

        if ($response->successful()) {
            return back()->with('success', 'Invitado removido de la lista.');
        }

        return back()->with('error', 'No se pudo eliminar al invitado.');
    }

    /**
     * Importación masiva de invitados desde un archivo CSV
     */
    public function invitadosImport(Request $request)
    {
        $request->validate([
            'archivo_csv' => 'required|file'
        ]);

        $file = $request->file('archivo_csv');
        $lines = file($file->getRealPath(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $eventoId = Session::get('evento_activo')['_id'] ?? null;

        $invitados = [];
        foreach ($lines as $index => $line) {
            if ($index === 0 && (str_contains(strtolower($line), 'nombre') || str_contains(strtolower($line), 'correo'))) {
                continue;
            }

            $data = str_getcsv($line);
            if (!empty($data[0])) {
                $invitados[] = [
                    'nombre' => trim($data[0]),
                    'correo' => isset($data[1]) ? trim($data[1]) : '',
                    'eventoId' => $eventoId
                ];
            }
        }

        if (empty($invitados)) {
            return back()->with('error', 'El archivo no contiene registros válidos.');
        }

        $token = Session::get('token_jwt');
        $response = Http::withToken($token)->post("{$this->backendUrl}/eventos/{$eventoId}/invitados/importar", [
            'invitados' => $invitados
        ]);

        if ($response->successful()) {
            return back()->with('success', $response->json('mensaje') ?? '¡Carga masiva completada exitosamente!');
        }

        return back()->with('error', $response->json('mensaje') ?? 'Error al procesar la importación masiva.');
    }

    /**
     * Envía recordatorios masivos por correo
     */
    public function enviarRecordatorioMasivo()
    {
        $token = Session::get('token_jwt');
        $eventoId = Session::get('evento_activo')['_id'] ?? null;

        $response = Http::withToken($token)->post("{$this->backendUrl}/asistencia/recordatorio", [
            'eventoId' => $eventoId
        ]);

        if ($response->successful()) {
            return back()->with('success', $response->json('mensaje') ?? 'Recordatorios despachados por correo.');
        }

        return back()->with('error', $response->json('mensaje') ?? 'Error al enviar recordatorios.');
    }

    /**
     * Registrar respuesta de asistencia pública desde la Landing
     */
    public function registrarAsistencia(Request $request)
    {
        $datos = [
            'nombre' => $request->input('nombre'),
            'correo' => $request->input('correo'),
            'estado' => $request->input('estado', 'confirmado'),
            'eventoId' => $request->input('evento_id')
        ];

        $response = Http::post("{$this->backendUrl}/asistencia", $datos);

        if ($response->successful()) {
            return back()->with('success', '¡Asistencia registrada correctamente!');
        }

        return back()->with('error', 'Error al registrar la asistencia.');
    }

    /**
     * Webhook de sincronización de estados emitidos por Node.js
     */
    public function sincronizarDesdeNode(Request $request)
    {
        Log::info('Webhook de sincronización recibido', $request->all());
        return response()->json(['status' => 'Sincronizado']);
    }

    /**
     * Libera la reserva de un artículo o regalo (Anfitrión)
     */
    public function liberarItem(Request $request, $id)
    {
        $token = Session::get('token_jwt');

        $response = Http::withToken($token)->put("{$this->backendUrl}/regalos/{$id}/reservar", [
            'estado'    => 'disponible',
            'reservaId' => $request->input('reserva_id')
        ]);

        if ($response->successful()) {
            return back()->with('success', 'Artículo liberado con éxito.');
        }

        return back()->with('error', $response->json('mensaje') ?? 'Error al liberar el artículo.');
    }

    /**
     * Envía un ticket de soporte o incidencia al administrador
     */
    public function enviarIncidencia(Request $request)
    {
        $usuario = Session::get('usuario_logueado');
        $eventoId = Session::get('evento_activo')['_id'] ?? null;

        $datos = [
            'anfitrion' => $usuario['nombre'] ?? 'Anfitrión',
            'mensaje'   => $request->input('mensaje'),
            'eventoId'  => $eventoId
        ];

        $response = Http::post("{$this->backendUrl}/eventos/incidencias", $datos);

        if ($response->successful()) {
            return back()->with('success', 'Ticket de soporte enviado con éxito.');
        }

        return back()->with('error', 'Error al enviar reporte.');
    }

    /**
     * Muestra la lista de todos los eventos del anfitrión
     */
public function misEventos()
    {
        $token = Session::get('token_jwt');
        
        try {
            // Llamamos directamente a la ruta correcta que el backend preparó
            $response = Http::withToken($token)
                ->timeout(5)
                ->get("http://localhost:3000/api/eventos/mis-eventos/todos");
            
            $data = $response->json();
            
            // Extraemos el array limpio
            $todosMisEventos = $data['eventos'] ?? [];

            return view('anfitrion.mis-eventos', compact('todosMisEventos'));
            
        } catch (\Exception $e) {
            return back()->with('error', 'No se pudieron cargar tus celebraciones.');
        }
    }
}