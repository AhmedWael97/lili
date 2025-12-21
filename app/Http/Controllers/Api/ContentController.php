<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\ContentService;
use App\Repositories\ContentRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContentController extends Controller
{
    public function __construct(
        protected ContentService $contentService,
        protected ContentRepository $contentRepo
    ) {}

    /**
     * Get all content for authenticated user
     */
    public function index(Request $request)
    {
        $status = $request->query('status');
        $pageId = $request->query('page_id');
        
        $query = $this->contentRepo->getUserContent(Auth::id());
        
        if ($status) {
            $query->where('status', $status);
        }
        
        if ($pageId) {
            $query->where('facebook_page_id', $pageId);
        }
        
        $content = $query->with('facebookPage')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return response()->json($content);
    }

    /**
     * Get single content
     */
    public function show(int $id)
    {
        $content = $this->contentRepo->find($id);
        
        if (!$content || $content->user_id !== Auth::id()) {
            return response()->json(['error' => 'Content not found'], 404);
        }

        return response()->json($content->load('facebookPage'));
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
            'scheduled_at' => 'nullable|date|after:now',
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
     * Schedule content
     */
    public function schedule(Request $request, int $id)
    {
        $validated = $request->validate([
            'scheduled_at' => 'required|date|after:now',
        ]);

        $content = $this->contentService->scheduleContent($id, Auth::id(), $validated['scheduled_at']);

        return response()->json($content);
    }

    /**
     * Publish content immediately
     */
    public function publish(int $id)
    {
        $content = $this->contentService->publishContent($id, Auth::id());

        return response()->json($content);
    }
}
