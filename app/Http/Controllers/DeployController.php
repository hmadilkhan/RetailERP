<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class DeployController extends Controller
{
    public function rebuild()
    {
        $response = Http::withHeaders([
            'Accept' => 'application/vnd.github+json',
            'Authorization' => 'Bearer ' . env('GITHUB_TOKEN'),
        ])->post("https://api.github.com/repos/" . env('GITHUB_OWNER') . "/" . env('GITHUB_REPO') . "/actions/workflows/deploy.yml/dispatches", [
            'ref' => 'main', // ya jo bhi branch hai
        ]);

        if ($response->successful()) {
            return back()->with('status', '✅ Deploy triggered successfully!');
        }

        return back()->with('error', '❌ Failed: ' . $response->body());
    }
}
