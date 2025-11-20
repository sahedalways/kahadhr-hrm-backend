@php
    $user = app('authUser');
@endphp

<div>
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl z-index-sticky" id="navbarBlur"
        data-scroll="false">
        <div class="container-fluid py-1 px-3 position-relative">

            <div class="sidenav-toggler sidenav-toggler-inner d-xl-block d-none">
                <a href="javascript:;" class="nav-link p-0">
                    <div class="sidenav-toggler-inner">
                        <i class="sidenav-toggler-line bg-white"></i>
                        <i class="sidenav-toggler-line bg-white"></i>
                        <i class="sidenav-toggler-line bg-white"></i>
                    </div>
                </a>
            </div>


            <div class="position-absolute start-50 translate-middle-x d-flex align-items-center">
                {{-- <img src="{{ $user->contact_avatar }}" alt="Avatar" class="rounded-circle" width="40"
                    height="40"> --}}
                <span class="ms-2 fw-bold text-white">{{ $user->full_name }}</span>
            </div>

            <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
                <ul class="ms-auto navbar-nav justify-content-end">


                    <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                        <a href="javascript:;" class="nav-link text-white p-0" id="iconNavbarSidenav">
                            <div class="sidenav-toggler-inner">
                                <i class="sidenav-toggler-line bg-white"></i>
                                <i class="sidenav-toggler-line bg-white"></i>
                                <i class="sidenav-toggler-line bg-white"></i>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>

        </div>
    </nav>
</div>
