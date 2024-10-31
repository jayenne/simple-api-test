<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Account;

class PaymentTest extends TestCase
{
    /**
     * @test
     *
     * @group payment
     */
    public function test_payment_post()
    {
        $account = Account::first();
        $this->post('/api/payments', ['account' => $account->id, 'amount' => 1000])->assertStatus(201);
    }

    /**
     * @test
     *
     * @group payment
     */
    public function test_payment_post_account_not_found()
    {
        $this->post('/api/payments', ['account' => 'abc', 'amount' => 1000])->assertStatus(422);
    }

    /**
     * @test
     *
     * @group payment
     */
    public function test_payment_post_validation_fail()
    {
        $this->post('/api/payments', ['account' => null, 'amount' => 1000])->assertStatus(422);
    }
}
