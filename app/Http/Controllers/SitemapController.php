<?php

namespace App\Http\Controllers;

use App\Models\PageModel;
use App\Models\PostModel;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $pages = PageModel::published()->get();
        $posts = PostModel::published()->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        // Homepage
        $xml .= '<url>';
        $xml .= '<loc>'.url('/').'</loc>';
        $xml .= '<lastmod>'.now()->toW3cString().'</lastmod>';
        $xml .= '<changefreq>daily</changefreq>';
        $xml .= '<priority>1.0</priority>';
        $xml .= '</url>';

        // Blog index
        $xml .= '<url>';
        $xml .= '<loc>'.url('/blog').'</loc>';
        $xml .= '<lastmod>'.now()->toW3cString().'</lastmod>';
        $xml .= '<changefreq>daily</changefreq>';
        $xml .= '<priority>0.9</priority>';
        $xml .= '</url>';

        // Pages
        foreach ($pages as $page) {
            $xml .= '<url>';
            $xml .= '<loc>'.url('/'.$page->slug).'</loc>';
            $xml .= '<lastmod>'.$page->updated_at->toW3cString().'</lastmod>';
            $xml .= '<changefreq>weekly</changefreq>';
            $xml .= '<priority>0.8</priority>';
            $xml .= '</url>';
        }

        // Posts
        foreach ($posts as $post) {
            $xml .= '<url>';
            $xml .= '<loc>'.url('/blog/'.$post->slug).'</loc>';
            $xml .= '<lastmod>'.$post->updated_at->toW3cString().'</lastmod>';
            $xml .= '<changefreq>weekly</changefreq>';
            $xml .= '<priority>0.6</priority>';
            $xml .= '</url>';
        }

        $xml .= '</urlset>';

        return response($xml, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }
}
