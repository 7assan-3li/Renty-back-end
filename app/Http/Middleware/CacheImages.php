<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CacheImages
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only apply to successful responses for image-related routes or resources
        if ($response->isSuccessful()) {
            // Set cache headers for 1 week
            $response->headers->set('Cache-Control', 'public, max-age=604800, immutable');
            $response->headers->set('Expires', gmdate('D, d M Y H:i:s \G\M\T', time() + 604800));
            
            // Add ETag for browser validation
            $content = $response->getContent();
            if ($content) {
                $response->headers->set('ETag', md5($content));
            }
        }

        return $response;
    }
}
