<?php

namespace App\Http\Controllers\Admin\Auth;

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
                config('app.frontend_url') . 'admins' . '?verified=1'
            );
        }

        if ($request->user('api-vendor')->markEmailAsVerified()) {
            event(new Verified($request->user('api-admin')));
        }

        return redirect()->intended(
            config('app.frontend_url') . 'admins' . '?verified=1'
        );
    }
}