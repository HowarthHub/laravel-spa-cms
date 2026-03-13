<?php

namespace App\Http\Middleware;

use App\Models\RedirectModel;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleRedirects
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->isMethod('GET') || str_starts_with($request->path(), 'admin')) {
            return $next($request);
        }

        $path = '/'.ltrim($request->path(), '/');

        $redirect = RedirectModel::query()
            ->active()
            ->where('source_path', $path)
            ->first();

        if ($redirect) {
            $redirect->increment('hit_count');
            $redirect->update(['last_hit_at' => now()]);

            return redirect($redirect->destination_path, $redirect->status_code);
        }

        return $next($request);
    }
}
