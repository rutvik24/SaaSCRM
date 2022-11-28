<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckSubscription
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
            if ($subdomain !== 'www') {
                $user = DB::connection('mysql2')->table('users')->where('subdomain', $subdomain)->first();
                if ($user) {
                    $subscriptionEndDate = Carbon::parse($user->subscription_end_date);
                    $today = Carbon::today();
                    if ($today->greaterThan($subscriptionEndDate)) {
                        return redirect()->route('expired');
                    }
                }
            }
        }

        return $next($request);
    }
}
