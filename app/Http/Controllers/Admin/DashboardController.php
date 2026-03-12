<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Dashboard\DashboardIndexRequest;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(DashboardIndexRequest $request): Response
    {
        return Inertia::render('Admin/Dashboard/DashboardIndexPage', [
            'stats' => [
                'totalPages' => 0,
                'publishedPosts' => 0,
                'newEnquiries' => 0,
                'totalMedia' => 0,
            ],
            'recentPosts' => [],
            'recentEnquiries' => [],
        ]);
    }
}
