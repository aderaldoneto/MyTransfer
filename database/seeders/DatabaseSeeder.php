<?php

namespace Database\Seeders;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Enums\UserType;
use App\Models\Balance;
use App\Models\Transaction;
use App\Models\User;
use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $mainUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'type'  => UserType::PESSOA->value,
            'document' => fake()->unique()->cpf(),
        ]);

        $otherUsers = User::factory(5)->create();

        Balance::updateOrCreate(
            ['user_id' => $mainUser->id],
            ['amount'  => 2_500_00]
        );

        foreach ($otherUsers as $index => $user) {
            Balance::updateOrCreate(
                ['user_id' => $user->id],
                ['amount'  => (1_000 + $index * 500) * 100]
            );
        }

        DB::transaction(function () use ($mainUser, $otherUsers) {

            Transaction::create([
                'sender_id'       => null,
                'receiver_id'     => $mainUser->id,
                'type'            => TransactionType::DEPOSIT->value,
                'amount'          => 2_000_00,
                'status'          => TransactionStatus::COMPLETED->value,
                'authorization_id'=> null,
                'notified_at'     => now()->subDays(5),
            ]);

            $receiverA = $otherUsers[0];

            Transaction::create([
                'sender_id'       => $mainUser->id,
                'receiver_id'     => $receiverA->id,
                'type'            => TransactionType::TRANSFER->value,
                'amount'          => 300_00, // R$ 300,00
                'status'          => TransactionStatus::COMPLETED->value,
                'authorization_id'=> null,
                'notified_at'     => now()->subDays(3),
            ]);

            $senderB = $otherUsers[1];

            Transaction::create([
                'sender_id'       => $senderB->id,
                'receiver_id'     => $mainUser->id,
                'type'            => TransactionType::TRANSFER->value,
                'amount'          => 150_00, // R$ 150,00
                'status'          => TransactionStatus::COMPLETED->value,
                'authorization_id'=> null,
                'notified_at'     => now()->subDays(2),
            ]);

            $receiverC = $otherUsers[2];

            Transaction::create([
                'sender_id'       => $mainUser->id,
                'receiver_id'     => $receiverC->id,
                'type'            => TransactionType::TRANSFER->value,
                'amount'          => 75_50, // R$ 75,50
                'status'          => TransactionStatus::COMPLETED->value,
                'authorization_id'=> null,
                'notified_at'     => now()->subDay(),
            ]);
        });

        $this->recalculateBalances();

    }

    protected function recalculateBalances(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            $in = Transaction::where('receiver_id', $user->id)
                ->where('status', TransactionStatus::COMPLETED->value)
                ->sum('amount');

            $out = Transaction::where('sender_id', $user->id)
                ->where('status', TransactionStatus::COMPLETED->value)
                ->sum('amount');

            $amount = $in - $out;

            Balance::updateOrCreate(
                ['user_id' => $user->id],
                ['amount'  => max($amount, 0)]
            );
        }
    }
}
