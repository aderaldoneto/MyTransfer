<?php

namespace Database\Factories;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $sender   = User::factory()->create();
        $receiver = User::factory()->create();

        return [
            'sender_id'   => $sender->id,
            'receiver_id' => $receiver->id,
            'type'        => TransactionType::TRANSFER->value,
            'amount'      => $this->faker->numberBetween(1_00, 500_00),
            'status'      => TransactionStatus::COMPLETED->value,
            'authorization_id' => null,
            'notified_at' => null,
        ];
    }
}
