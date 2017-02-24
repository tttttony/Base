<?php

namespace Modules\Base\Services\Access\Traits;

use Modules\Base\Http\Requests\User\ChangePasswordRequest;

/**
 * Class ChangePasswords
 * @package App\Services\Access\Traits
 */
trait ChangePasswords
{

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showChangePasswordForm()
    {
        return view('auth.passwords.change');
    }

    /**
     * @param ChangePasswordRequest $request
     * @return mixed
     */
    public function changePassword(ChangePasswordRequest $request) {
        $this->user->changePassword($request->all());
        return redirect()->route('user.dashboard')->withFlashSuccess(trans('strings.frontend.user.password_updated'));
    }
}