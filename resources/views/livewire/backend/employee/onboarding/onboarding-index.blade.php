<div>
    <input type="text" wire:model="search" wire:keyup="set('search', $event.target.value)"
        placeholder="Search by title ..." class="form-control mb-2" />

    <div class="list-group">
        @foreach ($announcements as $announcement)
            <div class="list-group-item">
                <h5>{{ $announcement->title }}</h5>
                <p>{!! $announcement->description !!}</p>

                @if ($announcement->media)
                    <div class="mt-2">
                        @php
                            $ext = pathinfo($announcement->media, PATHINFO_EXTENSION);
                        @endphp
                        @if (in_array($ext, ['mp4', 'mov', 'avi']))
                            <video width="250" controls>
                                <source src="{{ asset('storage/' . $announcement->media) }}"
                                    type="video/{{ $ext }}">
                            </video>
                        @elseif (in_array($ext, ['mp3', 'wav']))
                            <audio controls>
                                <source src="{{ asset('storage/' . $announcement->media) }}"
                                    type="audio/{{ $ext }}">
                            </audio>
                        @else
                            <img src="{{ asset('storage/' . $announcement->media) }}" alt="media" class="img-fluid">
                        @endif
                    </div>
                @endif


                <div class="mt-2 text-muted small">
                    <span>
                        Created by:
                        @if (isset($announcement->creator) && $announcement->creator->user_type === 'company')
                            Company Admin
                        @else
                            {{ $announcement->creator->full_name ?? 'Admin' }}
                        @endif
                    </span> |
                    <span>Created at: {{ $announcement->created_at->format('d M Y') }}</span>
                </div>

            </div>
        @endforeach
    </div>

    @if ($hasMore)
        <button wire:click="loadMore" class="btn btn-primary mt-2">Load More</button>
    @endif
</div>
