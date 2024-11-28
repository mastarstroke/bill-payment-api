<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WalletTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_a_wallet_for_a_new_user()
    {
        $user = User::factory()->create();

        $this->assertDatabaseHas('wallets', [
            'user_id' => $user->id,
            'balance' => 0,
        ]);
    }


    /** @test */
    public function it_can_fund_a_wallet()
    {
        $user = User::factory()->create();
        $wallet = $user->wallet;

        $wallet->credit(100, 'Initial funding');

        $this->assertEquals(100, $wallet->balance);

        $this->assertDatabaseHas('transactions', [
            'wallet_id' => $wallet->id,
            'type' => 'credit',
            'amount' => 100,
            'description' => 'Initial funding',
        ]);
    }

    /** @test */
    public function it_can_purchase_airtime()
    {
        $user = User::factory()->create();
        $wallet = $user->wallet;

        // Add initial balance
        $wallet->credit(200, 'Initial funding');

        // Simulate airtime purchase
        $wallet->debit(50, 'Airtime purchase for 1234567890');

        $this->assertEquals(150, $wallet->balance);

        $this->assertDatabaseHas('transactions', [
            'wallet_id' => $wallet->id,
            'type' => 'debit',
            'amount' => 50,
            'description' => 'Airtime purchase for 1234567890',
        ]);
    }

    /** @test */
    public function it_prevents_transactions_with_insufficient_balance()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Insufficient wallet balance.');

        $user = User::factory()->create();
        $wallet = $user->wallet;

        $wallet->debit(50, 'Attempt to purchase airtime');
    }

    /** @test */
    public function it_handles_concurrent_transactions_safely()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Fund the wallet with an initial balance
        $wallet = $user->wallet;
        $wallet->credit(200, 'Initial funding');

        // Define payload for airtime purchase
        $payload = ['amount' => 150, 'phone_number' => '1234567890'];

        // Simulate two concurrent requests
        $responses = [
            $this->postJson('/api/bill/airtime', $payload),
            $this->postJson('/api/bill/airtime', $payload),
        ];

        // Assert that at least one request succeeds
        $this->assertTrue($responses[0]->status() === 200 || $responses[1]->status() === 200);

        // Assert that only one transaction was recorded
        $this->assertEquals(1, $wallet->transactions()->where('type', 'debit')->count());

        // Assert the wallet balance was correctly updated
        $wallet->refresh();
        $this->assertEquals(50, $wallet->balance);
    }










}
