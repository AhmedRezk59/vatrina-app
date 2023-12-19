<?php

namespace App\Http\Middleware;

use App\Traits\WithApiResponse;
use Closure;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureVendorIsNotBanned
{
    use WithApiResponse;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth('api-vendor')->user()->is_banned == true) {
            throw new HttpResponseException(
                $this->apiResponse(
                    code: 403,
                    msg: 'Banned vendor.'
                )
            );
        }

        return $next($request);
    }
}