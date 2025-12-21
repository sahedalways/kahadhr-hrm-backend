<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <title>@yield('title', siteSetting()->site_title)</title>

    <link rel="icon" type="image/png" href="{{ siteSetting()->favicon_url }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Permissions-Policy" content="fullscreen=(self)">

    <link href="{{ asset('assets/css/poppinsfont.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/nucleo-icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendors/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
    <link id="pagestyle" href="{{ asset('assets/css/argon-dashboard.min28b5.css?v=2.0.0') }}" rel="stylesheet" />
    <link id="pagestyle" href="{{ asset('assets/css/newdashboard.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/js/plugins/toastr.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('assets/js/plugins/chartjs.min.js') }}"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet" />

    <link href="{{ asset('assets/css/clockInOut.css') }}" rel="stylesheet" />

    @vite('resources/js/app.js')

    @stack('styles')

    @livewireStyles

    @livewireScripts
</head>

<body class="g-sidenav-show">


    <div id="preloader" class="preloader">
        <div class="hr-line-loader">
            <div class="line"></div>
            <div class="line"></div>
            <div class="line"></div>
        </div>
    </div>


    <div class=" bg-white position-absolute w-100">

    </div>
    <div class="fixed-sidebar">
        @livewire('backend.components.side-bar')
    </div>

    @php
        $company = auth()->check() ? auth()->user()->company : null;
        $showBanner = $company && in_array($company->subscription_status, ['trial', 'suspended']);
        $bannerText =
            $company && $company->subscription_status === 'trial'
                ? 'You are currently on a trial plan.'
                : ($company && $company->subscription_status === 'suspended'
                    ? 'Your account is suspended. Contact to support'
                    : '');
    @endphp

    @if ($showBanner)
        <div class="status-banner text-center text-white py-1 mx-auto"
            style="max-width: 600px; background-color: rgba(243, 156, 18, 0.15); 
                position: fixed; top: 10px; left: 0; right: 0; z-index:1050; 
                font-weight:500; font-size:0.85rem; border-radius:5px;">
            {{ $bannerText }}
        </div>
    @endif



    <main class="main-content position-relative border-radius-lg content-wrapper"
        @if ($showBanner) style="padding-top: 50px;" @else style="padding-top: 25px;" @endif>
        <div class="fixed-header border-bottom">
            @livewire('backend.components.header')
        </div>

        <div class="container-fluid pb-3 h-100">
            {{ $slot }}
        </div>
    </main>


    {{-- for showing image --}}
    <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0">
                <div class="modal-body p-0">
                    <img id="modalImage" src="" class="img-fluid w-100" alt="Preview">
                </div>
            </div>
        </div>
    </div>






    <script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/toastr.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/dragula/dragula.min.js') }}"></script>
    <script src="{{ asset('assets/js/argon-dashboard.min.js') }}"></script>



    <script>
        "use strict";
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = {
                damping: '0.5'
            }
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }
    </script>


    <script>
        "use strict"
        Livewire.on('closemodal', () => {
            $('.modal').modal('hide');
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
            $('body').removeAttr('style');
        })
    </script>


    <script>
        "use strict";
        Livewire.on('reloadpage', () => {
            window.location.reload();
        })
    </script>




    <script>
        document.addEventListener("livewire:init", () => {
            Livewire.on("toast", (event) => {
                if (event.notify && event.message) {
                    toastr[event.notify](event.message);
                } else {
                    console.warn("Toast event missing 'notify' or 'message'.", event);
                }
            });


            toastr.options = {
                closeButton: true,
                progressBar: true,
                timeOut: 5000,
                positionClass: "toast-top-center",
            };
        });
    </script>


    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const loader = document.getElementById("preloader");


            window.onload = () => {
                loader.classList.add("hidden");
            };
        });

        window.addEventListener("beforeunload", function() {
            document.getElementById("preloader").classList.remove("hidden");
        });
    </script>



    <script>
        "use strict";
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = {
                damping: '0.5'
            }
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }
    </script>

    <script>
        window.addEventListener('reload-page', event => {

            setTimeout(() => {
                location.reload();
            }, 2000);
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const images = document.querySelectorAll('.clickable-image');
            const modal = new bootstrap.Modal(document.getElementById('imagePreviewModal'));
            const modalImage = document.getElementById('modalImage');

            images.forEach(img => {
                img.addEventListener('click', function() {
                    const src = img.getAttribute('data-src');
                    modalImage.src = src;
                    modal.show();
                });
            });
        });
    </script>



    @php
        $todaysShift = todaysShiftForUser();
    @endphp

    @if (auth()->check() && auth()->user()->user_type === 'employee' && $todaysShift)
        <livewire:backend.employee.clock-modal.clock-modal />
    @endif


    @stack('js')


</body>

</html>
