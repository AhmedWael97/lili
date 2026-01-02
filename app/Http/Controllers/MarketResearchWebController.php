<?php

namespace App\Http\Controllers;

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
