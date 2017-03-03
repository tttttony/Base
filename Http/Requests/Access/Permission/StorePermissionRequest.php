<?php

namespace Modules\Base\Http\Requests\Access\Permission;

use Modules\Base\Http\Requests\Request;

/**
 * Class StorePermissionRequest
 * @package Modules\Base\Http\Requests\Access\Permission
 */
class StorePermissionRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return access()->allow('create-permissions');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'         => 'required',
            'display_name' => 'required',
        ];
    }
}
