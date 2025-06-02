<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GitHubService;
use Illuminate\Support\Facades\Auth;

class BuilderController
{
    public function __construct(GitHubService $gitHubService)
    {
    }

    public function stashLogo(Request $request): \Illuminate\Http\JsonResponse
    {


        return response()->json();
    }
}
