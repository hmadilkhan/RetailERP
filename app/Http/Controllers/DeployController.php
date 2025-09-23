<?php

namespace App\Http\Controllers;

use App\WebsiteDetail;
use Illuminate\Support\Facades\Http;

class DeployController extends Controller
{
    public function rebuild($websiteId)
    {
        $website = WebsiteDetail::find($websiteId);
        if (!$website) {
            return back()->with('error', '❌ Website not found.');
        }
        if (empty($website->github_token) || empty($website->github_owner) || empty($website->github_repo)) {
            return back()->with('error', '❌ GitHub configuration is missing for this website.');
        }
        
        $bearingToken = 'Bearer ' . $website->github_token;
        $url = "https://api.github.com/repos/" . $website->github_owner . "/" . $website->github_repo . "/actions/workflows/deploy.yml/dispatches";
        
        $response = Http::withHeaders([
            'Accept' => 'application/vnd.github+json',
            'Authorization' => $bearingToken,
        ])->post($url, [
            'ref' => "main", // ya jo bhi branch hai
        ]);
       
        if ($response->successful()) {
            return back()->with('status', '✅ Deploy triggered successfully!');
        }

        return back()->with('error', '❌ Failed: ' . $response->body());
    }
}
