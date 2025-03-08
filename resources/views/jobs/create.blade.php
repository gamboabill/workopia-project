<x-layout>
    <x-slot name="title">Create Job</x-slot>
    <div class="bg-white mx-auto p-8 rounded-lg shadow-md w-full md:max-w-3xl">
        <h2 class="text-4xl text-center font-bold mb-4">
            Create Job Listing
        </h2>
        <form method="POST" action="{{url('/jobs')}}" enctype="multipart/form-data">
            @csrf
            <h2 class="text-2xl font-bold mb-6 text-center text-gray-500">
                Job Info
            </h2>

            <x-inputs.text id="title" name="title" label="Job Title" placeholder="Enter a job title"/>

            <x-inputs.textarea name="description" id="description" placeholder="Enter Job Description" label="Job Description" />

            <x-inputs.text id="salary" name="salary" label="Salary" type="number" placeholder="90000"/>

            <x-inputs.textarea name="requirements" id="requirements" placeholder="Enter Requirements" label="Requirements" />

            <x-inputs.textarea name="benefits" id="benefits" placeholder="Enter Benefits" label="Benefits" />

            <x-inputs.text id="tags" name="tags" label="Tags (comma-separated)" placeholder="development, coding, java, python"/>

            <x-inputs.select id="job_type" name="job_type" label="Job Type" value="{{old('job_type')}}" :options="['' => 'Select Job Type', 'Full-Time' => 'Full-Time', 'Part-Time' => 'Part-Time', 'Contract' => 'Contract', 'Temporary' => 'Temporary', 'Internship' => 'Internship', 'Volunteer' => 'Volunteer', 'On-Call' => 'On-Call']" />

            <x-inputs.select id="remote" name="remote" label="Remote" value="{{old('remote')}}" :options="[0 => 'No', 1 => 'Yes']" />

            <x-inputs.text id="address" name="address" label="Address" placeholder="123 Main St"/>

            <x-inputs.text id="city" name="city" label="City" placeholder="Albany"/>

            <x-inputs.text id="state" name="state" label="State" placeholder="NY"/>

            <x-inputs.text id="zipcode" name="zipcode" label="ZIP Code" placeholder="12201"/>

            <h2 class="text-2xl font-bold mb-6 text-center text-gray-500">
                Company Info
            </h2>

            <x-inputs.text id="company_name" name="company_name" label="Company Name" placeholder="Enter Company Name"/>

            <x-inputs.textarea name="company_description" id="company_description" placeholder="Enter Company Description"  label="Company Description"/>

            <x-inputs.text id="company_website" name="company_website" label="Company Website" placeholder="Enter Company Website" type="url"/>

            <x-inputs.text id="contact_phone" name="contact_phone" label="Contact Phone" placeholder="Enter Company Phone" label="Company Phone"/>

            <x-inputs.text id="contact_email" name="contact_email" label="Contact Email" type="email" placeholder="Enter Company Email"/>

            <x-inputs.file id="company_logo" name="company_logo" label="Company Logo"/>

            <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white px-4 py-2 my-3 rounded focus:outline-none">Save</button>
        </form>
    </div>
    {{-- @forelse($errors->all() as $whoops)
    {{ $whoops }}
    @empty
        NO ERRORS
    @endforelse --}}
</x-layout>