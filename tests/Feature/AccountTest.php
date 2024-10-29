<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\{Account, Payment};

class AccountTest extends TestCase
{
    public function test_account_post()
    {
        $response1 = $this->post('/api/accounts')->assertStatus(201)->getOriginalContent();

        $this->assertDatabaseHas('accounts', [
            'id' => $response1['id']
        ]);

        $response2 = $this->post('/api/accounts')->assertStatus(201)->getOriginalContent();

        $this->assertDatabaseHas('accounts', [
            'id' => $response2['id']
        ]);

        $this->assertEquals($response1['id'], $response2['id'] - 1);
    }

    public function test_account_get_by_id()
    {
        $account = Account::first();

        $response = $this->get('/api/accounts/' . $account->id)->assertStatus(200)->getOriginalContent();

        $this->assertEquals($response, $account);
    }

    public function test_account_get_by_id_not_found()
    {
        $response = $this->get('/api/accounts/abc')->assertStatus(404);
    }

    public function test_payment_post()
    {
        $account = Account::first();
        $this->post('/api/payments', ['account' => $account->id, 'amount' => 1000])->assertStatus(201);
    }

    public function test_payment_post_account_not_found()
    {
        $this->post('/api/payments', ['account' => 'abc', 'amount' => 1000])->assertStatus(404);
    }

    public function test_payment_post_validation_fail()
    {
        $this->post('/api/payments', ['account' => null, 'amount' => 1000])->assertStatus(400);
    }
}
