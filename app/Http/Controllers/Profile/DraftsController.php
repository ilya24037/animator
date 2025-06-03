<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\Animator;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DraftsController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $pending = Animator::where('user_id', $userId)
                          ->where('status', 'pending')
                          ->orderBy('updated_at', 'desc')
                          ->get();

        $drafts = Animator::where('user_id', $userId)
                          ->where('status', 'draft')
                          ->orderBy('updated_at', 'desc')
                          ->get();

        $archive = Animator::where('user_id', $userId)
                          ->where('status', 'archive')
                          ->orderBy('updated_at', 'desc')
                          ->get();

        return Inertia::render('Personal/Items', [
            'pending' => $pending,
            'drafts'  => $drafts,
            'archive' => $archive,
        ]);
    }
}
