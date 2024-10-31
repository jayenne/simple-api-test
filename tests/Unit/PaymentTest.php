<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\Api\PaymentController;
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

        $response = $this->postJson(
            action([PaymentController::class, 'store']),
            ['account' => $account->id, 'amount' => 1000]
        )->assertStatus(201)->getOriginalContent();
    }

    /**
     * @test
     *
     * @group payment
     */
    public function test_payment_post_account_not_found()
    {
        $response = $this->postJson(
            action([PaymentController::class, 'store']),
            ['account' => '200000', 'amount' => 1000]
        )->assertStatus(404);
    }

    /**
     * @test
     *
     * @group payment
     */
    public function test_payment_post_account_not_validated()
    {
        $response = $this->postJson(
            action([PaymentController::class, 'store']),
            ['account' => 'abc', 'amount' => 1000]
        )->assertStatus(422);
    }

    /**
     * @test
     *
     * @group payment
     */
    public function test_payment_post_validation_fail()
    {
        $response = $this->postJson(
            action([PaymentController::class, 'store']),
            ['account' => null, 'amount' => 1000]
        )->assertStatus(422);

        $this->assertEquals($response['errMsg'], 'The account field is required; The request is unprocessable.');
    }
}
