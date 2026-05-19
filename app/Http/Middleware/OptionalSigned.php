<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OptionalSigned
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->has('signature') && ! $request->hasValidSignature()) {
            abort(403);
        }

        return $next($request);
    }
}
