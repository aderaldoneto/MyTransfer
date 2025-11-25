<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="flex justify-center">
    <div class="w-full max-w-md">

        <div class="mb-8 text-center space-y-2">
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-50">
                Entrar na sua conta
            </h1>
            <p class="text-sm text-slate-400">
                Acesse o MyTransfer para acompanhar seu saldo, transferências e notificações.
            </p>
        </div>


        <x-auth-session-status class="mb-4" :status="session('status')" />

        <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-6 shadow-lg shadow-emerald-500/5">
            <form wire:submit="login" class="space-y-5">

                <div>
                    <x-input-label for="email" :value="__('E-mail')" class="text-slate-200" />
                    <x-text-input
                        wire:model="form.email"
                        id="email"
                        class="block mt-1 w-full bg-slate-950/70 border-slate-700 text-slate-100 placeholder:text-slate-500 focus:border-emerald-500 focus:ring-emerald-500"
                        type="email"
                        name="email"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="seuemail@exemplo.com"
                    />
                    <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
                </div>


                <div>
                    <div class="flex items-center justify-between">
                        <x-input-label for="password" :value="__('Senha')" class="text-slate-200" />

                        @if (Route::has('password.request'))
                            <a
                                class="text-xs text-emerald-400 hover:text-emerald-300 underline-offset-2 hover:underline"
                                href="{{ route('password.request') }}"
                                wire:navigate
                            >
                                {{ __('Esqueceu a senha?') }}
                            </a>
                        @endif
                    </div>

                    <x-text-input
                        wire:model="form.password"
                        id="password"
                        class="block mt-1 w-full bg-slate-950/70 border-slate-700 text-slate-100 placeholder:text-slate-500 focus:border-emerald-500 focus:ring-emerald-500"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="••••••••"
                    />
                    <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
                </div>


                <div class="flex items-center justify-between">
                    <label for="remember" class="inline-flex items-center gap-2">
                        <input
                            wire:model="form.remember"
                            id="remember"
                            type="checkbox"
                            class="rounded border-slate-700 bg-slate-950 text-emerald-500 shadow-sm focus:ring-emerald-500 focus:ring-offset-slate-900"
                            name="remember"
                        >
                        <span class="text-sm text-slate-300">
                            {{ __('Lembrar de mim') }}
                        </span>
                    </label>

                    @if (Route::has('register'))
                        <a
                            href="{{ route('register') }}"
                            class="text-xs text-slate-400 hover:text-emerald-300 underline-offset-2 hover:underline"
                            wire:navigate
                        >
                            Ainda não tem conta?
                        </a>
                    @endif
                </div>

                <div class="pt-2">
                    <x-primary-button class="w-full justify-center bg-emerald-500 text-slate-950 hover:bg-emerald-400">
                        {{ __('Entrar') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</div>


