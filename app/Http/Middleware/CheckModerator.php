<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckModerator
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login');
        }

        if (!Auth::user()->isAdmin() && !Auth::user()->isModerator()) {
            abort(403, 'Solo i moderatori e gli amministratori possono accedere a questa pagina');
        }

        return $next($request);
    }
}
