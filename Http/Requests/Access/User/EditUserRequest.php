<?php

namespace Modules\Base\Http\Requests\Access\User;

use Modules\Base\Http\Requests\Request;

/**
 * Class EditUserRequest
 * @package Modules\Base\Http\Requests\Access\User
 */
class EditUserRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return access()->allow('edit-users');
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
