<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageModel;
use App\Models\PostModel;
use App\Models\RevisionModel;
use App\Models\ServiceModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class RevisionController extends Controller
{
    /**
     * @return array<string, class-string>
     */
    private function modelMap(): array
    {
        return [
            'pages' => PageModel::class,
            'posts' => PostModel::class,
            'services' => ServiceModel::class,
        ];
    }

    public function index(string $type, int $id): JsonResponse
    {
        $modelClass = $this->modelMap()[$type] ?? null;

        abort_unless($modelClass, 404);

        $model = $modelClass::findOrFail($id);

        $revisions = $model->revisions()
            ->with('user:id,name')
            ->latest('created_at')
            ->get(['id', 'revisionable_type', 'revisionable_id', 'user_id', 'created_at']);

        return response()->json($revisions);
    }

    public function restore(RevisionModel $revision): RedirectResponse
    {
        $model = $revision->revisionable;

        abort_unless($model, 404);

        $model->createRevision();
        $model->restoreRevision($revision);

        return redirect()->back()->with('success', 'Revision restored.');
    }
}
