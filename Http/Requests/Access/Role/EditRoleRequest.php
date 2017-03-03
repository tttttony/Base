<?php

namespace Modules\Base\Http\Requests\Access\Role;

use Modules\Base\Http\Requests\Request;

/**
 * Class EditRoleRequest
 * @package Modules\Base\Http\Requests\Access\Role
 */
class EditRoleRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return access()->allow('edit-roles');
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
