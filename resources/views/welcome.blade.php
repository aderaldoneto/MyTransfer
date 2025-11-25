<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>MyTransfer</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased font-sans bg-slate-950/80 text-slate-100">
        <div class="min-h-screen flex flex-col">

            <header>
                @include('livewire.layout.header')
            </header>


            <main class="flex-1">
                <div class="max-w-6xl mx-auto px-4 py-12 lg:py-20 grid gap-12 lg:grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)] items-center">

                    <section class="space-y-6">
                        <div class="inline-flex items-center gap-2 rounded-full bg-emerald-500/10 border border-emerald-500/40 px-3 py-1 text-xs font-medium text-emerald-300">
                            <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            Plataforma de transferências light
                        </div>

                        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-slate-50 leading-tight">
                            Envie e receba dinheiro entre usuários<br class="hidden sm:block" /> de forma <span class="text-emerald-400">simples, segura</span> e transparente.
                        </h1>

                        <p class="text-slate-300 text-sm sm:text-base max-w-xl">
                            O MyTransfer é uma carteira digital onde usuários e lojistas podem realizar transferências entre si. Você deposita, transfere, acompanha o saldo e recebe notificações de cada operação.
                        </p>

                        <div class="flex flex-wrap items-center gap-3">
                            @if (Route::has('register'))
                                <a
                                    href="{{ route('register') }}"
                                    class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg bg-emerald-500 text-slate-950 font-semibold text-sm hover:bg-emerald-400 transition"
                                >
                                    Começar agora
                                    <svg class="ml-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.5 12h15m0 0-6.75-6.75M19.5 12l-6.75 6.75" />
                                    </svg>
                                </a>
                            @endif

                            @if (Route::has('login'))
                                <a
                                    href="{{ route('login') }}"
                                    class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg border border-slate-700 text-slate-200 text-sm hover:bg-slate-800 transition"
                                >
                                    Já tenho conta
                                </a>
                            @endif
                        </div>

                        <div class="grid gap-3 sm:grid-cols-3 pt-4 border-t border-slate-800 mt-4">
                            <div class="flex flex-col gap-1">
                                <span class="text-xs uppercase tracking-wide text-slate-500">Usuários</span>
                                <span class="text-sm text-slate-100">Envie e receba entre contas pessoais.</span>
                            </div>
                            <div class="flex flex-col gap-1">
                                <span class="text-xs uppercase tracking-wide text-slate-500">Lojistas</span>
                                <span class="text-sm text-slate-100">Lojistas só recebem, com controle total.</span>
                            </div>
                            <div class="flex flex-col gap-1">
                                <span class="text-xs uppercase tracking-wide text-slate-500">Autorização</span>
                                <span class="text-sm text-slate-100">Cada transferência passa por autorização externa.</span>
                            </div>
                        </div>
                    </section>

                    <section class="space-y-4">
                        <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-5 shadow-lg shadow-emerald-500/5">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <p class="text-xs uppercase tracking-wide text-slate-400">Saldo de exemplo</p>
                                    <p class="text-2xl font-semibold text-slate-50">R$ 1.250,00</p>
                                </div>
                                <div class="h-10 w-10 rounded-full bg-emerald-500/10 border border-emerald-500/40 flex items-center justify-center">
                                    <svg class="h-5 w-5 text-emerald-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m6-6H6" />
                                    </svg>
                                </div>
                            </div>

                            <div class="space-y-2 text-xs text-slate-300">
                                <p class="flex items-center gap-2">
                                    <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                                    Depósitos e transferências registradas em histórico.
                                </p>
                                <p class="flex items-center gap-2">
                                    <span class="h-2 w-2 rounded-full bg-sky-400"></span>
                                    Operações revertidas em caso de falha na autorização.
                                </p>
                                <p class="flex items-center gap-2">
                                    <span class="h-2 w-2 rounded-full bg-amber-400"></span>
                                    Notificações via serviço externo simulado.
                                </p>
                            </div>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="rounded-xl border border-slate-800 bg-slate-900/60 p-4 text-sm">
                                <p class="text-xs font-medium text-emerald-300 mb-1">Transferências</p>
                                <p class="text-slate-100 mb-2">Entre usuários e lojistas com regras muito bem definidas!</p>
                                <p class="text-xs text-slate-400">Usuários (pessoa) podem enviar. Empresas só recebem.</p>
                            </div>

                            <div class="rounded-xl border border-slate-800 bg-slate-900/60 p-4 text-sm">
                                <p class="text-xs font-medium text-sky-300 mb-1">Segurança</p>
                                <p class="text-slate-100 mb-2">Transações envolvem autorização externa e rollback!</p>
                                <p class="text-xs text-slate-400">Testes, validações e transações atômicas no backend.</p>
                            </div>
                        </div>

                    </section>
                </div>
            </main>

            <footer>
                @include('livewire.layout.footer')
            </footer>
        </div>
    </body>
</html>
