<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Arr;
use App\Http\Controllers\Api\{AccountController, PaymentController};
use App\Models\{Account, Payment};

class AccountTest extends TestCase
{
    public function test_account_post()
    {
        $response = $this->postJson(
            action([AccountController::class, 'store'])
        )->assertStatus(201)->getOriginalContent();
    }

    public function test_account_get()
    {
        $account = Account::first();

        $response = $this->getJson(
            action([AccountController::class, 'get'], $account->id)
        )->assertStatus(200)->getOriginalContent();

        $this->assertEquals($response, $account);
    }

    public function test_account_get_not_found()
    {
        $this->getJson(
            action([AccountController::class, 'get'], 'abc')
        )->assertStatus(404);
    }

    public function test_payment_post()
    {
        $account = Account::first();

        $response = $this->postJson(
            action([PaymentController::class, 'store']),
            ['account' => $account->id, 'amount' => 1000]
        )->assertStatus(201)->getOriginalContent();
    }

    public function test_payment_post_account_not_found()
    {
        $response = $this->postJson(
            action([PaymentController::class, 'store']),
            ['account' => 'abc', 'amount' => 1000]
        )->assertStatus(404);
    }

    public function test_payment_post_validation_fail()
    {
        $response = $this->postJson(
            action([PaymentController::class, 'store']),
            ['account' => null, 'amount' => 1000]
        )->assertStatus(400);

        $this->assertEquals($response['errMsg'], 'MandatoryFieldsNotComplete');
    }
}
