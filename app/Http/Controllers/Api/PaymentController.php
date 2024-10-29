<?php

namespace App\Http\Controllers\Api;

use App\Models\Account;
use App\Models\Payment;
use App\Rules\PaymentsRule;
use Illuminate\Http\Request;
use App\Enums\HttpStatusEnum;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\JsonResponseService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PaymentController extends Controller
{

  /**
   * @note  The README states that this endpoint does not require an ID to create a payment, 
   *        but the comments above imply otherwise (#all fields are required).
   *        Providing an ID to create an entity is not best practice, so I have not 
   *        coded for it.
   * 
   *        Additionally, I have included ENUMS for use instead of magic numbers, custom Rules
   *        and a more realistically structured jsonResponse for a more refined developer
   *        consumer experience. Just switch the returned responses.
   * 
   *        The test precludes proper validation errors on the account value by expecting a 404
   *        rather than a 422 as should be the case for validation errors.
   */

  /**
   * Create a new Payment model for a given account with a given amount
   *
   * @param Request $request
   * @param ApiJsonResponseService $responseService
   * @return JsonResponse
   */
  public function store(Request $request, JsonResponseService $responseService): JsonResponse
  {
    #write your code for payment creation here...
    #model name = Payment
    #table name = payments
    #table fields = id,account,amount
    #all fields are required

    // Validate the request values
    $validator = Validator::make($request->all(), [
      'account' => ['required'], // This should handle more validation rules and return 422 on failure
      'amount' => ['required', 'integer', new PaymentsRule],
    ]);

    if ($validator->fails()) {

      return response()->json(['errMsg' => 'MandatoryFieldsNotComplete'], 400);

      // @note it would be better to return an abstracted custom json response than a model resource
      // return $responseService->jsonResponse(
      //   status: HttpStatusEnum::ERROR->value(),
      //   httpCode: 400,
      //   errors: $validator->errors()->toArray()
      // );
    }

    // get validated request values
    $validated = $validator->safe();
    $validationErrors = $validator->errors()->toArray();

    // Create the payment
    try {
      $account = Account::findOrFail($validated['account']);
    } catch (ModelNotFoundException $e) {

      // Return a 404 resonse as a JsonResponse
      return response()->json(__('responses.model_not_found', ['model' => 'Account']), 404);

      // @note it would be better to return an abstracted custom json response than a model resource
      // $validationErrors['account'] = __('responses.model_not_found', ['model' => 'Account']);

      // return $responseService->jsonResponse(
      //   status: HttpStatusEnum::ERROR->value(),
      //   httpCode: 404,
      //   errors: $validationErrors,
      // );
    }

    $payment = Payment::create([
      'account' => $validated['account'],
      'amount'  => $validated['amount'],
    ])->toArray();

    // update the balance for the account
    $account = $this->updateAccountBallance($account, $validated['amount']);

    // Return a 201 resonse as a JsonResponse
    return response()->json($payment, 201);

    // @note it would be better to return an abstracted custom json response than a model resource
    // @note it may be helpful to also return the balance on the account
    // $payment['balance'] = $account->balance;

    // return $responseService->jsonResponse(
    //   status: HttpStatusEnum::SUCCESS->value(),
    //   httpCode: 201,
    //   errors: $validationErrors,
    //   data: $account->toArray()
    // );
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
