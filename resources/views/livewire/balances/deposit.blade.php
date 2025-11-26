<?php

use App\Models\Balance;
use App\Models\Transaction;
use App\Enums\TransactionType;
use App\Enums\TransactionStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    public string $amount = '';

    public int $balanceAmount = 0;

    public ?string $flashMessage = null;
    public ?string $flashError = null;

    public function mount(): void
    {
        $user = Auth::user();

        $this->balanceAmount = Balance::firstOrCreate(
            ['user_id' => $user->id],
            ['amount' => 0],
        )->amount;
    }

    public function getFormattedBalanceProperty(): string
    {
        return 'R$ ' . number_format($this->balanceAmount / 100, 2, ',', '.');
    }

    protected function parseAmountToCents(string $value): int
    {
        $clean = preg_replace('/[^\d,\.]/', '', $value) ?? '0';
        $clean = str_replace(',', '.', $clean);

        return (int) round(((float) $clean) * 100);
    }

    public function deposit(): void
    {
        $this->validate([
            'amount' => 'required|string|min:1',
        ]);

        $amountInCents = $this->parseAmountToCents($this->amount);

        if ($amountInCents <= 0) {
            $this->addError('amount', 'Informe um valor maior que zero.');
            return;
        }

        DB::transaction(function () use ($amountInCents) {
            $user = Auth::user();

            $balance = Balance::where('user_id', $user->id)
                ->lockForUpdate()
                ->first();

            if (! $balance) {
                $balance = Balance::create([
                    'user_id' => $user->id,
                    'amount'  => 0,
                ]);
            }

            $balance->amount += $amountInCents;
            $balance->save();

            Transaction::create([
                'sender_id'   => null,
                'receiver_id' => $user->id,
                'type'        => TransactionType::DEPOSIT,
                'amount'      => $amountInCents,
                'status'      => TransactionStatus::COMPLETED,
            ]);
        });

        $this->balanceAmount += $amountInCents;

        $this->flashMessage = 'Depósito de R$ ' . number_format($amountInCents / 100, 2, ',', '.') . ' realizado com sucesso!';
        $this->flashError = null;
        $this->amount = '';
    }
}; ?>



<x-slot name="header">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="font-semibold text-xl text-black leading-tight">
                Depósito de saldo
            </h2>
            <p class="text-sm text-slate-400">
                Adicione saldo à sua conta MyTransfer.
            </p>
        </div>
    </div>
</x-slot>

<div class="py-10">
    <div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Saldo atual (AGORA DENTRO DO COMPONENTE LIVEWIRE) --}}
        <div class="mb-6 rounded-2xl border border-slate-800 bg-slate-900/80 px-4 py-3">
            <p class="text-xs text-slate-400">
                Saldo atual
            </p>
            <p class="mt-1 text-2xl font-semibold text-emerald-400">
                {{ $this->formattedBalance }}
            </p>
        </div>

        @if ($flashMessage)
            <div class="mb-4 rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-sm text-black">
                {{ $flashMessage }}
            </div>
        @endif

        @if ($flashError)
            <div class="mb-4 rounded-lg border border-rose-500/40 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
                {{ $flashError }}
            </div>
        @endif

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6 shadow-lg shadow-emerald-500/5">
            <form wire:submit="deposit" class="space-y-5">

                {{-- Valor --}}
                <div>
                    <x-input-label for="amount" value="Valor do depósito" class="text-slate-200" />
                    <x-text-input
                        id="amount"
                        type="text"
                        class="mt-1 block w-full bg-slate-950/70 border-slate-700 text-slate-100 placeholder:text-slate-500 focus:border-emerald-500 focus:ring-emerald-500"
                        placeholder="Ex: 150,00"
                        wire:model.defer="amount"
                    />
                    <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-slate-800">
                    <a href="{{ route('dashboard') }}" wire:navigate class="text-xs text-slate-400 hover:text-slate-200">
                        Voltar para o painel
                    </a>

                    <x-primary-button>
                        Depositar saldo
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</div>
