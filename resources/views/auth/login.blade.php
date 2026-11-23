<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Invitapp</title>
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
        <div class="max-w-md w-full bg-white/90 backdrop-blur-2xl p-8 sm:p-10 rounded-[2.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.07)] border border-slate-100/80 relative">
            
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-indigo-50 text-indigo-600 text-3xl font-bold mb-4 shadow-inner hover:scale-110 hover:rotate-12 transition-all duration-300 cursor-default">
                    ✨
                </div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Bienvenido de nuevo</h1>
                <p class="text-xs text-slate-500 mt-1 font-medium">Ingresa a tu cuenta para gestionar tus celebraciones</p>
            </div>

            @if(session('error'))
                <div class="mb-6 p-4 bg-rose-50 border border-rose-100 text-rose-600 text-xs font-bold rounded-2xl text-center shadow-sm animate-pulse">
                    ⚠️ {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-600 text-xs font-bold rounded-2xl text-center shadow-sm">
                    ✅ {{ session('success') }}
                </div>
            @endif

            <form action="/login" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-2">Correo Electrónico</label>
                    <input type="email" name="correo" id="correo-dinamico" required 
                           class="w-full px-5 py-3.5 bg-slate-50/80 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500 transition-all text-sm font-medium">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-2">Contraseña</label>
                    <input type="password" name="contrasena" required placeholder="••••••••"
                           class="w-full px-5 py-3.5 bg-slate-50/80 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500 transition-all text-sm font-medium">
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center gap-2 cursor-pointer text-slate-600 font-medium group">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-200 w-4 h-4 transition-transform group-hover:scale-110">
                        <span>Recordarme</span>
                    </label>
                    <a href="/recuperar" class="font-bold text-indigo-600 hover:text-indigo-700 transition-colors">¿Olvidaste tu contraseña?</a>
                </div>

                <button type="submit" class="w-full mt-2 py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all cursor-pointer active:scale-95">
                    Iniciar Sesión
                </button>
            </form>

            <div class="mt-8 text-center border-t border-slate-100 pt-6">
                <p class="text-xs text-slate-500 font-medium">
                    ¿Aún no tienes una cuenta? 
                    <a href="/registro" class="font-bold text-indigo-600 hover:text-indigo-700 transition-colors ml-1">Regístrate gratis</a>
                </p>
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