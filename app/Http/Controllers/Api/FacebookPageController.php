<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repositories\FacebookPageRepository;
use Illuminate\Support\Facades\Auth;

class FacebookPageController extends Controller
{
    public function __construct(
        protected FacebookPageRepository $pageRepo
    ) {}

    /**
     * Get all pages for authenticated user
     */
    public function index()
    {
        $pages = $this->pageRepo->getUserPages(Auth::id())
            ->orderBy('page_name')
            ->get();

        return response()->json($pages);
    }

    /**
     * Get single page
     */
    public function show(int $id)
    {
        $page = $this->pageRepo->find($id);
        
        if (!$page || $page->user_id !== Auth::id()) {
            return response()->json(['error' => 'Page not found'], 404);
        }

        return response()->json($page);
    }

    /**
     * Sync page data from Facebook
     */
    public function sync(int $id)
    {
        $page = $this->pageRepo->find($id);
        
        if (!$page || $page->user_id !== Auth::id()) {
            return response()->json(['error' => 'Page not found'], 404);
        }

        try {
            $fb = new \Facebook\Facebook([
                'app_id' => config('services.facebook.client_id'),
                'app_secret' => config('services.facebook.client_secret'),
                'default_graph_version' => 'v18.0',
            ]);

            // Fetch page info
            $response = $fb->get("/{$page->page_id}?fields=name,followers_count,category", $page->page_access_token);
            $pageData = $response->getDecodedBody();

            $page->update([
                'page_name' => $pageData['name'] ?? $page->page_name,
                'follower_count' => $pageData['followers_count'] ?? $page->follower_count,
                'page_category' => $pageData['category'] ?? $page->page_category,
            ]);

            return response()->json($page);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to sync page: ' . $e->getMessage()], 500);
        }
    }
}
