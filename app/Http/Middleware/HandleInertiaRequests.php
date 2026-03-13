<?php

namespace App\Http\Middleware;

use App\Models\ContactEnquiryModel;
use App\Models\MenuModel;
use App\Services\Interfaces\SettingServiceInterface;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $settingService = app(SettingServiceInterface::class);

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
                'roles' => $request->user()?->getRoleNames() ?? [],
                'permissions' => $request->user()?->getAllPermissions()->pluck('name') ?? [],
            ],
            'darkMode' => $request->user()?->dark_mode ?? true,
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
                'warning' => session('warning'),
            ],
            'enquiryCount' => $request->user()
                ? ContactEnquiryModel::where('status', 'new')->count()
                : 0,
            'site' => [
                'name' => $settingService->get('general.site_name', 'My Site'),
                'tagline' => $settingService->get('general.tagline', ''),
                'logo' => $settingService->get('general.logo', ''),
                'favicon' => $settingService->get('general.favicon', ''),
                'footer' => $settingService->get('general.footer_text', ''),
            ],
            'socialLinks' => $settingService->group('social'),
            'navigation' => fn () => MenuModel::where('handle', 'main')
                ->first()
                ?->items()
                ->whereNull('parent_id')
                ->with('children')
                ->orderBy('sort_order')
                ->get() ?? [],
        ];
    }
}
