<?php

namespace App\Http\Middleware;

use App\Services\Interfaces\SettingServiceInterface;
use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    public function __construct(
        private readonly SettingServiceInterface $settingService
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isMaintenanceMode = $this->settingService->get('general.maintenance_mode', '0') === '1';

        if (! $isMaintenanceMode) {
            return $next($request);
        }

        // Allow admin routes through
        if ($request->is('admin', 'admin/*')) {
            return $next($request);
        }

        // Allow auth routes through (login, register, password reset, etc.)
        if ($request->is('login', 'register', 'forgot-password', 'reset-password/*', 'logout')) {
            return $next($request);
        }

        $message = $this->settingService->get(
            'general.maintenance_message',
            'We are currently performing scheduled maintenance. We\'ll be back shortly.'
        );

        $siteName = $this->settingService->get('general.site_name', 'My Site');

        return Inertia::render('Public/MaintenancePage', [
            'message' => $message,
            'siteName' => $siteName,
        ])->toResponse($request)->setStatusCode(503);
    }
}
