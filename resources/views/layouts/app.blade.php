<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ config('app.name') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    @yield('css')

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    @include('layouts.topbar')

    <div class="container-fluid pt-5">
        @auth
            <div class="row min-vh-100 pt-2">
                @include('layouts.sidebar')

                <main class="col-md-9 ms-sm-auto col-lg-10 px-md py-3 overflow-auto">
                    <div class="container">
                        @include('layouts.breadcrumb')
                        @yield('content')
                        @include('layouts.footer')
                    </div>
                </main>
            </div>
        @else
            <div class="container pt-3">
                @yield('content')
                @include('layouts.footer')
            </div>
        @endauth
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const dropdownElement = document.getElementById('topbarProfileDropdown');
        dropdownElement.addEventListener('shown.bs.dropdown', function () {
            this.setAttribute('aria-expanded', 'false');
        });
    </script>
    @yield('javascript')
</body>

</html>
