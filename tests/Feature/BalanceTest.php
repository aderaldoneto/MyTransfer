<?php

namespace Tests\Unit\Models;

use App\Models\Balance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BalanceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_belongs_to_a_user(): void
    {
        $user = User::factory()->create();
        $balance = Balance::factory()->create([
            'user_id' => $user->id,
            'amount'  => 100_00, 
        ]);

        $relatedUser = $balance->user;

        $this->assertInstanceOf(User::class, $relatedUser);
        $this->assertTrue($relatedUser->is($user));
    }

    /** @test */
    public function it_formats_amount_as_brazilian_currency(): void
    {
        $balance = Balance::factory()->make([
            'amount' => 1_234_56, 
        ]);

        $formatted = $balance->formatted_amount;

        $this->assertSame('R$ 1.234,56', $formatted);
    }

    /** @test */
    public function it_has_fillable_fields(): void
    {
        $balance = new Balance();

        $this->assertEquals(
            ['user_id', 'amount'],
            $balance->getFillable()
        );
    }
}
