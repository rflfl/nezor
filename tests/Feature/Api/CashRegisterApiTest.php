<?php

namespace Tests\Feature\Api;

use App\Models\CashRegister;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CashRegisterApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    // test
    public function test_lists_cash_registers()
    {
        CashRegister::factory()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/cash-registers');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    // test
    public function test_opens_a_cash_register()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/cash-registers', [
                'opening_amount' => 150.00,
            ]);

        $response->assertCreated()
            ->assertJsonFragment([
                'opening_amount' => '150.00',
                'status' => 'open',
            ]);

        $this->assertDatabaseHas('cash_registers', [
            'user_id' => $this->user->id,
            'opening_amount' => 150.00,
            'status' => 'open',
        ]);
    }

    // test
    public function test_prevents_opening_when_already_open()
    {
        CashRegister::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'open',
        ]);

        $response = $this->actingAs($this->user)
            ->postJson('/api/cash-registers', [
                'opening_amount' => 100.00,
            ]);

        $response->assertUnprocessable()
            ->assertJsonFragment(['message' => 'Já existe um caixa aberto']);
    }

    // test
    public function test_closes_a_cash_register()
    {
        $cashRegister = CashRegister::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'open',
            'expected_amount' => 500.00,
        ]);

        $response = $this->actingAs($this->user)
            ->putJson("/api/cash-registers/{$cashRegister->id}", [
                'closing_amount' => 495.00,
                'closing_note' => 'Diferença de troco',
            ]);

        $response->assertOk()
            ->assertJsonFragment([
                'status' => 'closed',
                'counted_amount' => '495.00',
                'difference_amount' => '-5.00',
                'closing_note' => 'Diferença de troco',
            ]);
    }

    // test
    public function test_calculates_positive_difference_on_close()
    {
        $cashRegister = CashRegister::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'open',
            'expected_amount' => 500.00,
        ]);

        $response = $this->actingAs($this->user)
            ->putJson("/api/cash-registers/{$cashRegister->id}", [
                'closing_amount' => 510.00,
            ]);

        $response->assertOk()
            ->assertJsonFragment([
                'difference_amount' => '10.00',
            ]);
    }

    // test
    public function test_prevents_closing_already_closed_cash_register()
    {
        $cashRegister = CashRegister::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'closed',
        ]);

        $response = $this->actingAs($this->user)
            ->putJson("/api/cash-registers/{$cashRegister->id}", [
                'closing_amount' => 100.00,
            ]);

        $response->assertUnprocessable()
            ->assertJsonFragment(['message' => 'Caixa já está fechado']);
    }

    // test
    public function test_returns_open_cash_register()
    {
        $cashRegister = CashRegister::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'open',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/cash-registers/open');

        $response->assertOk()
            ->assertJsonFragment(['id' => $cashRegister->id]);
    }

    // test
    public function test_returns_null_when_no_open_cash_register()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/cash-registers/open');

        $response->assertOk()
            ->assertExactJson([]);
    }

    // test
    public function test_deletes_a_cash_register()
    {
        $cashRegister = CashRegister::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'closed',
        ]);

        $this->actingAs($this->user)
            ->deleteJson("/api/cash-registers/{$cashRegister->id}")
            ->assertNoContent();

        $this->assertSoftDeleted('cash_registers', ['id' => $cashRegister->id]);
    }
}
