<?php

namespace Modules\Base\Http\Requests\Access\Permission;

use Modules\Base\Http\Requests\Request;

/**
 * Class CreatePermissionRequest
 * @package Modules\Base\Http\Requests\Access\Permission
 */
class CreatePermissionRequest extends Request
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
            //
        ];
    }
}
