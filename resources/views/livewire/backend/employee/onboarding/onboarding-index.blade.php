<div>
    <input type="text" wire:model="search" wire:keyup="set('search', $event.target.value)"
        placeholder="Search by title ..." class="form-control mb-2" />

    <div class="list-group">
        @forelse ($announcements as $announcement)
            <div class="list-group-item text-center">
                <h5 class="text-center">{{ $announcement->title }}</h5>
                <p class="text-center">{!! $announcement->description !!}</p>

                @if ($announcement->media)
                    <div class="mt-2 text-center">
                        @php
                            $ext = pathinfo($announcement->media, PATHINFO_EXTENSION);
                        @endphp

                        @if (in_array($ext, ['mp4', 'mov', 'avi']))
                            <video controls
                                style="max-width:300px; max-height:200px; width:100%; height:auto; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.15);">
                                <source src="{{ asset('storage/' . $announcement->media) }}"
                                    type="video/{{ $ext }}">
                            </video>
                        @elseif (in_array($ext, ['mp3', 'wav']))
                            <audio controls style="width:100%; max-width:300px;">
                                <source src="{{ asset('storage/' . $announcement->media) }}"
                                    type="audio/{{ $ext }}">
                            </audio>
                        @else
                            <img src="{{ asset('storage/' . $announcement->media) }}" alt="media"
                                style="max-width:300px; max-height:200px; width:100%; object-fit:cover; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.15);"
                                class="clickable-image cursor-pointer"
                                data-src="{{ asset('storage/' . $announcement->media) ?? asset('assets/default-user.jpg') }}">
                        @endif
                    </div>
                @endif

                <div class="mt-2 text-muted small text-center mt-4">
                    <span>
                        Created by:
                        @if (isset($announcement->creator) && $announcement->creator->user_type === 'company')
                            {{ $announcement->creator->full_name }}
                        @else
                            {{ $announcement->creator->full_name ?? 'Admin' }}
                        @endif
                    </span> |
                    <span>Created at: {{ $announcement->created_at->format('d M Y') }}</span>
                </div>
            </div>
        @empty
            <div class="list-group-item text-center text-muted">
                No announcements found.
            </div>
        @endforelse
    </div>

    @if ($hasMore)
        <button wire:click="loadMore" class="btn btn-primary mt-2">Load More</button>
    @endif
</div>
