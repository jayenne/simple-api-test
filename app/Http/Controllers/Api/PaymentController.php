<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Account;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Enums\HttpStatusEnum;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\JsonResponseService;
use App\Http\Requests\PaymentStoreRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PaymentController extends Controller
{

    /**
     * Create a new Payment model for a given account with a given amount
     *
     * @param Request $request
     * @param ApiJsonResponseService $responseService
     * @return JsonResponse
     */
    public function store(PaymentStoreRequest $request, JsonResponseService $responseService): JsonResponse
    {

        // As we are creating one model and updating another, we should run it as a transaction so that if one fails all fails.
        DB::beginTransaction();

        try {
            // Fetch the account. This will throw an exception if not found.
            $account = Account::findOrFail($request->input('account'));

            // Create the payment
            $payment = Payment::create([
                'account' => $request->input('account'),
                'amount'  => $request->input('amount'),
            ])->toArray();

            // Update the balance for the account
            $account = $this->updateAccountBallance($account, $request->input('amount'));

            DB::commit();

            // Include the updated balance in the response
            $payment['balance'] = $account->balance;

            // Return a 201 response as a JsonResponse
            return $responseService->jsonResponse(
                status: HttpStatusEnum::SUCCESS->value(),
                httpCode: 201,
                data: $payment
            );
        } catch (ModelNotFoundException $e) {
            // Rollback the transaction in case of model not found
            DB::rollBack();

            $errors[] = $e->getMessage();
            $errors[] = __('responses.404');

            return $responseService->jsonResponse(
                status: HttpStatusEnum::ERROR->value(),
                httpCode: 404,
                errors: $errors
            );
        } catch (Exception $e) {
            // Rollback the transaction for any other exceptions
            DB::rollBack();

            $errors[] = $e->getMessage();
            $errors[] = __('responses.500');

            return $responseService->jsonResponse(
                status: HttpStatusEnum::ERROR->value(),
                httpCode: 500,
                errors: $errors
            );
        }
    }

    /**
     * Increment the balance of the account by the given amount.
     *
     * @param Account $account
     * @param integer $amount
     * @return Account
     */
    private function updateAccountBallance(Account $account, int $amount): Account
    {
        $account->increment('balance', $amount);

        return $account;
    }
}
