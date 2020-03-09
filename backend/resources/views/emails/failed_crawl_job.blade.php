@component('mail::message')
    # Failed Job

    Crawl url [{{ $url }}]({{ $url }}) failed by {{ $user['name'] }} ({{ $user['email'] }}).
@endcomponent
