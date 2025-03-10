<h3>Cloudflare URL: @if ($url)
        {{ $url->url }}
        <a href="{{ route('delete_cloudflare', $url->id) }}"><button>delete</button></a>
    @else
    @endif

</h3>

<br><br>


@if ($url)
@else
    <form action="{{ url('/cloudflare') }}" method="POST">
        @csrf

        <input type="text" name="url" placeholder="input cloudflare url">

        <button type="submit">Submit</button>

    </form>
@endif
