@if (request()->get('breadcrumbs') && request()->get('breadcrumbs')->isNotEmpty())
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">@lang('home.index')</a></li>
            
            @foreach(request()->get('breadcrumbs') as $crumb)
                @if($crumb->link === 'home.index') @continue @endif

                @if($loop->last)
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ $crumb->translated_name }}
                    </li>
                @else
                    <li class="breadcrumb-item">
                        @if(!empty($crumb->link) && Route::has($crumb->link))
                            <a href="{{ route($crumb->link) }}">
                                {{ $crumb->translated_name }}
                            </a>
                        @else
                            <span class="text-muted">{{ $crumb->translated_name }}</span>
                        @endif
                    </li>
                @endif
            @endforeach
        </ol>
    </nav>
@endif