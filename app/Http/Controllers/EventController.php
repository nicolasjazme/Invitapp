<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class EventController extends Controller
{
    /**
     * 1. INDEX: Muestra la Landing Page general de Invita App (Catálogo)
     */
    public function index()
    {
        return view('welcome');
    }

    /**
     * 2. CREATE: Redirige al formulario de creación manual de eventos
     */
    public function create()
    {
        return view('events.create');
    }

    /**
     * 3. STORE: Envía a Node.js los datos formateados y AUTO-ACTIVA el evento
     */
    public function store(Request $request)
    {
        $token = Session::get('token_jwt');
        $usuario = Session::get('usuario_logueado');

        // 🛡️ 1. Verificar sesión
        if (!$token || !$usuario) {
            Session::forget(['usuario_logueado', 'token_jwt', 'evento_activo']);
            return redirect('/login')->with('error', 'Tu sesión ha expirado. Por favor inicia sesión nuevamente.');
        }

        $idUsuario = $usuario['_id'] ?? $usuario['id'] ?? null;

        // 🛡️ 2. Normalizar tipo_evento para encajar en el enum de Mongoose
        $tipoInput = strtolower($request->input('tipo_evento', 'babyshower'));
        $tipoLimpio = str_replace(['_', 'ñ', ' '], ['', 'n', ''], $tipoInput);

        $enumsValidos = ['babyshower', 'matrimonio', 'asado', 'cumpleanos', 'personalizado'];
        $tipoEvento = in_array($tipoLimpio, $enumsValidos) ? $tipoLimpio : 'personalizado';

        // 🛡️ 3. Módulos activos
        $modulosPredefinidos = [
            'babyshower'  => ['regalos', 'itinerario', 'menu'],
            'matrimonio'  => ['regalos', 'mesas', 'itinerario', 'menu', 'galeria'],
            'cumpleanos'  => ['itinerario', 'avisos', 'musica', 'galeria'],
            'asado'       => ['cuota', 'itinerario', 'insumos', 'musica'],
            'personalizado' => ['itinerario', 'avisos', 'musica']
        ];

        if ($tipoEvento === 'personalizado') {
            $modulosActivos = $request->input('modulos_activos', []);
        } else {
            $modulosActivos = $modulosPredefinidos[$tipoEvento] ?? ['itinerario'];
        }

        // 🛡️ 4. Formatear Lugar y Dirección
        $ubicacionForm = $request->input('ubicacion') ?? $request->input('lugar') ?? $request->input('direccion') ?? 'Por definir';
        $lugar = $request->input('lugar') ?? $ubicacionForm;
        $direccion = $request->input('direccion') ?? $ubicacionForm;

        // 🛡️ 5. Generar Slug obligatorio único
        $nombreTitulo = $request->input('titulo') ?? $request->input('nombre') ?? ('Mi ' . ucfirst($tipoEvento));
        $slug = Str::slug($nombreTitulo . '-' . Str::random(6));

        // 🎨 Obtenemos los colores y emojis correctos según la temática
        $estiloTema = $this->obtenerPaletaColor($tipoEvento);

        // 🛡️ 6. Payload 100% compatible con Event.js (Backend)
        $datosEvento = [
            'organizadorId'      => $idUsuario,
            'titulo'             => $nombreTitulo,
            'tipo_evento'        => $tipoEvento,
            'fecha'              => $request->input('fecha', date('Y-m-d')),
            'hora'               => $request->input('hora', '12:00'),
            'lugar'              => $lugar,
            'direccion'          => $direccion,
            'descripcion'        => $request->input('descripcion', ''),
            'mensajeBienvenida'  => $request->input('mensaje_bienvenida', '¡Bienvenidos a mi evento!'),
            'slug'               => $slug,
            'estado'             => 'publicado',
            'modulos_activos'    => $modulosActivos,
            'configVisual'       => [
                'colorTema'  => $estiloTema['hex'] ?? '#4f46e5',
                'iconoEmoji' => $estiloTema['emoji'] ?? '🎉'
            ]
        ];

        try {
            $response = Http::withToken($token)->timeout(5)->post('http://localhost:3000/api/eventos', $datosEvento);

            if ($response->successful()) {
                // 💡 AUTO-ACTIVACIÓN: Forzamos la sobreescritura de la sesión con la data fresca del backend
                $eventoCreado = $response->json('evento') ?? $response->json();
                Session::put('evento_activo', $eventoCreado);
                Session::save(); // Forzamos el guardado inmediato en la sesión
                
                return redirect()->route('anfitrion.index')->with('success', '¡Tu evento ha sido creado de forma espectacular!');
            }

            if ($response->status() === 401) {
                Session::forget(['usuario_logueado', 'token_jwt', 'evento_activo']);
                return redirect('/login')->with('error', 'Tu sesión ha expirado. Por favor ingresa de nuevo.');
            }

            $errorMsg = $response->json('mensaje') ?? $response->json('error') ?? 'Error al guardar el evento en la base de datos.';
            return back()->with('error', $errorMsg);

        } catch (\Exception $e) {
            return back()->with('error', 'Error al conectar con el servidor backend (Node.js).');
        }
    }

    /**
     * 4. EDIT: Carga los datos del evento para el formulario de edición
     */
    public function edit($id)
    {
        $token = Session::get('token_jwt');
        
        try {
            $response = Http::withToken($token)->timeout(5)->get("http://localhost:3000/api/eventos/{$id}");

            if ($response->successful()) {
                $evento = $response->json('evento') ?? $response->json();
                return view('eventos.edit', compact('evento'));
            }
            
            return redirect()->route('anfitrion.index')->with('error', 'No se pudo encontrar la información del evento.');
        } catch (\Exception $e) {
            return back()->with('error', 'Servidor no disponible temporalmente.');
        }
    }

    /**
     * 5. UPDATE: Actualiza los parámetros del evento
     */
    public function update(Request $request, $id)
    {
        $token = Session::get('token_jwt');

        $ubicacionForm = $request->input('ubicacion') ?? $request->input('lugar') ?? 'Por definir';

        $datosActualizar = [
            'fecha'     => $request->input('fecha'),
            'hora'      => $request->input('hora'),
            'lugar'     => $request->input('lugar') ?? $ubicacionForm,
            'direccion' => $request->input('direccion') ?? $ubicacionForm,
            'configVisual' => [
                'colorTema'  => $request->input('color_tema', '#4f46e5'),
                'iconoEmoji' => $request->input('icono_emoji', '✨')
            ]
        ];

        try {
            $response = Http::withToken($token)->timeout(5)->put("http://localhost:3000/api/eventos/{$id}", $datosActualizar);

            if ($response->successful()) {
                // Actualizamos también la sesión si el evento editado es el activo
                $eventoActivo = Session::get('evento_activo');
                if (isset($eventoActivo['_id']) && $eventoActivo['_id'] === $id) {
                    $eventoActualizado = $response->json('evento') ?? $response->json();
                    Session::put('evento_activo', $eventoActualizado);
                }

                return back()->with('success', '¡Parámetros del evento actualizados con éxito!');
            }

            return back()->with('error', 'Error al intentar guardar los cambios.');
        } catch (\Exception $e) {
            return back()->with('error', 'Problemas de conexión con el servidor.');
        }
    }

    /**
     * 6. SHOW PUBLIC: Renderiza la invitación pública con Slug
     */
    public function showPublic($slug)
    {
        try {
            $response = Http::timeout(5)->get("http://localhost:3000/api/eventos/publico/{$slug}");

            if (!$response->successful()) {
                dd([
                    'Alerta' => 'El servidor Node.js no encontró el evento público o está oculto.',
                    'slug_buscado'  => $slug,
                    'status_code'   => $response->status(),
                    'response_body' => $response->json() ?? $response->body()
                ]);
            }

            $evento = $response->json('evento') ?? $response->json();
            $items  = $response->json('items') ?? []; 

            $estiloTema = $this->obtenerPaletaColor($evento['tipo_evento'] ?? 'babyshower');

            return view('invitacion.magica', compact('evento', 'items', 'estiloTema'));

        } catch (\Exception $e) {
            dd('Error fatal de conexión con Backend:', $e->getMessage());
        }
    }
    
    /**
     * 7. RESERVE ITEM: Reserva regalos o insumos
     */
    public function reserveItem(Request $request)
    {
        $itemId = $request->input('item_id');
        $nombreInvitado = $request->input('nombre_invitado');

        try {
            $response = Http::timeout(5)->post("http://localhost:3000/api/eventos/items/reservar/{$itemId}", [
                'nombre_invitado' => $nombreInvitado
            ]);

            if ($response->successful()) {
                return back()->with('success', '¡Muchas gracias por tu colaboración!');
            }

            $error = $response->json('mensaje') ?? 'Este artículo ya fue reservado.';
            return back()->with('error', $error);
        } catch (\Exception $e) {
            return back()->with('error', 'Error al procesar tu reserva.');
        }
    }

    /**
     * 8. SELECCIONAR EVENTO: Cambia el evento activo en la sesión (Para la vista "Mis Eventos")
     */
    public function seleccionarEvento(Request $request)
    {
        $eventoId = $request->input('evento_id');
        $token = Session::get('token_jwt');

        try {
            // Obtenemos todos tus eventos usando la ruta confirmada
            $response = Http::withToken($token)
                ->timeout(5)
                ->get("http://localhost:3000/api/eventos/mis-eventos/todos");

            if ($response->successful()) {
                $eventos = $response->json('eventos') ?? [];
                
                // Filtramos el evento específico que seleccionaste en la tarjeta
                $eventoSeleccionado = null;
                foreach ($eventos as $ev) {
                    $id = $ev['_id'] ?? $ev['id'] ?? null;
                    if ((string)$id === (string)$eventoId) {
                        $eventoSeleccionado = $ev;
                        break;
                    }
                }

                if ($eventoSeleccionado) {
                    // Lo guardamos como el evento activo en la sesión
                    Session::put('evento_activo', $eventoSeleccionado);
                    Session::save();
                    
                    // Redirigimos al Dashboard
                    return redirect()->route('anfitrion.index')->with('success', '¡Celebración seleccionada y lista para gestionar!');
                }
            }

            return back()->with('error', 'No se pudo cargar el evento seleccionado.');
        } catch (\Exception $e) {
            return back()->with('error', 'Problemas de conexión al intentar cambiar de evento.');
        }
    }

    /**
     * AUXILIAR: Paleta de color según el tipo de celebración
     */
    private function obtenerPaletaColor($tipo)
    {
        $temas = [
            'babyshower' => [
                'bg_gradient'  => 'from-blue-50 to-pink-50',
                'color_card'   => 'bg-white text-blue-600',
                'color_boton'  => 'bg-blue-500 hover:bg-blue-600 text-white',
                'badge_color'  => 'bg-sky-100 text-sky-800',
                'hex'          => '#3b82f6',
                'emoji'        => '🍼'
            ],
            'matrimonio' => [
                'bg_gradient'  => 'from-rose-50 to-amber-50',
                'color_card'   => 'bg-white text-rose-600',
                'color_boton'  => 'bg-rose-500 hover:bg-rose-600 text-white',
                'badge_color'  => 'bg-rose-100 text-rose-800',
                'hex'          => '#f43f5e',
                'emoji'        => '💍'
            ],
            'cumpleanos' => [
                'bg_gradient'  => 'from-indigo-50 to-purple-50',
                'color_card'   => 'bg-white text-purple-600',
                'color_boton'  => 'bg-purple-600 hover:bg-purple-700 text-white',
                'badge_color'  => 'bg-purple-100 text-purple-800',
                'hex'          => '#9333ea',
                'emoji'        => '🎂'
            ],
            'asado' => [
                'bg_gradient'  => 'from-orange-50 to-amber-100',
                'color_card'   => 'bg-amber-900 text-white',
                'color_boton'  => 'bg-orange-600 hover:bg-orange-700 text-white',
                'badge_color'  => 'bg-orange-100 text-orange-800',
                'hex'          => '#ea580c',
                'emoji'        => '🥩'
            ],
            'personalizado' => [
                'bg_gradient'  => 'from-slate-900 to-purple-950 text-white',
                'color_card'   => 'bg-slate-800 text-fuchsia-400',
                'color_boton'  => 'bg-fuchsia-600 hover:bg-fuchsia-700 text-white',
                'badge_color'  => 'bg-fuchsia-100 text-fuchsia-800',
                'hex'          => '#4f46e5',
                'emoji'        => '⚙️'
            ],
        ];

        return $temas[$tipo] ?? $temas['babyshower'];
    }
}