<header class="border-b border-slate-800 bg-slate-950/80 backdrop-blur">
    <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between gap-4">

        <a href="{{ url('/') }}" class="flex items-center gap-2">
            <div class="h-9 w-9 rounded-xl bg-emerald-400/10 border border-emerald-500/40 flex items-center justify-center">
                <span class="font-bold text-emerald-400">MT</span>
            </div>
            <div class="flex flex-col leading-tight">
                <span class="font-semibold text-slate-50 text-lg">MyTransfer</span>
                <span class="text-xs text-slate-400">Transferências simples e seguras</span>
            </div>
        </a>

        @if (Route::has('login'))
            <nav class="flex items-center gap-2 text-sm">
                @auth
                    <a
                        href="{{ route('dashboard') }}"
                        class="px-4 py-2 rounded-lg bg-emerald-500 text-slate-950 font-semibold hover:bg-emerald-400 transition"
                    >
                        Ir para o painel
                    </a>
                @else
                    <a
                        href="{{ route('login') }}"
                        class="px-4 py-2 rounded-lg border border-slate-700 text-slate-200 hover:bg-slate-800 transition"
                    >
                        Entrar
                    </a>

                    @if (Route::has('register'))
                        <a
                            href="{{ route('register') }}"
                            class="px-4 py-2 rounded-lg bg-emerald-500 text-slate-950 font-semibold hover:bg-emerald-400 transition"
                        >
                            Criar conta
                        </a>
                    @endif
                @endauth
            </nav>
        @endif
    </div>
</header>