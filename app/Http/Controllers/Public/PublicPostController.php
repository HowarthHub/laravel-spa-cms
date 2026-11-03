<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\PageModel;
use App\Models\PostModel;
use App\Services\Interfaces\SettingServiceInterface;
use Inertia\Inertia;
use Inertia\Response;

class PublicPostController extends Controller
{
    public function __construct(
        private readonly SettingServiceInterface $settingService
    ) {}

    public function index(): Response
    {
        $posts = PostModel::published()
            ->with('author', 'categories')
            ->latest('published_at')
            ->paginate(config('cms.public_per_page.posts', 12));

        $page = PageModel::published()
            ->where('template', 'blog')
            ->first();

        return Inertia::render('Public/BlogIndexPage', [
            'posts' => $posts,
            'page' => $page,
            'meta' => $this->buildMeta($page?->meta_title ?: $page?->title ?: 'Blog', $page?->meta_description),
        ]);
    }

    public function show(string $slug): Response
    {
        $post = PostModel::published()
            ->with('author', 'categories', 'tags')
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedPosts = PostModel::published()
            ->whereHas('categories', function ($query) use ($post) {
                $query->whereIn('categories.id', $post->categories->pluck('id'));
            })
            ->where('id', '!=', $post->id)
            ->with('author', 'categories')
            ->latest('published_at')
            ->limit(3)
            ->get();

        return Inertia::render('Public/BlogShowPage', [
            'post' => $post,
            'relatedPosts' => $relatedPosts,
            'meta' => $this->buildMeta(
                $post->meta_title ?: $post->title,
                $post->meta_description ?: $post->excerpt,
                $post->og_image
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
