<?php

namespace App\Modules\Auth\Controllers;

//use Auth;
//use App\Modules\User\Models\User;
use App\Exceptions\UnauthorizedException;
use App\Modules\Auth\Requests\SignInRequest;
use App\Modules\Shared\Controllers\Controller;
//use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Route;
//use Tymon\JWTAuth\Facades\JWTAuth;

class SignInController extends Controller
{
    public function signIn(SignInRequest $request)
    {
        $credentials = $request->all(['email', 'password']);

//        $user = User::where(['email' => $credentials['email']])->first(['_id', 'name', 'email', 'role_id', 'phone']);

        if (! \Auth::attempt($credentials)) {
            return new UnauthorizedException(null, __('auth.failed'));
        }

        $user = $request->user();
//        $token = $user->createToken('Luxstay', ['luxstay']);
        $token = $user->createToken('Luxstay')->accessToken;

//        if (! $token = Auth::attempt($credentials)) {
//        if (! $token = JWTAuth::attempt($credentials)) {

//        if (!$user || ! $token = auth()->claims(['user' => $user->toArray()])->attempt($credentials)) {
//            return new UnauthorizedException(null, __('auth.failed'));
//        }

//        https://stackoverflow.com/questions/39436509/laravel-passport-scopes
//        https://laravel-news.com/passport-grant-types
//        if ($user->role_id === config('constants.USER.ROLE.ADMIN')) {
//            $request->request->add([
//                'scope' => 'admin'
//            ]);
//        } else if ($user->role_id === config('constants.USER.ROLE.USER')) {
//            $request->request->add([
//                'scope' => 'user'
//            ]);
//        } else {
//            $request->request->add([
//                'scope' => 'locations-read'
//            ]);
//        }
//
//        $tokenRequest = Request::create(
//            '/oauth/token',
//            'post'
//        );
//
//        return Route::dispatch($tokenRequest);

//        return $this->respondWithToken($token);
        return $this->resSuccess([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
            'user' => $user->toArray(),
        ]);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return $this->resSuccess([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60
        ]);
    }
}
