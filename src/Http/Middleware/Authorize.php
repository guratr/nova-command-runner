<?php

namespace Guratr\CommandRunner\Http\Middleware;

use Guratr\CommandRunner\CommandRunner;

class Authorize
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response
     */
    public function handle($request, $next)
    {
        return resolve(CommandRunner::class)->authorize($request) ? $next($request) : abort(403);
    }
}
