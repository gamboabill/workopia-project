<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Models\Job;
use App\Mail\JobApplied;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class ApplicantController extends Controller
{
    // @desc    Store new job application
    // @route   POST /jobs/{job}/apply

    public function store(Request $request, Job $job): RedirectResponse
    {
        // Check if user is already applied
        $existingApplication = Applicant::where('job_id', $job->id)
            ->where('user_id', auth()->id())
            ->exists();

        if ($existingApplication) {
            return redirect()->back()->with('error', 'You have already applied to his job!');
        }

        // Validate incoming data
        $validatedData = $request->validate([
            'full_name' => 'required|string',
            'contact_phone' => 'string',
            'contact_email' => 'required|string|email',
            'message' => 'string',
            'location' => 'string',
            'resume' => 'required|file|mimes:pdf|max:2048'
        ]);

        // handle resume upload

        if ($request->hasFile('resume')) {
            $path = $request->file('resume')->store('resumes', 'public');

            $validatedData['resume_path'] = $path;
        }

        // Store the application
        $application = new Applicant($validatedData);

        $application->job_id = $job->id;
        $application->user_id = auth()->id();
        $application->save();

        // Send email to owner
        // Mail::to($job->user->email)->send(new JobApplied($application, $job));

        return redirect()->back()->with('success', 'Your application has been submitted!');
    }

    // @desc    Delete job applicants
    // @route   DELETE /applicant/{applicant}

    public function destroy($id): RedirectResponse
    {
        $applicant = Applicant::findOrFail($id);

        $resume = Applicant::select('resume_path')
            ->where('id', $id)
            ->first();

        Storage::disk('public')->delete($resume->resume_path);

        $applicant->delete();

        return redirect()->back()->with('success', 'Job applicant successfully deleted!');
    }

    public function withdraw_application($id)
    {
        $application = Applicant::find($id);

        $resume = Applicant::select('resume_path')
            ->where('id', $id)
            ->first();

        Storage::disk('public')->delete($resume->resume_path);

        $application->delete();

        return redirect()->back()->with('success', 'Application successfully withdrawn!');
    }
}
