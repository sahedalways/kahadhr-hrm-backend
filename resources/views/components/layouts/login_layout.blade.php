<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>@yield('title', siteSetting()->site_title)</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ siteSetting()->favicon_url ?? asset('favicon.ico') }}">

    {{-- Styles --}}
    <link id="pagestyle" href="{{ asset('assets/css/argon-dashboard.min28b5.css?v=2.0.0') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/js/plugins/toastr.min.css') }}" rel="stylesheet">
</head>

<body class="g-sidenav-show">
    @yield('content')

    {{-- Scripts --}}
    <script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/toastr.min.js') }}"></script>

    <script>
        document.addEventListener("livewire:init", () => {
            // Default Toastr options
            toastr.options = {
                closeButton: true,
                progressBar: true,
                timeOut: 5000,
                positionClass: "toast-top-right",
            };

            // Listen for Livewire toast event
            Livewire.on("toast", (event) => {
                if (event.notify && event.message) {
                    toastr[event.notify](event.message);
                } else {
                    console.warn("Toast event missing 'notify' or 'message'.", event);
                }
            });
        });
    </script>
</body>

</html>
