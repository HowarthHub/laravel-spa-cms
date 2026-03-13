<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\PageModel;
use App\Models\ServiceModel;
use App\Services\Interfaces\SettingServiceInterface;
use Inertia\Inertia;
use Inertia\Response;

class PublicServiceController extends Controller
{
    public function __construct(
        private readonly SettingServiceInterface $settingService
    ) {}

    public function index(): Response
    {
        $services = ServiceModel::published()
            ->orderBy('sort_order')
            ->get();

        $page = PageModel::published()
            ->where('template', 'services')
            ->first();

        return Inertia::render('Public/ServicesIndexPage', [
            'services' => $services,
            'page' => $page,
            'meta' => $this->buildMeta($page?->meta_title ?: $page?->title ?: 'Services', $page?->meta_description),
        ]);
    }

    public function show(string $slug): Response
    {
        $service = ServiceModel::published()
            ->where('slug', $slug)
            ->firstOrFail();

        return Inertia::render('Public/ServiceShowPage', [
            'service' => $service,
            'meta' => $this->buildMeta(
                $service->meta_title ?: $service->title,
                $service->meta_description ?: $service->short_description,
                $service->og_image
            ),
        ]);
    }

    /**
     * @return array{title: string, description: string, ogImage: string, canonicalUrl: string}
     */
    private function buildMeta(string $title, ?string $description = null, ?string $ogImage = null): array
    {
        $siteName = $this->settingService->get('general.site_name', 'My Site');
        $template = $this->settingService->get('seo.meta_title_template', '%title% | %site_name%');

        $metaTitle = str_replace(
            ['%title%', '%site_name%'],
            [$title, $siteName],
            $template
        );

        return [
            'title' => $metaTitle,
            'description' => $description ?: $this->settingService->get('seo.meta_description', ''),
            'ogImage' => $ogImage ?: $this->settingService->get('seo.og_image', ''),
            'canonicalUrl' => url()->current(),
        ];
    }
}
