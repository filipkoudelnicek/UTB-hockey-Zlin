<?php

namespace App\Http\Controllers;

use App\Services\PageDataService;

class TeamController extends Controller
{
    public function __construct(private readonly PageDataService $pageData)
    {
    }

    public function showPlayer(string $playerSlug)
    {
        return view('pages.player', $this->pageData->forPlayer($playerSlug));
    }
}
