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

        $count = Cache::add('global_request_count', 0, 86400) ? 1 : Cache::increment('global_request_count');

        // Send email every time requests cross a multiple of 500
        if ($count % 500 === 0) {
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
