<?php

namespace App\Http\Controllers\Vendor\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(
                config('app.frontend_url'). 'vendors'.'?verified=1'
            );
        }

        if ($request->user('api-vendor')->markEmailAsVerified()) {
            event(new Verified($request->user('api-vendor')));
        }

        return redirect()->intended(
            config('app.frontend_url'). 'vendors'.'?verified=1'
        );
    }
}
