<?php

namespace Modules\Base\Http\Requests\Access\Role;

use Modules\Base\Http\Requests\Request;

/**
 * Class DeleteRoleRequest
 * @package Modules\Base\Http\Requests\Access\Role
 */
class DeleteRoleRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return access()->allow('delete-roles');
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
