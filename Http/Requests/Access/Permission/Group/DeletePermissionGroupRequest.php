<?php

namespace Modules\Base\Http\Requests\Access\Permission\Group;

use Modules\Base\Http\Requests\Request;

/**
 * Class DeletePermissionGroupRequest
 * @package Modules\Base\Http\Requests\Access\Permission\Group
 */
class DeletePermissionGroupRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return access()->allow('delete-permission-groups');
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
