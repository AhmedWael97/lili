<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ContentService;
use App\Repositories\ContentRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContentApiController extends Controller
{
    public function __construct(
        protected ContentService $contentService,
        protected ContentRepository $contentRepo
    ) {}

    /**
     * List user's content
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $status = $request->input('status');

        $query = $this->contentRepo->getUserContent(Auth::id());

        if ($status) {
            $query->where('status', $status);
        }

        $content = $query->paginate($perPage);

        return response()->json($content);
    }

    /**
     * Get single content item
     */
    public function show(int $id)
    {
        $content = $this->contentRepo->find($id);

        if (!$content || $content->user_id !== Auth::id()) {
            return response()->json(['error' => 'Content not found'], 404);
        }

        return response()->json($content);
    }

    /**
     * Create new content
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'facebook_page_id' => 'required|exists:facebook_pages,id',
            'content_type' => 'required|in:post,story,reel',
            'caption' => 'required|string|max:2200',
            'media_url' => 'nullable|url',
            'scheduled_at' => 'nullable|date|after:now',
            'metadata' => 'nullable|array',
        ]);

        $content = $this->contentService->createContent(Auth::id(), $validated);

        return response()->json($content, 201);
    }

    /**
     * Update content
     */
    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'caption' => 'sometimes|string|max:2200',
            'media_url' => 'nullable|url',
            'metadata' => 'nullable|array',
        ]);

        $content = $this->contentService->updateContent($id, Auth::id(), $validated);

        return response()->json($content);
    }

    /**
     * Delete content
     */
    public function destroy(int $id)
    {
        $this->contentService->deleteContent($id, Auth::id());

        return response()->json(['message' => 'Content deleted successfully']);
    }

    /**
     * Get content statistics
     */
    public function statistics()
    {
        $stats = $this->contentRepo->getStatistics(Auth::id());

        return response()->json($stats);
    }
}
