<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;

class JsonResponseService
{
    /**
     * format given data in to a standardised json output
     *
     * @param string $status
     * @param integer $httpCode
     * @param array $errors
     * @param array $data
     * @param array $meta
     * @return JsonResponse
     */
    public function jsonResponse(
        string $status  = 'success',
        int $httpCode   = 200,
        array $errors   = [],
        array $data     = [],
        array $meta     = []
    ): JsonResponse {

        return response()->json(
            [
                'status'    => $status,
                'httpCode'  => $httpCode,
                'errors'    => $errors,
                'errMsg'    => $this->implodeErrorValues($errors), // this is ONLY for the benefit of the given tests
                'data'      => $data,
                'meta'      => $meta,
            ],
            $httpCode
        );
    }


    /**
     * return the leaf values of a nested array as a csv
     *
     * @param array $errors
     * @param string $seperator
     * @return string
     */
    function implodeErrorValues(array $errors, string $seperator = '; '): string
    {
        $leafValues = [];

        $getLeafValues = function ($errors) use (&$getLeafValues, &$leafValues) {
            foreach ($errors as $value) {
                if (is_array($value)) {
                    $getLeafValues($value);
                } else {
                    // trim the trailing full-stop from each error as we are using a seperator.
                    $leafValues[] = rtrim($value, '.');
                }
            }
        };

        $getLeafValues($errors);

        // add a full-stop to the last item in the array, purely for good grammar.
        if (!empty($leafValues)) {
            $leafValues[count($leafValues) - 1] .= '.';
        }

        return implode($seperator, $leafValues);
    }
}
