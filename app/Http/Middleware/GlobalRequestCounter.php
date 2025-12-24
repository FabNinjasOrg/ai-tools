<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\App;

class GlobalRequestCounter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Run only in production
        if (! App::environment('production')) {
            return $next($request);
        }

        // Initialize global request count if not present and reset after 24 hours
        Cache::remember('global_request_count', 86400, fn () => 0);

        // Increment global request count
        $count = Cache::increment('global_request_count');

        // Send email every time requests cross a multiple of 10
        // (10, 20, 30, ...)
        if ($count % 10 === 0) {
            Mail::raw(
                "Attention: Server has received {$count} total requests.",
                function ($message) {
                    $message->to([
                        'harshp@fabninjas.com',
                        'gsofficework@gmail.com',
                    ])->subject('Face Finder : Server Access Alert');
                }
            );
        }

        return $next($request);
    }
}
