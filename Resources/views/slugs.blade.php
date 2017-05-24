@if(isset($item))
    <div class="info">
        @if(is_array($item->getSlug()))
            <table class="slug-table">
                {{--@php(dd($calendar->getSlug()))--}}
                @foreach($item->getSlug() as $property => $slug)
                    <tr>
                        <td>{{ $property }}</td>
                        <td>{{ ($slug->slug) ?: 'N/A' }}</td>
                    </tr>
                @endforeach
            </table>
            <a href="#">manage slugs</a>
        @else
            {{ ($item->present()->slug) ?: 'N/A' }} <a href="#">{{ ( $item->present()->slug) ? 'edit':'add' }}</a>
        @endif
    </div>
    {{-- TODO: make link work once Slug management screens are available. --}}
@endif