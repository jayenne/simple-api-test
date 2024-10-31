<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
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
        $response1 = $this->post('/api/account')->assertStatus(201)->getOriginalContent();
        $this->assertDatabaseHas('accounts', [
            'id' => $response1['data']['id']
        ]);

        $response2 = $this->post('/api/account')->assertStatus(201)->getOriginalContent();
        $this->assertDatabaseHas('accounts', [
            'id' => $response2['data']['id']
        ]);

        $this->assertEquals($response1['data']['id'], $response2['data']['id'] - 1);
    }

    /**
     * @test
     *
     * @group account
     */
    public function test_account_get_by_id()
    {
        $account = Account::first();

        $response = $this->get('/api/account/' . $account->id)->assertStatus(200)->getOriginalContent();

        $this->assertEquals($response['data'], $account->toArray());
    }

    /**
     * @test
     *
     * @group account
     */
    public function test_account_get_by_id_not_found()
    {
        $response = $this->get('/api/account/abc');

        $response->assertStatus(404);
    }
}
