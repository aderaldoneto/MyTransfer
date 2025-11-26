<?php

use App\Models\User;
use App\Models\Balance;
use App\Services\TransferService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    public ?int $receiver_id = null;
    public string $amount = '';

    /** @var \Illuminate\Support\Collection<int,\App\Models\User> */
    public $receivers;

    public ?string $flashError = null;

    public function mount(): void
    {
        $user = Auth::user();

        $this->receivers = User::query()
            ->where('id', '!=', $user->id)
            ->orderBy('name')
            ->get(['id', 'name', 'document', 'type']);
    }

    public function getUserProperty()
    {
        return Auth::user();
    }

    public function getBalanceProperty(): ?Balance
    {
        return Balance::where('user_id', $this->user->id)->first();
    }

    public function getFormattedBalanceProperty(): string
    {
        $amount = $this->balance?->amount ?? 0;

        return 'R$ ' . number_format($amount / 100, 2, ',', '.');
    }

    protected function parseAmountToCents(string $value): int
    {
        $clean = preg_replace('/[^\d,\. ,]/', '', $value) ?? '0';
        $clean = str_replace(['.', ','], ['', '.'], $clean); // 1.234,56 -> 1234.56

        return (int) round(((float) $clean) * 100);
    }

    public function submit(TransferService $transferService): void
    {
        // validação semelhante ao depósito
        $this->validate([
            'receiver_id' => ['required', 'exists:users,id'],
            'amount'      => ['required', 'string', 'min:1'],
        ]);

        $amountInCents = $this->parseAmountToCents($this->amount);

        if ($amountInCents <= 0) {
            $this->addError('amount', 'Informe um valor maior que zero.');
            return;
        }

        /** @var \App\Models\User $sender */
        $sender   = Auth::user();
        $receiver = User::findOrFail($this->receiver_id);

        try {
            $transferService->transfer($sender, $receiver, $amountInCents);

            session()->flash('status', 'Transferência de R$ ' . number_format($amountInCents / 100, 2, ',', '.') . ' realizada com sucesso!');

            $this->reset('receiver_id', 'amount');
            $this->flashError = null;

            $this->redirect(route('dashboard', absolute: false), navigate: true);
        } catch (\Throwable $e) {
            $this->addError('amount', 'Não foi possível realizar a transferência: ' . $e->getMessage());
            $this->flashError = 'Erro na transferência: ' . $e->getMessage();
        }
    }
}; ?>


<x-slot name="header">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="font-semibold text-xl text-black leading-tight">
                Nova transferência
            </h2>
            <p class="text-sm text-slate-400 mt-1">
                Envie saldo para outro usuário ou lojista.
            </p>
        </div>
    </div>
</x-slot>

<div class="py-10">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Saldo atual (agora dentro do componente, como no depósito) --}}
        <div class="mb-6 rounded-2xl border border-slate-800 bg-slate-900/80 px-4 py-3 flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-400">
                    Seu saldo atual
                </p>
                <p class="mt-1 text-2xl font-semibold text-emerald-400">
                    {{ $this->formattedBalance }}
                </p>
            </div>
        </div>

        @if (session('status'))
            <div class="mb-6 rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        @if ($flashError)
            <div class="mb-4 rounded-lg border border-rose-500/40 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
                {{ $flashError }}
            </div>
        @endif

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6 shadow-lg shadow-emerald-500/5">
            <form wire:submit="submit" class="space-y-5">
                {{-- Destinatário --}}
                <div>
                    <x-input-label for="receiver_id" value="Destinatário" class="text-slate-200" />
                    <select
                        id="receiver_id"
                        wire:model="receiver_id"
                        class="mt-1 block w-full rounded-lg border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm text-slate-100 focus:border-emerald-500 focus:ring-emerald-500"
                    >
                        <option value="">Selecione um usuário...</option>
                        @foreach ($receivers as $receiver)
                            <option value="{{ $receiver->id }}">
                                {{ $receiver->name }} — {{ $receiver->document }} ({{ $receiver->type }})
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('receiver_id')" class="mt-2" />
                </div>

                {{-- Valor --}}
                <div>
                    <x-input-label for="amount" value="Valor da transferência" class="text-slate-200" />
                    <div class="relative mt-1">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-sm text-slate-400">
                            R$
                        </span>
                        <x-text-input
                            id="amount"
                            type="text"
                            wire:model="amount"
                            class="block w-full pl-8 bg-slate-950/60 border-slate-700 text-slate-100 placeholder:text-slate-500 focus:border-emerald-500 focus:ring-emerald-500"
                            placeholder="0,00"
                        />
                    </div>
                    <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                    @if($this->balance)
                        <p class="mt-1 text-xs text-slate-500">
                            Saldo disponível: {{ $this->formattedBalance }}
                        </p>
                    @endif
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-slate-800">
                    <a href="{{ route('dashboard') }}" wire:navigate class="text-xs text-slate-400 hover:text-slate-200">
                        Voltar para o painel
                    </a>

                    <x-primary-button>
                        Confirmar transferência
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</div>
