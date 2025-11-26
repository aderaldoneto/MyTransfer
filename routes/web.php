<?php

use App\Enums\TransactionStatus;
use App\Models\Balance;
use App\Models\Transaction;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::view('/', 'welcome');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();

        $balance = Balance::firstOrCreate(
            ['user_id' => $user->id],
            ['amount' => 0]
        );

        $startOfMonth = now()->startOfMonth();
        $endOfMonth   = now()->endOfMonth();

        $entradaMensal = Transaction::where('receiver_id', $user->id)
            ->where('status', TransactionStatus::COMPLETED->value)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $saidaMensal = Transaction::where('sender_id', $user->id)
            ->where('status', TransactionStatus::COMPLETED->value)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        // Formata bonitinho para exibir
        $entradaMensalFormatada = 'R$ ' . number_format($entradaMensal / 100, 2, ',', '.');
        $saidaMensalFormatada = 'R$ ' . number_format($saidaMensal / 100, 2, ',', '.');

        $transactions = Transaction::query()
            ->with(['sender', 'receiver'])
            ->where(function ($q) use ($user) {
                $q->where('sender_id', $user->id)
                  ->orWhere('receiver_id', $user->id);
            })
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('dashboard', [
            'balance'      => $balance,
            'transactions' => $transactions,
            'entradaMensalFormatada' => $entradaMensalFormatada, 
            'saidaMensalFormatada' => $saidaMensalFormatada,
        ]);

    })->name('dashboard');
    
    Route::view('profile', 'profile')
        ->middleware(['auth'])
        ->name('profile');

    Volt::route('/transfer', 'transfers.create')
        ->name('transfers.create');
        
    Volt::route('/transfers', 'transfers.list')
        ->name('transfers.list');

    Volt::route('/balance', 'balances.deposit')
        ->name('balances.deposit');
});


require __DIR__.'/auth.php';
