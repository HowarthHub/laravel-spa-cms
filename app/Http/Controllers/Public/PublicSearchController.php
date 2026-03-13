<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\PageModel;
use App\Models\PostModel;
use App\Models\ServiceModel;
use App\Services\Interfaces\SettingServiceInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PublicSearchController extends Controller
{
    public function __construct(
        private readonly SettingServiceInterface $settingService
    ) {}

    public function __invoke(Request $request): Response
    {
        $query = $request->string('q')->trim()->toString();
        $results = [
            'pages' => [],
            'posts' => [],
            'services' => [],
        ];

        if ($query !== '') {
            $likeQuery = '%'.$query.'%';

            $results['pages'] = PageModel::published()
                ->where(function ($q) use ($likeQuery) {
                    $q->where('title', 'LIKE', $likeQuery)
                        ->orWhereRaw('CAST(content AS CHAR) LIKE ?', [$likeQuery]);
                })
                ->select('id', 'title', 'slug', 'excerpt', 'meta_description')
                ->limit(10)
                ->get();

            $results['posts'] = PostModel::published()
                ->where(function ($q) use ($likeQuery) {
                    $q->where('title', 'LIKE', $likeQuery)
                        ->orWhere('excerpt', 'LIKE', $likeQuery)
                        ->orWhereRaw('CAST(content AS CHAR) LIKE ?', [$likeQuery]);
                })
                ->select('id', 'title', 'slug', 'excerpt', 'meta_description')
                ->limit(10)
                ->get();

            $results['services'] = ServiceModel::published()
                ->where(function ($q) use ($likeQuery) {
                    $q->where('title', 'LIKE', $likeQuery)
                        ->orWhere('short_description', 'LIKE', $likeQuery);
                })
                ->select('id', 'title', 'slug', 'short_description')
                ->limit(10)
                ->get();
        }

        return Inertia::render('Public/SearchResultsPage', [
            'query' => $query,
            'results' => $results,
            'meta' => $this->buildMeta($query ? "Search: {$query}" : 'Search'),
        ]);
    }

    /**
     * @return array{title: string, description: string, ogImage: string, canonicalUrl: string}
     */
    private function buildMeta(string $title): array
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
            'description' => '',
            'ogImage' => '',
            'canonicalUrl' => url()->current(),
        ];
    }
}
