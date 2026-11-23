<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Invitapp</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased selection:bg-indigo-500 selection:text-white min-h-screen flex flex-col">

    <nav class="w-full bg-white/80 backdrop-blur-md border-b border-slate-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-2 group cursor-pointer">
                    <span class="text-2xl group-hover:rotate-12 transition-transform duration-300">✨</span>
                    <a href="/" class="font-black text-xl tracking-tight text-slate-900 hover:text-indigo-600 transition-colors">Invitapp</a>
                </div>
                <div>
                    <a href="/" class="text-sm font-bold text-slate-600 hover:text-indigo-600 transition-colors px-4 py-2 flex items-center gap-1 group">
                        <span class="group-hover:-translate-x-1 transition-transform">←</span> Volver al inicio
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex-1 flex items-center justify-center p-4 sm:p-6 lg:px-8">
        <div class="max-w-md w-full bg-white/90 backdrop-blur-2xl p-8 sm:p-10 rounded-[2.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.07)] border border-slate-100/80 relative my-8">
            
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-indigo-50 text-indigo-600 text-3xl font-bold mb-4 shadow-inner hover:scale-110 hover:rotate-12 transition-all duration-300 cursor-default">
                    ✨
                </div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Crear Cuenta</h1>
                <p class="text-xs text-slate-500 mt-1 font-medium">Regístrate para organizar tus celebraciones en Invitapp</p>
            </div>

            @if(session('error'))
                <div class="mb-6 p-4 bg-rose-50 border border-rose-100 text-rose-600 text-xs font-bold rounded-2xl text-center shadow-sm animate-pulse">
                    ⚠️ {{ session('error') }}
                </div>
            @endif

            <form action="/registro" method="POST" class="space-y-4">
                @csrf 
                <input type="hidden" name="tipo_evento" value="{{ request()->get('tipo', 'general') }}">
                
                <div>
                    <label for="nombre" class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-2">Nombre Completo</label>
                    <input type="text" id="nombre" name="nombre" required placeholder="Ej: María Pérez"
                        class="w-full px-5 py-3.5 bg-slate-50/80 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500 transition-all text-sm font-medium">
                </div>

                <div>
                    <label for="correo" class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-2">Correo Electrónico</label>
                    <input type="email" id="correo-dinamico" name="correo" required 
                        class="w-full px-5 py-3.5 bg-slate-50/80 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500 transition-all text-sm font-medium">
                </div>

                <div>
                    <label for="contrasena" class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-2">Contraseña</label>
                    <input type="password" id="contrasena" name="contrasena" required placeholder="••••••••"
                        class="w-full px-5 py-3.5 bg-slate-50/80 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500 transition-all text-sm font-medium">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-2">¿Cómo deseas registrarte? *</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="border-2 border-slate-100 rounded-2xl p-4 flex flex-col items-center cursor-pointer bg-slate-50/50 transition-all hover:bg-indigo-50/50 hover:border-indigo-200 has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50/80 has-[:checked]:shadow-sm group select-none">
                            <input type="radio" name="rol" value="anfitrion" required class="sr-only">
                            <span class="text-2xl mb-1 group-hover:animate-bounce transition-transform">📋</span>
                            <span class="text-[10px] font-extrabold text-slate-700 uppercase tracking-wider">Anfitrión</span>
                        </label>    
                    
                        <label class="border-2 border-slate-100 rounded-2xl p-4 flex flex-col items-center cursor-pointer bg-slate-50/50 transition-all hover:bg-indigo-50/50 hover:border-indigo-200 has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50/80 has-[:checked]:shadow-sm group select-none">
                            <input type="radio" name="rol" value="invitado" required class="sr-only">
                            <span class="text-2xl mb-1 group-hover:animate-bounce transition-transform">👥</span>
                            <span class="text-[10px] font-extrabold text-slate-700 uppercase tracking-wider">Invitado</span>
                        </label>
                    </div>
                </div>

                <button type="submit"
                    class="w-full mt-4 py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all cursor-pointer active:scale-95">
                    Registrarme
                </button>
            </form>

            <div class="mt-8 text-center text-xs text-slate-500 font-medium border-t border-slate-100 pt-6">
                ¿Ya tienes una cuenta? 
                <a href="/login" class="text-indigo-600 font-bold hover:text-indigo-700 transition-colors ml-1">Inicia sesión aquí</a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const textos = ["tu@correo.com", "fiesta@asado.cl", "contacto@matrimonio.com", "organizador@cumple.com"];
            let count = 0;
            let index = 0;
            let isDeleting = false;
            const input = document.getElementById('correo-dinamico');

            function typeWriter() {
                const currentText = textos[count % textos.length];
                input.setAttribute('placeholder', currentText.slice(0, index));

                let typeSpeed = isDeleting ? 50 : 100;

                if (!isDeleting && index === currentText.length) {
                    typeSpeed = 2000;
                    isDeleting = true;
                } else if (isDeleting && index === 0) {
                    isDeleting = false;
                    count++;
                    typeSpeed = 500;
                }

                index += isDeleting ? -1 : 1;
                setTimeout(typeWriter, typeSpeed);
            }
            typeWriter();
        });
    </script>
</body>
</html>