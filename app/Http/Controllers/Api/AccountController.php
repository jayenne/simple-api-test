<?php

namespace App\Http\Controllers\Api;

use Log;
use App\Models\Account;
use App\Enums\PaymentsEnum;
use App\Rules\PaymentsRule;
use Illuminate\Http\Request;
use App\Enums\HttpStatusEnum;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\JsonResponseService;
use Illuminate\Support\Facades\Validator;
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
        #write your code for account creation here...
        #model name = Account
        #table name = accounts
        #table fields = id,balance
        #all fields are required

        /**
         * @note    "all fields are required" is ambiguous as it seems to conflict with readme.md
         *          ID should not be a required field for a post.
         *          Creating an account should not also create a payment and so balance should be 0.
         *          A second call should be required to may a payment.
         */

        // @note the balance should be created using a factory or set in the db schema as either nullable or default to 0
        try {
            // create an account then return the model
            $account  = Account::create([
                'balance' => PaymentsEnum::DEFAULT_VALUE->value(),
            ])->toArray();

            // @note this reposne is on brief but far too simplistic
            return response()->json($account, 201);

            // @note it would be better to return an abstracted custom json response than a model resource
            // return $responseService->jsonResponse(
            //     status: HttpStatusEnum::SUCCESS->value(),
            //     httpCode: 201,
            //     data: $account
            // );
        } catch (ModelNotFoundException $e) {
            // Log errors and return a response
            Log::error(__('responses.model_creation_failure', ['model' => 'Account']) . ' ' . $e->getMessage());

            // Return a 500 response as a JsonResponse
            return $responseService->jsonResponse(
                status: HttpStatusEnum::SUCCESS->value(),
                httpCode: 201,
                errors: ['account' => __('responses.model_creation_failure', ['model' => 'Account'])],
            );
        }
    }

    public function get(mixed $id, JsonResponseService $responseService): JsonResponse
    {
        #write your code to get account details...
        #model name = Account
        #table name = accounts
        #table fields = id,balance

        // Validate the $id is required
        // @note unfortunately we must write a custom rule because of the tests expectations.
        $min  = PaymentsEnum::MIN_VALUE->value();
        $validator = Validator::make(['id' => $id], [
            'id' => ['required', 'integer', "min:{$min}"],
        ]);

        if ($validator->fails()) {
            // Return a 404 response as a JsonResponse (This should be a 422 as it is a validation error and not a model not found)
            return response()->json(__('responses.model_not_found', ['model' => 'Account']), 404);

            // @note it would be better to return an abstracted custom json response than a model resource
            // return $responseService->jsonResponse(
            //     status: HttpStatusEnum::ERROR->value(),
            //     httpCode: 422,
            //     errors: $validator->errors()->toArray(),
            // );
        }

        // get validated request values
        $validated = $validator->safe();
        $validationErrors = $validator->errors()->toArray();

        // try to get the requested account 
        try {
            $account = Account::findOrFail([
                'id' => $validated['id'],
            ])->first();

            // Return a 200 response as a JsonResponse
            return response()->json($account, 200);

            // @note it would be better to return an abstracted custom json response than a model resource
            // return $responseService->jsonResponse(
            //     status: HttpStatusEnum::SUCCESS->value(),
            //     httpCode: 200,
            //     errors: $validationErrors,
            //     data: $account->toArray()
            // );
        } catch (ModelNotFoundException $e) {
            // Log errors and return a response
            Log::error(__('responses.model_not_found', ['model' => 'Account']) . ' ' . $e->getMessage());

            $validationErrors['account'] = __('responses.model_not_found', ['model' => 'Account']);

            // @note there is no test to catch this instance 
            return response()->json($validationErrors['account'], 404);

            // @note it would be better to return an abstracted custom json response than a model resource
            // return $responseService->jsonResponse(
            //     status: HttpStatusEnum::ERROR->value(),
            //     httpCode: 404,
            //     errors: $validationErrors,
            // );
        }
    }
}
