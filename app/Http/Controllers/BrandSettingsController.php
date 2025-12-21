<?php

namespace App\Http\Controllers;

use App\Repositories\BrandSettingRepository;
use App\Repositories\AuditLogRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BrandSettingsController extends Controller
{
    public function __construct(
        protected BrandSettingRepository $brandRepo,
        protected AuditLogRepository $auditRepo
    ) {}

    /**
     * Show brand settings form
     */
    public function edit()
    {
        $settings = $this->brandRepo->getOrCreateForUser(Auth::id());
        
        return view('dashboard.settings', compact('settings'));
    }

    /**
     * Update brand settings
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'brand_name' => 'nullable|string|max:255',
            'industry' => 'nullable|string|max:255',
            'brand_tone' => 'required|in:professional,casual,friendly,authoritative',
            'voice_characteristics' => 'nullable|string|max:1000',
            'target_audience' => 'nullable|string|max:1000',
            'business_goals' => 'nullable|string|max:1000',
            'key_messages' => 'nullable|string|max:1000',
            'forbidden_words' => 'nullable|string|max:500',
            'primary_colors' => 'nullable|string|max:255',
            'secondary_color' => 'nullable|string|max:50',
            'font_family' => 'nullable|string|max:100',
            'visual_style' => 'nullable|string|max:255',
            'logo_in_images' => 'boolean',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:5120', // 5MB max
            'website_url' => 'nullable|url|max:255',
            'preferred_language' => 'required|in:en,ar,es,fr,de,it,pt,zh,ja,ko',
            'monthly_budget' => 'nullable|numeric|min:0|max:999999.99',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
            $validated['logo_path'] = $logoPath;
        }

        $settings = $this->brandRepo->updateForUser(Auth::id(), $validated);

        $this->auditRepo->log(
            'updated',
            'Brand settings updated',
            Auth::id(),
            'brand_settings',
            $settings->id,
            ['changes' => $validated]
        );

        return redirect()->route('dashboard.settings')
            ->with('success', 'Brand settings updated successfully!');
    }
}
