<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'MyTransfer') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased font-sans bg-slate-950/80 text-slate-100">
        <div class="min-h-screen flex flex-col">

            <header class="border-b border-slate-800 bg-slate-950/80 backdrop-blur">
                @include('livewire.layout.header')
            </header>

            <main class="flex-1">
                <div class="max-w-6xl mx-auto px-4 py-12 lg:py-20">
                    {{ $slot }}
                </div>
            </main>

            <footer>
                @include('livewire.layout.footer')
            </footer>
        </div>
    </body>
</html>
