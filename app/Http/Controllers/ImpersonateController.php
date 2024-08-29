<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonateController extends Controller
{
    public function impersonate($userId)
    {
        $user = User::findOrFail(auth()->user()->id);
        $userToImpersonate = User::findOrFail($userId);

        if ($user->canImpersonate() && $userToImpersonate->canBeImpersonated()) {
            Auth::user()->impersonate($userToImpersonate);
        }

        return redirect()->route('home'); // Change to the route you want to redirect to after impersonation
    }

    public function leave()
    {
        Auth::user()->leaveImpersonation();

        return redirect()->route('home'); // Change to the route you want to redirect to after leaving impersonation
    }
}
