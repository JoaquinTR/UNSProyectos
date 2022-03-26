<?php

namespace App\Http\Middleware;

use Closure;

class isAlumno
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(!auth()->user()->isProfesor()) {
            return $next($request);
        }
        return redirect('dashboard');
    }
}
