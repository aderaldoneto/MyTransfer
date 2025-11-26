<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public string $document = '';
    public string $type = '';


    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'document' => ['required', 'string', 'max:20', 'unique:users,document'],
            'type' => ['required', 'in:pessoa,empresa'],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="flex justify-center">
    <div class="w-full max-w-md">

        <div class="mb-8 text-center space-y-2">
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-50">
                Criar sua conta
            </h1>
            <p class="text-sm text-slate-400">
                Cadastre-se no MyTransfer para começar a enviar e receber transferências com segurança.
            </p>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-6 shadow-lg shadow-emerald-500/5">
            <form wire:submit="register" class="space-y-5">
                <!-- Nome -->
                <div>
                    <x-input-label for="name" :value="__('Nome completo')" class="text-slate-200" />
                    <x-text-input
                        wire:model="name"
                        id="name"
                        class="block mt-1 w-full bg-slate-950/70 border-slate-700 text-slate-100 placeholder:text-slate-500 focus:border-emerald-500 focus:ring-emerald-500"
                        type="text"
                        name="name"
                        required
                        autofocus
                        autocomplete="name"
                        placeholder="Seu nome completo"
                    />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Email -->
                <div>
                    <x-input-label for="email" :value="__('E-mail')" class="text-slate-200" />
                    <x-text-input
                        wire:model="email"
                        id="email"
                        class="block mt-1 w-full bg-slate-950/70 border-slate-700 text-slate-100 placeholder:text-slate-500 focus:border-emerald-500 focus:ring-emerald-500"
                        type="email"
                        name="email"
                        required
                        autocomplete="username"
                        placeholder="seuemail@exemplo.com"
                    />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Senha -->
                <div>
                    <x-input-label for="password" :value="__('Senha')" class="text-slate-200" />
                    <x-text-input
                        wire:model="password"
                        id="password"
                        class="block mt-1 w-full bg-slate-950/70 border-slate-700 text-slate-100 placeholder:text-slate-500 focus:border-emerald-500 focus:ring-emerald-500"
                        type="password"
                        name="password"
                        required
                        autocomplete="new-password"
                        placeholder="••••••••"
                    />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirmar senha -->
                <div>
                    <x-input-label for="password_confirmation" :value="__('Confirmar senha')" class="text-slate-200" />
                    <x-text-input
                        wire:model="password_confirmation"
                        id="password_confirmation"
                        class="block mt-1 w-full bg-slate-950/70 border-slate-700 text-slate-100 placeholder:text-slate-500 focus:border-emerald-500 focus:ring-emerald-500"
                        type="password"
                        name="password_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="Repita a senha"
                    />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                {{-- Tipo de cadastro --}}
                <div>
                    <x-input-label for="type" :value="__('Tipo de cadastro')" class="text-slate-200" />
                    <select
                        wire:model="type"
                        id="type"
                        name="type"
                        class="block mt-1 w-full rounded-md bg-slate-950/70 border-slate-700 text-slate-100 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                    >
                        <option value="">Selecione...</option>
                        <option value="pessoa">Pessoa Física</option>
                        <option value="empresa">Empresa (CNPJ)</option>
                    </select>
                    <x-input-error :messages="$errors->get('type')" class="mt-2" />
                </div>

                {{-- CPF / CNPJ --}}
                <div>
                    <x-input-label for="document" :value="__('Documento (CPF/CNPJ)')" class="text-slate-200" />
                    <x-text-input
                        wire:model="document"
                        id="document"
                        class="block mt-1 w-full bg-slate-950/70 border-slate-700 text-slate-100 placeholder:text-slate-500 focus:border-emerald-500 focus:ring-emerald-500"
                        type="text"
                        name="document"
                        placeholder="Apenas números"
                    />
                    <x-input-error :messages="$errors->get('document')" class="mt-2" />
                </div>

                <div class="flex items-center justify-between pt-2">
                    <a
                        class="text-xs text-slate-400 hover:text-emerald-300 underline-offset-2 hover:underline"
                        href="{{ route('login') }}"
                        wire:navigate
                    >
                        {{ __('Já tem conta? Entrar') }}
                    </a>

                    <x-primary-button class="bg-emerald-500 text-slate-950 hover:bg-emerald-400">
                        {{ __('Criar conta') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</div>
