<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSubdomain
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $domain = request()->getHttpHost();
        $domainParts = explode('.', $domain);
        if (count($domainParts) > 2) {
            $subdomain = $domainParts[0];
            if ($subdomain !== 'www' && $subdomain !== 'app') {
                return redirect()->route('client-view');
            }
        }
        return $next($request);
    }
}
