<?php namespace Modules\Base\Http\Controllers\Api;

use Modules\Base\Http\Controllers\Controller;
use EllipseSynergie\ApiResponse\Contracts\Response;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    protected $response;

    public function __construct(Request $request, Response $response)
    {
        $this->response = $response;
/*
 * //TODO: this is only for retailer, admins can do anything to any account.
        if(!empty($request->all())) {
            if (!access()->user()->hasAccount($request->get('account_number'))) {
                return $this->response->errorUnauthorized('Unauthorized');
            }
        }
*/
    }

}