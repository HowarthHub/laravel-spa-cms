<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\PageModel;
use App\Models\PostModel;
use App\Models\ServiceModel;
use App\Services\Interfaces\SettingServiceInterface;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PublicPageController extends Controller
{
    public function __construct(
        private readonly SettingServiceInterface $settingService
    ) {}

    public function home(): Response
    {
        $homepageId = $this->settingService->get('general.homepage');

        $page = $homepageId
            ? PageModel::published()->find($homepageId)
            : null;

        return Inertia::render('Public/HomePage', [
            'page' => $page,
            'meta' => $this->buildMeta($page?->meta_title ?: $page?->title ?: 'Home', $page?->meta_description),
        ]);
    }

    public function show(string $slug): Response|RedirectResponse
    {
        $homepageId = $this->settingService->get('general.homepage');
        $page = PageModel::published()
            ->with('parent')
            ->where('slug', $slug)
            ->firstOrFail();

        // Redirect homepage slug to / to avoid duplicate content
        if ($homepageId && (string) $page->id === $homepageId) {
            return redirect('/');
        }

        // Redirect child pages to their nested URL
        if ($page->parent_id && $page->parent) {
            return redirect($page->url_path, 301);
        }

        return $this->renderPage($page);
    }

    public function showChild(string $parent, string $child): Response|RedirectResponse
    {
        $homepageId = $this->settingService->get('general.homepage');

        $page = PageModel::published()
            ->with('parent')
            ->where('slug', $child)
            ->whereHas('parent', fn ($q) => $q->where('slug', $parent))
            ->firstOrFail();

        // Redirect homepage slug to / to avoid duplicate content
        if ($homepageId && (string) $page->id === $homepageId) {
            return redirect('/');
        }

        return $this->renderPage($page);
    }

    private function renderPage(PageModel $page): Response
    {
        $template = $page->template ?? 'default';
        $component = $this->resolveComponent($template);
        $props = [
            'page' => $page,
            'template' => $template,
            'meta' => $this->buildMeta($page->meta_title ?: $page->title, $page->meta_description, $page->og_image),
        ];

        if ($template === 'blog') {
            $props['posts'] = PostModel::published()
                ->with('author', 'categories')
                ->latest('published_at')
                ->paginate(config('cms.public_per_page.posts', 12));
        }

        if ($template === 'services') {
            $props['services'] = ServiceModel::published()
                ->orderBy('sort_order')
                ->get();
        }

        if ($template === 'contact') {
            $page->load('form');
            $props['form'] = $page->form;
        }

        return Inertia::render($component, $props);
    }

    private function resolveComponent(string $template): string
    {
        return match ($template) {
            'blog' => 'Public/PageShowPage',
            'services' => 'Public/PageShowPage',
            'contact' => 'Public/PageShowPage',
            'landing' => 'Public/PageShowPage',
            'page-builder' => 'Public/PageShowPage',
            default => 'Public/PageShowPage',
        };
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
