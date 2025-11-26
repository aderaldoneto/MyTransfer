<?php

namespace Tests\Unit\Models;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_fillable_attributes(): void
    {
        $transaction = new Transaction();

        $this->assertEquals(
            [
                'sender_id',
                'receiver_id',
                'type',
                'amount',
                'status',
                'authorization_id',
                'notified_at',
            ],
            $transaction->getFillable()
        );
    }

    /** @test */
    public function it_casts_type_and_status_as_enums(): void
    {
        $sender   = User::factory()->create();
        $receiver = User::factory()->create();

        $transaction = Transaction::create([
            'sender_id'   => $sender->id,
            'receiver_id' => $receiver->id,
            'type'        => TransactionType::TRANSFER->value,
            'amount'      => 100_00,
            'status'      => TransactionStatus::COMPLETED->value,
            'authorization_id' => null,
            'notified_at' => now(),
        ]);

        $this->assertInstanceOf(TransactionType::class, $transaction->type);
        $this->assertEquals(TransactionType::TRANSFER, $transaction->type);

        $this->assertInstanceOf(TransactionStatus::class, $transaction->status);
        $this->assertEquals(TransactionStatus::COMPLETED, $transaction->status);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $transaction->notified_at);
    }

    /** @test */
    public function it_belongs_to_a_sender_and_a_receiver(): void
    {
        $sender   = User::factory()->create();
        $receiver = User::factory()->create();

        $transaction = Transaction::create([
            'sender_id'   => $sender->id,
            'receiver_id' => $receiver->id,
            'type'        => TransactionType::TRANSFER->value,
            'amount'      => 250_00,
            'status'      => TransactionStatus::COMPLETED->value,
        ]);

        $this->assertInstanceOf(User::class, $transaction->sender);
        $this->assertTrue($transaction->sender->is($sender));

        $this->assertInstanceOf(User::class, $transaction->receiver);
        $this->assertTrue($transaction->receiver->is($receiver));
    }

    /** @test */
    public function it_formats_amount_as_brazilian_currency(): void
    {
        $transaction = new Transaction([
            'amount' => 1_234_56,
        ]);

        $this->assertSame('R$ 1.234,56', $transaction->formatted_amount);
    }
}
