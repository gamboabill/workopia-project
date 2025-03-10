<?php

namespace App\Http\Controllers;

use App\Models\Cloudflare;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\Job;

class BookmarkController extends Controller
{
    // @desc Get all users bookmarks
    // @route GET /bookmarks

    public function index(): View
    {
        $cloudflare = Cloudflare::get()->first();

        $user = Auth::user();

        $bookmarks = $user->bookmarkedJobs()->orderBy('job_user_bookmarks.created_at', 'desc')->paginate(9);

        return view('jobs.bookmarked')->with('bookmarks', $bookmarks)->with('cloudflare', $cloudflare);
    }

    // @desc Create new bookmark job
    // @route Post /bookmarks/{job}

    public function store(Job $job): RedirectResponse
    {
        $user = Auth::user();

        // Check if the job is already bookmarked
        if ($user->bookmarkedJobs()->where('job_id', $job->id)->exists()) {
            return back()->with('error', 'Job is already bookmarked!');
        }

        // Create new bookmark
        $user->bookmarkedJobs()->attach($job->id);

        return back()->with('success', 'Job bookmarked successfully!');
    }

    // @desc Remove bookmarked job
    // @route DELETE /bookmarks/{job}

    public function destroy(Job $job): RedirectResponse
    {
        $user = Auth::user();

        // Check if the job is not bookmarked
        if (!$user->bookmarkedJobs()->where('job_id', $job->id)->exists()) {
            return back()->with('error', 'Job is not bookmarked');
        }

        // Remove new bookmark
        $user->bookmarkedJobs()->detach($job->id);

        return back()->with('success', 'Bookmark removed successfully!');
    }
}
