<?php

namespace App\Http\Controllers;

use App\Models\ResearchRequest;
use Illuminate\Http\Request;

class MarketResearchWebController extends Controller
{
    /**
     * Display the market research dashboard
     */
    public function index()
    {
        return view('market-research.index');
    }

    /**
     * Display a specific report
     */
    public function show($id)
    {
        $researchRequest = ResearchRequest::findOrFail($id);

        // If research needs verification, redirect to verification page
        if ($researchRequest->needsVerification()) {
            return redirect()
                ->route('market-research.verify', ['id' => $id])
                ->with('info', 'Please verify the collected data before viewing the report.');
        }

        // If not completed yet, show status
        if (!$researchRequest->isVerified()) {
            return redirect()
                ->route('market-research.index')
                ->with('info', 'Research is still processing. Please wait...');
        }

        return view('market-research.report', [
            'requestId' => $id
        ]);
    }

    /**
     * Display all research requests
     */
    public function requests()
    {
        return view('market-research.requests');
    }
}
