<?php

namespace App\Http\Controllers\Api;

use Log;
use App\Models\Account;
use App\Enums\PaymentEnum;
use Illuminate\Http\Request;
use App\Enums\HttpStatusEnum;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\JsonResponseService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AccountController extends Controller
{

    /**
     * Create a new Account model then return a json response
     *
     * @param Request $request
     * @param JsonResponseService $responseService
     * @return JsonResponse
     */
    public function store(Request $request, JsonResponseService $responseService): JsonResponse
    {
        try {
            // create an account then return the model
            $account  = Account::create([
                'balance' => PaymentEnum::DEFAULT_VALUE->value(),
            ])->toArray();

            return $responseService->jsonResponse(
                status: HttpStatusEnum::SUCCESS->value(),
                httpCode: 201,
                data: $account
            );
        } catch (ModelNotFoundException $e) {
            // Log errors and return a response
            Log::error(__('responses.model_creation_failure', ['model' => 'Account']) . ' ' . $e->getMessage());

            return $responseService->jsonResponse(
                status: HttpStatusEnum::SUCCESS->value(),
                httpCode: 404,
                errors: ['account' => __('responses.model_creation_failure', ['model' => 'Account'])],
            );
        }
    }

    public function get(Account $account, JsonResponseService $responseService): JsonResponse
    {

        return $responseService->jsonResponse(
            status: HttpStatusEnum::SUCCESS->value(),
            httpCode: 200,
            data: $account->toArray()
        );
    }
}
