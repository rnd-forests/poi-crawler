<?php

namespace App\Modules\Auth\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Modules\Shared\Controllers\Controller;

class AuthController extends Controller
{
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return $this->resSuccess(Auth::user());
    }

    public function signOut()
    {
        $user = \Auth::guard()->user();
        $user->token()->revoke();
//        auth()->logout();

        return $this->resNoContent();
    }
}
