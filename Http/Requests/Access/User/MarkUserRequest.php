<?php

namespace Modules\Base\Http\Requests\Access\User;

use Modules\Base\Http\Requests\Request;

/**
 * Class MarkUserRequest
 * @package Modules\Base\Http\Requests\Access\User
 */
class MarkUserRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        //Get the 'mark' id
        switch ((int) request()->segment(6)) {
            case 0:
                return access()->allow('deactivate-users');
            break;

            case 1:
                return access()->allow('reactivate-users');
            break;
        }

        return false;
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
