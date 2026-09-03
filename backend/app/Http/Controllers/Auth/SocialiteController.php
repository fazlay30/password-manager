<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GroupProjectController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    /**
     * @throws ValidationException
     */
    public function redirect(Request $request, string $provider)
    {
        if (($request->has('group-invite') && $request->get('group-invite') === 'true') && ($request->has('token') && $request->get('token') !== null)) {
            Session::put('group-invite', $request->get('group-invite'));
            Session::put('token', $request->get('token'));
        }

        $this->validateProvider($provider);

        return Socialite::driver($provider)->redirect();
    }

    /**
     * @throws ValidationException
     */
    public function callback(string $provider)
    {
        $this->validateProvider($provider);

        $response = Socialite::driver($provider)->user();

//        // OAuth 2.0 providers...
//        $token = $response->token;
//        $refreshToken = $response->refreshToken;
//        $expiresIn = $response->expiresIn;
//
//        // OAuth 1.0 providers...
//        $token = $response->token;
//        $tokenSecret = $response->tokenSecret;
//
//        // All providers...
//        $response->getId();
//        $response->getNickname();
//        $response->getName();
//        $response->getEmail();
//        $response->getAvatar();

        $user = User::query()->firstWhere(['email' => $response->getEmail()]);

        if ($user) {
            $user->update([
                $provider . '_id'    => $response->getId(),
                $provider . '_token' => $response->token
            ]);
        } else {
            $user = User::query()->create([
                'name'               => $response->getName(),
                'email'              => $response->getEmail(),
//                'password'           => Hash::make(Str::random(8)),
                $provider . '_id'    => $response->getId(),
                $provider . '_token' => $response->token
            ]);
        }

        auth()->login($user);

        $groupProjectController = new GroupProjectController();
        $verifyResponse = null;
        if ((Session::has('group-invite') && Session::get('group-invite') === 'true') && (Session::has('token') && Session::get('token') !== null)) {
            $verifyResponse = $groupProjectController->verifyInvitation(auth()->id(), Session::get('token'));
            Session::forget(['group-invite', 'token']);
        }

        return redirect()->intended(config('app.frontend_url') . '/dashboard')->with($verifyResponse ? json_decode($verifyResponse->content(), true) : []);
    }

    /**
     * @throws ValidationException
     */
    protected function validateProvider(string $provider): array
    {
        return $this->getValidationFactory()->make(
            ['provider' => $provider],
            ['provider' => 'in:google']
        )->validate();
    }
}
