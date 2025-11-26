<?php

namespace Tests\Unit\Models;

use App\Enums\UserType;
use App\Models\Balance;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_fillable_attributes(): void
    {
        $user = new User();

        $this->assertEquals(
            ['name', 'document', 'email', 'password', 'type', 'created_by'],
            $user->getFillable()
        );
    }

    /** @test */
    public function it_casts_type_as_enum(): void
    {
        $user = User::factory()->create([
            'type' => UserType::PESSOA->value,
        ]);

        $this->assertInstanceOf(UserType::class, $user->type);
        $this->assertEquals(UserType::PESSOA, $user->type);
    }

    /** @test */
    public function it_casts_password_as_hashed(): void
    {
        $user = User::factory()->create([
            'password' => 'secret123',
        ]);

        $this->assertNotEquals('secret123', $user->password);
        $this->assertTrue(password_verify('secret123', $user->password));
    }

    /** @test */
    public function it_has_one_balance(): void
    {
        $user = User::factory()->create();
        $balance = Balance::factory()->create([
            'user_id' => $user->id,
            'amount'  => 500_00,
        ]);

        $this->assertInstanceOf(Balance::class, $user->balance);
        $this->assertTrue($user->balance->is($balance));
    }

    /** @test */
    public function it_has_many_sent_transactions(): void
    {
        $user = User::factory()->create();
        $receiver = User::factory()->create();

        $transactions = Transaction::factory(3)->create([
            'sender_id'   => $user->id,
            'receiver_id' => $receiver->id,
        ]);

        $this->assertCount(3, $user->sentTransactions);
        $this->assertTrue($transactions->first()->is($user->sentTransactions->first()));
    }

    /** @test */
    public function it_has_many_received_transactions(): void
    {
        $user = User::factory()->create();
        $sender = User::factory()->create();

        $transactions = Transaction::factory(2)->create([
            'sender_id'   => $sender->id,
            'receiver_id' => $user->id,
        ]);

        $this->assertCount(2, $user->receivedTransactions);
        $this->assertTrue($transactions->first()->is($user->receivedTransactions->first()));
    }

    /** @test */
    public function it_belongs_to_a_creator_user(): void
    {
        $creator = User::factory()->create();
        $user = User::factory()->create([
            'created_by' => $creator->id,
        ]);

        $this->assertInstanceOf(User::class, $user->creator);
        $this->assertTrue($user->creator->is($creator));
    }

    /** @test */
    public function it_returns_the_correct_type_label(): void
    {
        $userPessoa = User::factory()->create(['type' => UserType::PESSOA->value]);
        $userEmpresa = User::factory()->create(['type' => UserType::EMPRESA->value]);

        $this->assertEquals('Pessoa física', $userPessoa->type_label);
        $this->assertEquals('Empresa', $userEmpresa->type_label);
    }
}
