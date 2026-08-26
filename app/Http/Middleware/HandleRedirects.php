<?php

namespace App\Http\Middleware;

use App\Models\Redirect;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class HandleRedirects
{
    public function handle(Request $request, Closure $next): Response
    {
        // Skip admin and livewire paths
        $path = '/' . ltrim($request->getPathInfo(), '/');

        if (str_starts_with($path, '/admin') || str_starts_with($path, '/livewire')) {
            return $next($request);
        }

        try {
            if (Schema::hasTable('redirects')) {
                $redirect = Redirect::where('from_url', $path)
                    ->where('active', true)
                    ->first();

                if ($redirect) {
                    return redirect($redirect->to_url, $redirect->http_code);
                }
            }
        } catch (\Throwable $e) {
            // Table may not exist yet — just continue
        }

        return $next($request);
    }
}
