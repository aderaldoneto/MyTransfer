<?php

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app')] class extends Component
{
    use WithPagination;

    public string $direction = 'all'; // all, in, out

    public function getUserProperty()
    {
        return Auth::user();
    }

    public function getTransactionsProperty()
    {
        $userId = $this->user->id;

        $query = Transaction::query()
            ->with(['sender', 'receiver'])
            ->where(function ($q) use ($userId) {
                $q->where('sender_id', $userId)
                  ->orWhere('receiver_id', $userId);
            })
            ->orderByDesc('created_at');

        if ($this->direction === 'in') {
            $query->where('receiver_id', $userId);
        } elseif ($this->direction === 'out') {
            $query->where('sender_id', $userId);
        }

        return $query->paginate(10);
    }

    public function updatingDirection(): void
    {
        $this->resetPage();
    }
}; ?>

<x-slot name="header">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="font-semibold text-xl text-black leading-tight">
                Extrato de movimentações
            </h2>
            <p class="text-sm text-slate-400 mt-1">
                Acompanhe entradas e saídas da sua conta MyTransfer.
            </p>
        </div>
    </div>
</x-slot>

<div class="py-10">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Filtros --}}
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">

            <div class="inline-flex rounded-full bg-slate-900/70 border border-slate-800 p-1 text-xs">
                <button
                    type="button"
                    wire:click="$set('direction', 'all')"
                    class="px-3 py-1.5 rounded-full {{ $direction === 'all' ? 'bg-slate-800 text-slate-50' : 'text-slate-400' }}"
                >
                    Todas
                </button>
                <button
                    type="button"
                    wire:click="$set('direction', 'in')"
                    class="px-3 py-1.5 rounded-full {{ $direction === 'in' ? 'bg-emerald-500/20 text-emerald-300' : 'text-slate-400' }}"
                >
                    Entradas
                </button>
                <button
                    type="button"
                    wire:click="$set('direction', 'out')"
                    class="px-3 py-1.5 rounded-full {{ $direction === 'out' ? 'bg-rose-500/20 text-rose-300' : 'text-slate-400' }}"
                >
                    Saídas
                </button>
            </div>
        </div>

        @php
            /** @var \Illuminate\Pagination\LengthAwarePaginator $transactions */
            $transactions = $this->transactions;
        @endphp

        @if ($transactions->isEmpty())
            <div class="rounded-2xl border border-dashed border-slate-700 bg-slate-950/50 px-6 py-10 text-center">
                <div class="mb-3 inline-flex h-10 w-10 items-center justify-center rounded-full bg-slate-900 border border-slate-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M12 6v6l3 3" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                        <circle cx="12" cy="12" r="8" stroke-width="1.6" />
                    </svg>
                </div>
                <p class="text-sm font-medium text-slate-100">
                    Nenhuma movimentação encontrada
                </p>
                <p class="mt-1 text-xs text-slate-400">
                    Depósitos e transferências aparecerão aqui assim que forem realizados.
                </p>
            </div>
        @else
            <div class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/80 shadow-lg shadow-emerald-500/5">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-950/60 border-b border-slate-800">
                        <tr class="text-xs uppercase tracking-wide text-slate-400">
                            <th class="px-4 py-3 text-left">Data</th>
                            <th class="px-4 py-3 text-left">Tipo</th>
                            <th class="px-4 py-3 text-left">Contraparte</th>
                            <th class="px-4 py-3 text-right">Valor</th>
                            <th class="px-4 py-3 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $transaction)
                            @php
                                $isOutgoing = $transaction->sender_id === $this->user->id;
                                $isIncoming = $transaction->receiver_id === $this->user->id;

                                $directionLabel = $isOutgoing ? 'Saída' : 'Entrada';
                                $directionColor = $isOutgoing ? 'text-rose-400 bg-rose-500/10 border-rose-500/30'
                                                              : 'text-emerald-300 bg-emerald-500/10 border-emerald-500/30';

                                // Tipo: depósito ou transferência
                                $typeLabel = $transaction->type === \App\Enums\TransactionType::DEPOSIT->value
                                    ? 'Depósito'
                                    : 'Transferência';

                                // Contraparte (quem está do outro lado)
                                if ($transaction->sender_id === null && $isIncoming) {
                                    $counterparty = 'Depósito';
                                } elseif ($isOutgoing) {
                                    $counterparty = $transaction->receiver?->name ?? 'Destinatário desconhecido';
                                } else {
                                    $counterparty = $transaction->sender?->name ?? 'Remetente desconhecido';
                                }

                                $amountFormatted = 'R$ ' . number_format($transaction->amount / 100, 2, ',', '.');

                                // Status badge
                                $status = $transaction->status;
                                $statusClasses = match ($status) {
                                    \App\Enums\TransactionStatus::COMPLETED->value => 'bg-emerald-500/10 text-emerald-300 border-emerald-500/40',
                                    \App\Enums\TransactionStatus::PENDING->value   => 'bg-amber-500/10 text-amber-200 border-amber-500/40',
                                    \App\Enums\TransactionStatus::FAILED->value    => 'bg-rose-500/10 text-rose-300 border-rose-500/40',
                                    default                                         => 'bg-slate-800 text-slate-300 border-slate-600',
                                };
                            @endphp

                            <tr class="border-t border-slate-800/80 hover:bg-slate-900/80 transition">
                                <td class="px-4 py-3 align-middle text-slate-200">
                                    <div class="flex flex-col">
                                        <span>{{ $transaction->created_at->format('d/m/Y') }}</span>
                                        <span class="text-[11px] text-slate-500">{{ $transaction->created_at->format('H:i') }}</span>
                                    </div>
                                </td>

                                <td class="px-4 py-3 align-middle">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-slate-100 text-xs">{{ $typeLabel }}</span>
                                        <span class="inline-flex items-center rounded-full border px-2 py-0.5 text-[11px] font-medium {{ $directionColor }}">
                                            {{ $directionLabel }}
                                        </span>
                                    </div>
                                </td>

                                <td class="px-4 py-3 align-middle text-slate-200">
                                    {{ $counterparty }}
                                </td>

                                <td class="px-4 py-3 align-middle text-right">
                                    <span class="font-mono text-sm {{ $isOutgoing ? 'text-rose-300' : 'text-emerald-300' }}">
                                        {{ $isOutgoing ? '-' : '+' }} {{ $amountFormatted }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 align-middle text-right">
                                    <span class="inline-flex items-center rounded-full border px-2 py-0.5 text-[11px] font-medium {{ $statusClasses }}">
                                        {{ $status }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Paginação --}}
                <div class="border-t border-slate-800 bg-slate-950/60 px-4 py-3">
                    {{ $transactions->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
