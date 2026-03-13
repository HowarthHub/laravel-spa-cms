<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Settings\SettingIndexRequest;
use App\Http\Requests\Admin\Settings\SettingUpdateRequest;
use App\Models\PageModel;
use App\Models\SettingModel;
use App\Services\Interfaces\SettingServiceInterface;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    public function __construct(
        private readonly SettingServiceInterface $settingService
    ) {}

    public function index(SettingIndexRequest $request): Response
    {
        $settings = SettingModel::all()->groupBy('group')->map(function ($group) {
            return $group->mapWithKeys(fn ($s) => [
                $s->key => ['value' => $s->value, 'type' => $s->type],
            ]);
        });

        return Inertia::render('Admin/Settings/SettingIndexPage', [
            'settings' => $settings,
            'pages' => PageModel::orderBy('title')->get(['id', 'title']),
        ]);
    }

    public function update(SettingUpdateRequest $request): RedirectResponse
    {
        $data = $request->validated();

        foreach ($data['settings'] as $groupAndKey => $value) {
            $this->settingService->set($groupAndKey, $value);
        }

        return redirect()->route('admin.settings.index', ['tab' => $data['tab'] ?? null])
            ->with('success', 'Settings saved.');
    }
}
