<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;

class SiteController extends BaseController
{
    /**
     * Get Terms and Conditions details of site.
     *
     * @param  Request  $request
     * @return [json] Terms & Conditions data as array
     * @return [string] message
    */
    public function getTermsConditions(Request $request) {
        $input = $request->all();
        $terms_and_conditions = setting('site.terms_and_conditions');
        $records = [
            "termsAndConditions" => $terms_and_conditions
        ];
        if(count($records) < 0)
        {
            $status_code = config('response_status_code.no_records_found');
            return $this->sendResponse(false, $status_code, trans('message.no_records_found'), $response_data);
        }
        $status_code = config('response_status_code.terms_and_conditions_fetched_success');
        return $this->sendResponse(true, $status_code, trans('message.terms_and_conditions_fetched_success'), $records);
    }
}
