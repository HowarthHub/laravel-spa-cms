<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Dashboard\DashboardIndexRequest;
use App\Models\FormSubmissionModel;
use App\Models\MediaItemModel;
use App\Models\PageModel;
use App\Models\PostModel;
use App\Models\UserModel;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(DashboardIndexRequest $request): Response
    {
        return Inertia::render('Admin/Dashboard/DashboardIndexPage', [
            'stats' => [
                'totalPages' => PageModel::count(),
                'publishedPosts' => PostModel::where('status', 'published')->count(),
                'draftPosts' => PostModel::where('status', 'draft')->count(),
                'newEnquiries' => FormSubmissionModel::where('created_at', '>=', now()->subDays(7))->count(),
                'totalMedia' => MediaItemModel::count(),
                'totalUsers' => UserModel::count(),
            ],
            'recentPosts' => PostModel::with('author')
                ->latest('updated_at')
                ->limit(5)
                ->get(['id', 'title', 'status', 'author_id', 'updated_at']),
            'recentEnquiries' => FormSubmissionModel::with('form:id,name')
                ->latest()
                ->limit(5)
                ->get(),
            'recentPages' => PageModel::with('author')
                ->latest('updated_at')
                ->limit(5)
                ->get(['id', 'title', 'status', 'author_id', 'updated_at']),
        ]);
    }
}
