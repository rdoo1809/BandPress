<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GitHubService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BuilderController
{
    public function __construct(GitHubService $gitHubService)
    {
    }

    public function stashLogo(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

        $user = auth()->user();
        $site = $user->website;

        $bandName = $user->name;
        $filename = Str::slug($bandName) . '_logo.' . $request->file('logo')->getClientOriginalExtension();
        $path = $request->file('logo')->storeAs('logos', $filename, 'public');

        $content = $site->siteContent()->firstOrCreate([]);
        $content->update([
            'logo' => $path,
        ]);

        return response()->json([
            'message' => 'Logo uploaded successfully.',
            'path' => asset("storage/{$path}"),
        ]);
    }
}
