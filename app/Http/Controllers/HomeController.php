<?php

namespace App\Http\Controllers;

use App\Models\Cloudflare;
use Illuminate\Http\Request;
use App\Models\Job;
use Illuminate\View\View;

class HomeController extends Controller
{
    // @desc Show home index view
    // @route Get /
    public function index(): View
    {

        $cloudflare = Cloudflare::get()->first();

        $jobs = Job::latest()->limit(6)->get();
        return view('pages.index')->with('jobs', $jobs)->with('cloudflare', $cloudflare);
    }
}
