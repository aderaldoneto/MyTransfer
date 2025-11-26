<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-400">
                    Bem-vindo(a) de volta {{ auth()->user()?->name }}
                </p>
            </div>

            <div class="hidden sm:flex items-center gap-3">
                <span class="text-xs text-slate-400">
                    Último acesso
                </span>
                <span class="inline-flex items-center rounded-full border border-emerald-500/40 bg-emerald-500/10 px-3 py-1 text-xs font-medium text-emerald-300">
                    {{ now()->format('d/m/Y H:i') }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            <div class="grid gap-6 md:grid-cols-3">

                <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5 shadow-lg shadow-emerald-500/5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                Saldo disponível
                            </p>
                            <p class="mt-3 text-3xl font-semibold text-slate-50">
                                {{ $balance->formatted_amount }}
                            </p>
                            <p class="mt-1 text-xs text-slate-500">
                                Atualizado em tempo real
                            </p>
                        </div>
                        <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-500/10 border border-emerald-500/30">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M12 3v18m-6-5h12M6 8h12" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    </div>
                </div>


                <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5 shadow-lg shadow-emerald-500/5">
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Entradas do mês
                    </p>
                    <p class="mt-3 text-2xl font-semibold text-emerald-400">
                        {{ $entradaMensalFormatada }}
                    </p>
                    <p class="mt-1 text-xs text-slate-500">
                        Nenhuma entrada registrada neste mês.
                    </p>
                </div>


                <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5 shadow-lg shadow-emerald-500/5">
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Saídas do mês
                    </p>
                    <p class="mt-3 text-2xl font-semibold text-rose-400">
                        {{ $saidaMensalFormatada }}
                    </p>
                    <p class="mt-1 text-xs text-slate-500">
                        Nenhuma transferência enviada neste mês.
                    </p>
                </div>
            </div>


            <div class="grid gap-6 lg:grid-cols-3">

                <div class="space-y-4 lg:col-span-1">
                    <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5 shadow-lg shadow-emerald-500/5">
                        <h3 class="text-sm font-semibold text-slate-100">
                            Ações
                        </h3>
                        <p class="mt-1 text-xs text-slate-400">
                            Realizar transferência.
                        </p>

                        <div class="mt-4 grid grid-cols-1 gap-3">
                            <a
                                href="{{ route('balances.deposit') }}"
                                class="inline-flex items-center justify-between rounded-xl border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-sm font-medium text-emerald-100 hover:bg-emerald-500/20 transition"
                            >
                                <span>Adicionar saldo</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M5 12h14M13 5l7 7-7 7" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                            <a
                                href="{{ route('transfers.create') }}"
                                class="inline-flex items-center justify-between rounded-xl border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-sm font-medium text-emerald-100 hover:bg-emerald-500/20 transition"
                            >
                                <span>Nova transferência</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M5 12h14M13 5l7 7-7 7" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>

                            <a
                                href="{{ route('transfers.list') }}"
                                class="inline-flex items-center justify-between rounded-xl border border-slate-700 bg-slate-900 px-4 py-3 text-sm font-medium text-slate-100 hover:border-emerald-500/60 hover:bg-slate-800 transition"
                            >
                                <span>Extrato</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M7 5h10M7 9h7M7 13h5M7 17h8" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>


                <div class="lg:col-span-2">
                    <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5 shadow-lg shadow-emerald-500/5">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-semibold text-slate-100">
                                    Últimas movimentações
                                </h3>
                            </div>

                            <span class="inline-flex items-center rounded-full border border-slate-700 px-3 py-1 text-[11px] font-medium uppercase tracking-wide text-slate-400">
                                Histórico
                            </span>
                        </div>

                        <div class="mt-5">
                            @php
                                /** @var \Illuminate\Support\Collection|\App\Models\Transaction[] $transactions */
                                $userId = auth()->id();
                            @endphp

                            @if ($transactions->isEmpty())
                                <div class="flex flex-col items-center justify-center rounded-xl border border-dashed border-slate-700 bg-slate-950/40 px-6 py-10 text-center">
                                    <div class="mb-3 inline-flex h-10 w-10 items-center justify-center rounded-full bg-slate-900 border border-slate-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path d="M12 6v6l3 3" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                                            <circle cx="12" cy="12" r="8" stroke-width="1.6" />
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium text-slate-100">
                                        Nenhuma transferência encontrada
                                    </p>
                                    <p class="mt-1 text-xs text-slate-400">
                                        Quando você realizar envios ou recebimentos, eles aparecerão neste painel.
                                    </p>

                                    <a
                                        href="{{ route('transfers.create') }}"
                                        class="mt-4 inline-flex items-center gap-2 rounded-lg bg-emerald-500 px-4 py-2 text-xs font-semibold text-slate-950 hover:bg-emerald-400 transition"
                                    >
                                        Iniciar primeira transferência
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path d="M5 12h14M13 5l7 7-7 7" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </a>
                                </div>
                            @else
                                <div class="rounded-xl border border-slate-800 bg-slate-950/40">
                                    <ul class="divide-y divide-slate-800">
                                        @foreach ($transactions as $transaction)
                                            @php
                                                $isOutgoing = $transaction->sender_id === $userId;
                                                $isIncoming = $transaction->receiver_id === $userId;

                                                $amountFormatted = 'R$ ' . number_format($transaction->amount / 100, 2, ',', '.');

                                                if ($transaction->sender_id === null && $isIncoming) {
                                                    $directionLabel = 'Entrada';
                                                    $directionColor = 'text-emerald-300 bg-emerald-500/10 border-emerald-500/30';
                                                    $counterparty    = 'Depósito';
                                                } elseif ($isOutgoing) {
                                                    $directionLabel = 'Saída';
                                                    $directionColor = 'text-rose-300 bg-rose-500/10 border-rose-500/30';
                                                    $counterparty    = $transaction->receiver?->name ?? 'Destinatário desconhecido';
                                                } else {
                                                    $directionLabel = 'Entrada';
                                                    $directionColor = 'text-emerald-300 bg-emerald-500/10 border-emerald-500/30';
                                                    $counterparty    = $transaction->sender?->name ?? 'Remetente desconhecido';
                                                }

                                                $typeLabel = $transaction->type === \App\Enums\TransactionType::DEPOSIT->value
                                                    ? 'Depósito'
                                                    : 'Transferência';

                                                $status     = $transaction->status;
                                                $statusClasses = match ($status) {
                                                    \App\Enums\TransactionStatus::COMPLETED->value => 'bg-emerald-500/10 text-emerald-300 border-emerald-500/40',
                                                    \App\Enums\TransactionStatus::PENDING->value   => 'bg-amber-500/10 text-amber-200 border-amber-500/40',
                                                    \App\Enums\TransactionStatus::FAILED->value    => 'bg-rose-500/10 text-rose-300 border-rose-500/40',
                                                    default                                         => 'bg-slate-800 text-slate-300 border-slate-600',
                                                };
                                            @endphp

                                            <li class="px-4 py-3 flex items-center justify-between gap-4">
                                                <div class="flex items-center gap-3">
                                                    <div class="inline-flex h-9 w-9 items-center justify-center rounded-full border {{ $directionColor }}">
                                                        @if ($isOutgoing)
                                                            {{-- Ícone seta pra fora --}}
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                                <path d="M7 17L17 7M9 7h8v8" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                                                            </svg>
                                                        @else
                                                            {{-- Ícone seta pra dentro --}}
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                                <path d="M7 7l10 10M15 17H7v-8" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                                                            </svg>
                                                        @endif
                                                    </div>

                                                    <div>
                                                        <p class="text-sm font-medium text-slate-100">
                                                            {{ $typeLabel }} · {{ $directionLabel }}
                                                        </p>
                                                        <p class="text-xs text-slate-400">
                                                            {{ $counterparty }}
                                                        </p>
                                                    </div>
                                                </div>

                                                <div class="text-right">
                                                    <p class="font-mono text-sm {{ $isOutgoing ? 'text-rose-300' : 'text-emerald-300' }}">
                                                        {{ $isOutgoing ? '-' : '+' }} {{ $amountFormatted }}
                                                    </p>
                                                    <div class="mt-1 flex items-center justify-end gap-2 text-[11px] text-slate-500">
                                                        <span>{{ $transaction->created_at->format('d/m H:i') }}</span>
                                                        <span class="inline-flex items-center rounded-full border px-2 py-0.5 {{ $statusClasses }}">
                                                            {{ $status }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>

                                    <div class="flex items-center justify-end px-4 py-2 border-t border-slate-800">
                                        <a href="{{ route('transfers.list') }}" class="text-[11px] text-emerald-300 hover:text-emerald-200">
                                            Ver extrato completo →
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

<footer>
    @include('livewire.layout.footer');
</footer>