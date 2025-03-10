<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage; // use for deleting images in the storage folder
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Job;
use App\Models\Applicant;
use App\Models\Cloudflare;
use Illuminate\Support\Facades\Auth;

class JobController extends Controller
{
    use AuthorizesRequests;

    // @desc Show all job listings
    // @route Get /jobs
    public function index(): View
    {
        $cloudflare = Cloudflare::get()->first();
        $jobs = Job::latest()->paginate(9);
        return view('jobs.index')->with('jobs', $jobs)->with('cloudflare', $cloudflare);
    }

    // @desc Show create job form
    // @route Get /job/create
    public function create(): View
    {
        $user = Auth::user();
        return view('jobs.create')->with('user', $user);
    }

    // @desc Save job to database
    // @route POST /job
    public function store(Request $request): RedirectResponse
    {
        $user_id = Auth::user()->id;

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'salary' => 'required|integer',
            'tags' => 'nullable|string',
            'job_type' => 'required|string',
            'remote' => 'required|boolean',
            'requirements' => 'nullable|string',
            'benefits' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'zipcode' => 'required|string',
            'contact_email' => 'required|string',
            'contact_phone' => 'nullable|string',
            'company_name' => 'required|string',
            'company_description' => 'nullable|string',
            'company_logo' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
            'company_website' => 'nullable|url'
        ]);

        // user id
        $validatedData['user_id'] = $user_id;

        // Check for image
        if ($request->hasFile('company_logo')) {
            // Store the file and get path
            $path = $request->file('company_logo')->store('logos', 'public');

            // Add path to validated data

            $validatedData['company_logo'] = $path;
        }

        // Submit to database

        Job::create($validatedData);

        // Job::create([
        //     'title' => $validatedData['title'],
        //     'description' => $validatedData['description']
        // ]);

        return redirect()->route('jobs.index')->with('success', 'Job listing created successfully!');
    }

    // @desc Display a single job listing
    // @route Get /jobs/{$id}
    public function show(Job $job): View
    {
        $cloudflare = Cloudflare::get()->first();

        // this is to get the ID of the application incase user wants to withdraw her/his application

        $data['application'] = Applicant::select('id') // this is how to get data specifically 
            ->where('job_id', $job->id)
            ->where('user_id', auth()->id())
            ->first();

        // this is to create a condition in show view if the selected job is already applied

        $data['existingApplication'] = Applicant::where('job_id', $job->id)
            ->where('user_id', auth()->id())
            ->exists();

        return view('jobs.show')->with('job', $job)->with($data)->with('cloudflare', $cloudflare);
    }

    // @desc Show home edit job form
    // @route Get /job/{$id}/edit
    public function edit(Job $job)
    {
        // Check if user is authorized
        // $this->authorize('update', $job);

        if (Auth::user()->id != $job->user_id) {
            return redirect()->route('jobs.index')->with('error', 'This action is unauthorized!');
        }

        return view('jobs.edit')->with('job', $job);
    }

    // @desc Update job listing
    // @route PUT /jobs/{$id}
    public function update(Request $request, Job $job): RedirectResponse
    {
        if (Auth::user()->id != $job->user_id) {
            return redirect()->route('jobs.index')->with('error', 'This action is unauthorized!');
        }

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'salary' => 'required|integer',
            'tags' => 'nullable|string',
            'job_type' => 'required|string',
            'remote' => 'required|boolean',
            'requirements' => 'nullable|string',
            'benefits' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'zipcode' => 'required|string',
            'contact_email' => 'required|string',
            'contact_phone' => 'nullable|string',
            'company_name' => 'required|string',
            'company_description' => 'nullable|string',
            'company_logo' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
            'company_website' => 'nullable|url'
        ]);

        // Check for image
        if ($request->hasFile('company_logo')) {
            //Delete old logo
            if ($job->company_logo) {
                Storage::disk('public')->delete($job->company_logo);
            }

            // Store the file and get path
            $path = $request->file('company_logo')->store('logos', 'public');

            // Add path to validated data
            $validatedData['company_logo'] = $path;
        }

        // Submit to database
        $job->update($validatedData);

        return redirect()->route('jobs.index')->with('success', 'Job listing updated successfully!');
    }

    // @desc Delete a job listing
    // @route delete /jobs/{{$id}}
    public function destroy(Job $job): RedirectResponse
    {

        // Check if user is authorized
        $this->authorize('delete', $job);

        // If logo, then delete it
        if ($job->company_logo) {
            Storage::disk('public')->delete($job->company_logo);
        }

        // delete job listing
        $job->delete();

        // Check if request came from the dashboard
        if (request()->query('from') == 'dashboard') {
            return redirect()->route('dashboard')->with('success', 'Job listing deleted successfully!');
        }

        return redirect()->route('jobs.index')->with('success', 'Job listing deleted successfully!');
    }

    // @desc    Search job listings
    // @route   GET /jobs/search
    public function search(Request $request): View
    {
        $keywords = strtolower($request->input('keywords'));
        $location = strtolower($request->input('location'));

        $query = Job::query();

        if ($keywords) {
            $query->where(function ($q) use ($keywords) {
                $q->whereRaw('LOWER(title) like ?', ['%' . $keywords . '%'])
                    ->orWhereRaw('LOWER(description) like ?', ['%' . $keywords . '%'])
                    ->orWhereRaw('LOWER(tags) like ?', ['%' . $keywords . '%']);
            });
        }

        if ($location) {
            $query->where(function ($q) use ($location) {
                $q->whereRaw('LOWER(address) like ?', ['%' . $location . '%'])
                    ->orWhereRaw('LOWER(city) like ?', ['%' . $location . '%'])
                    ->orWhereRaw('LOWER(state) like ?', ['%' . $location . '%'])
                    ->orWhereRaw('LOWER(zipcode) like ?', ['%' . $location . '%']);
            });
        }

        $jobs = $query->paginate(12);

        return view('jobs.index')->with('jobs', $jobs);
    }
}
