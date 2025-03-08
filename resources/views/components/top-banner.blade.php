@props([
    'headings' => 'Unlock Your Career Potential',
    'subheadings' => 'Discover the perfect job opportunity for you.'
])

<section class="bg-blue-900 text-white py-6 text-center">
    <div class="container mx-auto">
        <h2 class="text-3xl font-semibold">
            {{$headings}}
        </h2>
        <p class="text-lg mt-2">
            {{$subheadings}}
        </p>
    </div>
</section>