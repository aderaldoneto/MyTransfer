<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
};
?>

<nav x-data="{ open: false }" class="border-b border-slate-800 bg-slate-950/80 backdrop-blur">
    <!-- Navegação principal -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-2">
                        <div class="h-9 w-9 rounded-xl bg-emerald-400/10 border border-emerald-500/40 flex items-center justify-center">
                            <span class="font-bold text-emerald-400">MT</span>
                        </div>
                        <div class="hidden sm:flex flex-col leading-tight">
                            <span class="font-semibold text-slate-50 text-base">MyTransfer</span>
                            <span class="text-[11px] text-slate-400">Transferências simples e seguras</span>
                        </div>
                    </a>
                </div>

                <!-- Links de navegação -->
                <div class="hidden sm:-my-px sm:ms-10 sm:flex sm:items-center sm:space-x-6">
                    <x-nav-link
                        :href="route('dashboard')"
                        :active="request()->routeIs('dashboard')"
                        wire:navigate
                        class="text-sm text-white"
                    >
                        Painel
                    </x-nav-link>
                </div>
            </div>

            <!-- Dropdown de usuário (desktop) -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center gap-2 px-3 py-2 border border-slate-700 text-sm leading-4 font-medium rounded-lg text-slate-200 bg-slate-900 hover:bg-slate-800 hover:border-emerald-500/50 focus:outline-none transition ease-in-out duration-150"
                        >
                            <div
                                class="flex items-center gap-2"
                                x-data="{{ json_encode(['name' => auth()->user()->name]) }}"
                                x-on:profile-updated.window="name = $event.detail.name"
                            >
                                <div class="h-7 w-7 rounded-full bg-emerald-500/10 border border-emerald-500/40 flex items-center justify-center text-[11px] font-semibold text-emerald-300">
                                    {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <span class="hidden md:inline-block text-xs text-slate-300" x-text="name"></span>
                            </div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 pt-3 pb-2 border-b border-slate-800">
                            <div
                                class="font-medium text-sm text-slate-100"
                                x-data="{{ json_encode(['name' => auth()->user()->name]) }}"
                                x-text="name"
                                x-on:profile-updated.window="name = $event.detail.name"
                            ></div>
                            <div class="font-medium text-xs text-slate-400">
                                {{ auth()->user()->email }}
                            </div>
                        </div>

                        <x-dropdown-link :href="route('profile')" wire:navigate>
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Autenticação -->
                        <button wire:click="logout" class="w-full text-start">
                            <x-dropdown-link>
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </button>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Botão hamburguer (mobile) -->
            <div class="-me-2 flex items-center sm:hidden">
                <button
                    @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-slate-400 hover:text-slate-100 hover:bg-slate-800 focus:outline-none focus:bg-slate-800 focus:text-slate-100 transition duration-150 ease-in-out"
                >
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path
                            :class="{'hidden': open, 'inline-flex': ! open }"
                            class="inline-flex"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"
                        />
                        <path
                            :class="{'hidden': ! open, 'inline-flex': open }"
                            class="hidden"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Navegação responsiva (mobile) -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-slate-800 bg-slate-950/95">
        <div class="pt-3 pb-2 space-y-1">
            <x-responsive-nav-link
                :href="route('dashboard')"
                :active="request()->routeIs('dashboard')"
                wire:navigate 
                class="text-white"
            >
                Painel
            </x-responsive-nav-link>
        </div>

        <!-- Opções de usuário (mobile) -->
        <div class="pt-3 pb-4 border-t border-slate-800">
            <div class="px-4">
                <div
                    class="font-medium text-sm text-slate-100"
                    x-data="{{ json_encode(['name' => auth()->user()->name]) }}"
                    x-text="name"
                    x-on:profile-updated.window="name = $event.detail.name"
                ></div>
                <div class="font-medium text-xs text-slate-400">
                    {{ auth()->user()->email }}
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile')" wire:navigate>
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Autenticação -->
                <button wire:click="logout" class="w-full text-start">
                    <x-responsive-nav-link>
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </button>
            </div>
        </div>
    </div>
</nav>
