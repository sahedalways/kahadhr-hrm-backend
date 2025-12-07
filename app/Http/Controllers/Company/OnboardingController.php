<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    public function view($company, $id, Request $request)
    {
        $item = Announcement::findOrFail($id);
        return view('livewire.backend.company.onboarding.view', compact('item'));
    }
}
