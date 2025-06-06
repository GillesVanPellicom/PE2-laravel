<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        return response('<h1 style="color:red;">You are using the "authenticate" middleware, we have switched to "auth".</h1>');
        /* if (!Auth::check()) {
            if ($request->is('courier/*')) {
                return redirect()->route('courier');
            }
            return redirect()->route('auth.login');
        }
        return $next($request);
        */
    }
}
