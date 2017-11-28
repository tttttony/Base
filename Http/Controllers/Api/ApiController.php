<?php namespace Modules\Base\Http\Controllers\Api;

use Modules\Base\Http\Controllers\Controller;
use EllipseSynergie\ApiResponse\Contracts\Response;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    protected $response;
    protected $sortBy;
    protected $sortOrder;
    protected $count = [];
    protected $filters = [];

    public function __construct(Request $request, Response $response)
    {
        $this->response = $response;

        if ($request->has('filter')) {
            $filters = $request->input('filter');

            foreach ($filters as $filter) {
                $new_filter = explode(':', $filter);
                if (count($new_filter) == 3 and $new_filter[1] == 'in' or $new_filter[1] == '!in') {
                    $new_filter[2] = explode(',', $new_filter[2]);
                }
                $this->filters[] = $new_filter;
            }
        }

        if ($request->has('sort')) {
            $sort = explode(':', $request->input('sort'));
            $this->sortBy = (!empty($sort[0])) ? $sort[0] : null;
            $this->sortOrder = (!empty($sort[1])) ? $sort[1] : null;
        }

        if ($request->has('count')) {
            $this->count = explode(',', $request->input('count'));
        }

        /*
         * //TODO: this is only for retailer, admins can do anything to any account.
                if(!empty($request->all())) {
                    if (!access()->user()->hasAccount($request->get('account_number'))) {
                        return $this->response->errorUnauthorized('Unauthorized');
                    }
                }
        */
    }

    protected function prepare($repo) {
        $repo->withCount($this->count);
        $repo->sort($this->sortBy, $this->sortOrder);

        foreach($this->filters as $filter) {
            switch(count($filter)){
                case 2:
                    $repo->addFilter($filter[0], $filter[1]);
                    break;
                case 3:
                    $repo->addFilter($filter[0], $filter[1], $filter[2]);
                    break;
            }
        }

        return $repo;
    }

}