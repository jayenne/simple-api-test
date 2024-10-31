<?php

namespace Tests\Unit;

use Tests\TestCase;

use App\Http\Controllers\Api\AccountController;
use App\Models\Account;

class AccountTest extends TestCase
{
    /**
     * @test
     *
     * @group account
     */
    public function test_account_post()
    {
        $response = $this->postJson(
            action([AccountController::class, 'store'])
        )->assertStatus(201)->getOriginalContent();
    }

    /**
     * @test
     *
     * @group account
     */
    public function test_account_get()
    {
        $account = Account::first();

        $response = $this->getJson(
            action([AccountController::class, 'get'], $account->id)
        )->assertStatus(200)->getOriginalContent();

        $this->assertEquals($response['data'], $account->toArray());
    }

    /**
     * @test
     *
     * @group account
     */
    public function test_account_get_not_found()
    {
        $this->getJson(
            action([AccountController::class, 'get'], 'abc')
        )->assertStatus(404);
    }
}
