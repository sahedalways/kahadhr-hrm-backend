<x-layouts.app>
    <div class="container py-5">

        {{-- Header Section --}}
        <div class="text-center mb-5">
            <h1 class="display-4 fw-bold text-white">{{ $item->title }}</h1>

            <p class="lead text-light">
                {!! $item->description !!}
            </p>

            <div class="d-flex justify-content-center align-items-center gap-3 mt-3 flex-wrap">
                <span class="badge bg-secondary">
                    Created by: {{ $item->creator->full_name ?? 'Admin' }}
                </span>

                <span class="badge bg-info text-dark">
                    Created at: {{ $item->created_at->format('M d, Y') }}
                </span>
            </div>
        </div>

        {{-- Media Section --}}
        <div class="text-center mb-5">
            @php
                $media = strtolower($item->media ?? '');
                $isImage = Str::endsWith($media, ['.jpg', '.jpeg', '.png', '.gif', '.webp']);
                $isVideo = Str::endsWith($media, ['.mp4', '.mov', '.avi']);
                $isAudio = Str::endsWith($media, ['.mp3', '.wav']);
            @endphp

            @if ($item->media && $isImage)
                <img src="{{ asset('storage/' . $item->media) }}" class="img-fluid rounded shadow-lg border"
                    alt="media">
            @elseif ($item->media && $isVideo)
                <video class="w-100 rounded shadow-lg border" controls>
                    <source src="{{ asset('storage/' . $item->media) }}" type="video/mp4">
                </video>
            @elseif ($item->media && $isAudio)
                <audio controls class="w-100">
                    <source src="{{ asset('storage/' . $item->media) }}">
                </audio>
            @else
                <div class="media-placeholder p-5 border rounded bg-dark-subtle text-center">
                    <i class="bi bi-file-earmark-text fs-2 text-muted"></i>
                    <p class="small text-muted mt-2">No media uploaded</p>
                </div>
            @endif
        </div>

        {{-- Back Button --}}
        <div class="d-flex justify-content-start mt-4">
            <a href="{{ route('company.dashboard.onboarding.index', ['company' => app('authUser')->company->sub_domain]) }}"
                class="btn btn-outline-light btn-lg d-flex align-items-center gap-2">
                <i class="bi bi-arrow-left"></i> Back to All Onboardings
            </a>
        </div>

    </div>
</x-layouts.app>
