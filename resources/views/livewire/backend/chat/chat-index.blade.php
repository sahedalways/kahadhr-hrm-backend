@php
    use Illuminate\Support\Str;
    use Carbon\Carbon;
    use App\Models\Employee;
    $lastDate = null;
@endphp

<div class="container-fluid chat-app-container">
    <div class="row h-100">

        {{-- SIDEBAR --}}
        <div class="col-md-4 col-lg-3 d-flex flex-column p-0 sidebar">

            {{-- Add new --}}
            <div class="p-3 d-flex justify-content-between align-items-center border-bottom">
                <div class="dropdown">
                    <button class="btn btn-primary d-flex align-items-center dropdown-toggle" type="button"
                        id="addNewDropdown" data-bs-toggle="dropdown" style="border-radius: 20px; font-weight: 600;">
                        <i class="bi bi-plus me-1"></i> Add new
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="addNewDropdown">
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="#" data-bs-toggle="modal"
                                data-bs-target="#newChatModal" wire:click="$set('searchUser', '')">

                                <i class="bi bi-chat-left-text me-2"></i> New Chat
                            </a>
                        </li>
                        @if (auth()->user()->user_type === 'company')
                            <li>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <i class="bi bi-people-fill me-2"></i> New Team
                                </a>
                            </li>
                        @endif

                    </ul>
                </div>
            </div>

            {{-- Search --}}
            <div class="p-3 border-bottom">
                {{-- Search --}}

                <div class="input-group mb-2 position-relative">
                    <input type="text" class="form-control ps-5" placeholder="Search" wire:model="searchTerm"
                        wire:keyup="set('searchTerm', $event.target.value)"
                        style="border-radius: 25px; padding-right: 120px; border-color: #ddd;">


                </div>

                {{-- Tabs --}}
                <div class="d-flex" id="chat-filters">
                    <button wire:click="$set('tab', 'all')"
                        class="btn btn-sm me-2 fw-bold {{ $tab == 'all' ? 'btn-primary text-white' : 'btn-light text-muted' }}"
                        style="border-radius: 15px;">All</button>
                    <button wire:click="$set('tab', 'unread')"
                        class="btn btn-sm me-2 fw-bold {{ $tab == 'unread' ? 'btn-primary text-white' : 'btn-light text-muted' }}"
                        style="border-radius: 15px;">Unread</button>
                    <button wire:click="$set('tab', 'teams')"
                        class="btn btn-sm me-2 fw-bold {{ $tab == 'teams' ? 'btn-primary text-white' : 'btn-light text-muted' }}"
                        style="border-radius: 15px;">Teams</button>
                </div>
            </div>

            {{-- Sidebar User List --}}
            <div class="flex-grow-1 overflow-auto mt-2">
                @if ($tab == 'all')
                    {{-- Always show All users' team chat first --}}
                    <div class="chat-list-item {{ $receiverId === 'group' ? 'active-chat border-start border-3 border-primary' : '' }}"
                        wire:click="startNewChat('group')"
                        style="
        {{ $receiverId === 'group' ? 'background-color:#f0f0f0;' : '' }}
        {{ isset($unreadCounts['group']) && $unreadCounts['group'] > 0 ? 'background-color:#ffe5e5;' : '' }}
     ">

                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle p-2 me-3"
                                    style="width: 40px; height: 40px; display:flex; justify-content:center; align-items:center;">
                                    <img src="{{ asset('/assets/img/chat/group-icon.png') }}" alt="Group Icon"
                                        style="width:40px;height:40px;object-fit:cover;">
                                </div>
                                <div>
                                    <div class="fw-bold">All users' team chat</div>
                                    <small class="text-muted">
                                        {{ isset($lastMessages['group']) ? Str::limit($lastMessages['group'], 50) : 'Start a conversation' }}
                                    </small>

                                    @if (isset($lastMessages['group']) && $lastMessages['group'] && ($unreadCounts['group'] ?? 0) > 0)
                                        <span class="badge bg-danger">{{ $unreadCounts['group'] }}</span>
                                    @endif

                                </div>
                            </div>

                            {{-- Hook icon on right-bottom --}}
                            <div style="display:flex; align-items:flex-end;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    fill="currentColor" class="text-muted" viewBox="0 0 24 24">
                                    <path
                                        d="M13.9571 3.89646C13.3271 3.2665 12.25 3.71266 12.25 4.60357V5.68935L8.09484 9.84449L5.64655 10.6606C4.94132 10.8957 4.73002 11.7907 5.25567 12.3164L7.93932 15L4.46967 18.4697C4.17678 18.7626 4.17678 19.2374 4.46967 19.5303C4.76256 19.8232 5.23744 19.8232 5.53033 19.5303L8.99998 16.0607L11.7244 18.7851C12.207 19.2677 13.0207 19.1357 13.3259 18.5252L15.1164 14.9443L18.3106 11.75H19.3964C20.2873 11.75 20.7335 10.6729 20.1035 10.0429L13.9571 3.89646ZM13.75 5.89646V5.81067L18.1893 10.25H18.1035C17.8383 10.25 17.584 10.3554 17.3964 10.5429L13.9983 13.941C13.9223 14.017 13.8591 14.1048 13.811 14.2009L12.2945 17.2339L6.88839 11.8278L8.68115 11.2302C8.82843 11.1811 8.96226 11.0984 9.07203 10.9886L13.4571 6.60357C13.6446 6.41603 13.75 6.16168 13.75 5.89646Z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Employee list --}}
                    @if ($chatUsers->isEmpty())
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-chat-left-dots fs-3 d-block mb-2"></i>
                            No users found
                        </div>
                    @else
                        @foreach ($chatUsers as $user)
                            @php
                                if ($user->user_type == 'company') {
                                    $displayName = 'Company Admin';
                                    $avatar = $user->company->company_logo_url ?? asset('assets/img/default-image.jpg');
                                } else {
                                    $displayName = trim(($user->f_name ?? '') . ' ' . ($user->l_name ?? ''));
                                    $displayName = $displayName ?: $user->email;
                                    $avatar = $user->employee->avatar_url ?? asset('assets/img/default-image.jpg');
                                }

                            @endphp

                            <div class="chat-list-item {{ $receiverId == $user->id ? 'active-chat border-start border-3 border-primary' : '' }}"
                                wire:click="startNewChat({{ $user->id }})"
                                style="
        {{ $receiverId == $user->id ? 'background-color:#f0f0f0;' : '' }}
        {{ isset($unreadCounts[$user->id]) && $unreadCounts[$user->id] > 0 ? 'background-color:#ffe5e5;' : '' }}
     ">
                                <div class="d-flex align-items-center">
                                    <img src="{{ $avatar }}" class="rounded-circle me-3"
                                        style="width:40px;height:40px;object-fit:cover;">
                                    <div>
                                        <div class="fw-bold">{{ $displayName }}</div>
                                        <small class="text-muted">
                                            {{ isset($lastMessages[$user->id]) ? Str::limit($lastMessages[$user->id], 50) : 'Start a conversation' }}
                                        </small>

                                        @if (isset($unreadCounts[$user->id]) && $unreadCounts[$user->id] > 0)
                                            <span class="badge bg-danger ms-1">{{ $unreadCounts[$user->id] }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                @elseif($tab == 'teams')
                    <div class="chat-list-item {{ $receiverId === 'group' ? 'active-chat border-start border-3 border-primary' : '' }}"
                        wire:click="startNewChat('group')"
                        style="
        {{ $receiverId === 'group' ? 'background-color:#f0f0f0;' : '' }}
        {{ isset($unreadCounts['group']) && $unreadCounts['group'] > 0 ? 'background-color:#ffe5e5;' : '' }}
     ">

                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle p-2 me-3"
                                    style="width: 40px; height: 40px; display:flex; justify-content:center; align-items:center;">
                                    <img src="{{ asset('/assets/img/chat/group-icon.png') }}" alt="Group Icon"
                                        style="width:40px;height:40px;object-fit:cover;">
                                </div>
                                <div>
                                    <div class="fw-bold">All users' team chat</div>
                                    <small class="text-muted">
                                        {{ isset($lastMessages['group']) ? Str::limit($lastMessages['group'], 50) : 'Start a conversation' }}
                                    </small>

                                    @if (isset($lastMessages['group']) && $lastMessages['group'] && ($unreadCounts['group'] ?? 0) > 0)
                                        <span class="badge bg-danger">{{ $unreadCounts['group'] }}</span>
                                    @endif


                                </div>
                            </div>

                            {{-- Hook icon on right-bottom --}}
                            <div style="display:flex; align-items:flex-end;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    fill="currentColor" class="text-muted" viewBox="0 0 24 24">
                                    <path
                                        d="M13.9571 3.89646C13.3271 3.2665 12.25 3.71266 12.25 4.60357V5.68935L8.09484 9.84449L5.64655 10.6606C4.94132 10.8957 4.73002 11.7907 5.25567 12.3164L7.93932 15L4.46967 18.4697C4.17678 18.7626 4.17678 19.2374 4.46967 19.5303C4.76256 19.8232 5.23744 19.8232 5.53033 19.5303L8.99998 16.0607L11.7244 18.7851C12.207 19.2677 13.0207 19.1357 13.3259 18.5252L15.1164 14.9443L18.3106 11.75H19.3964C20.2873 11.75 20.7335 10.6729 20.1035 10.0429L13.9571 3.89646ZM13.75 5.89646V5.81067L18.1893 10.25H18.1035C17.8383 10.25 17.584 10.3554 17.3964 10.5429L13.9983 13.941C13.9223 14.017 13.8591 14.1048 13.811 14.2009L12.2945 17.2339L6.88839 11.8278L8.68115 11.2302C8.82843 11.1811 8.96226 11.0984 9.07203 10.9886L13.4571 6.60357C13.6446 6.41603 13.75 6.16168 13.75 5.89646Z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($tab == 'unread')
                    @php
                        $unreadUsers = $chatUsers->filter(function ($user) use ($unreadCounts) {
                            return isset($unreadCounts[$user->id]) && $unreadCounts[$user->id] > 0;
                        });
                    @endphp

                    @if ($unreadUsers->isEmpty())
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-chat-left-dots fs-3 d-block mb-2"></i>
                            No unread messages
                        </div>
                    @else
                        @foreach ($unreadUsers as $user)
                            @php
                                if ($user->user_type == 'company') {
                                    $displayName = 'Company Admin';
                                    $avatar = $user->company->company_logo_url ?? asset('assets/img/default-image.jpg');
                                } else {
                                    $displayName = trim(($user->f_name ?? '') . ' ' . ($user->l_name ?? ''));
                                    $displayName = $displayName ?: $user->email;
                                    $avatar = $user->employee->avatar_url ?? asset('assets/img/default-image.jpg');
                                }
                            @endphp

                            <div class="chat-list-item {{ $receiverId == $user->id ? 'active-chat border-start border-3 border-primary' : '' }}"
                                wire:click="startNewChat({{ $user->id }})" style="background-color:#ffe5e5;">
                                <div class="d-flex align-items-center">
                                    <img src="{{ $avatar }}" class="rounded-circle me-3"
                                        style="width:40px;height:40px;object-fit:cover;">
                                    <div>
                                        <div class="fw-bold">{{ $displayName }}</div>
                                        <small class="text-muted">
                                            {{ isset($lastMessages[$user->id]) ? Str::limit($lastMessages[$user->id], 50) : 'Start a conversation' }}
                                        </small>

                                        @if (isset($unreadCounts[$user->id]) && $unreadCounts[$user->id] > 0)
                                            <span class="badge bg-danger ms-1">{{ $unreadCounts[$user->id] }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                @endif


            </div>


        </div>

        {{-- MAIN CHAT AREA --}}
        <div class="col-md-8 col-lg-9 d-flex flex-column p-0">

            {{-- Chat Header --}}
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">


                    <div class="rounded-circle p-2 me-2"
                        style="width: 45px; height: 45px; background-color: #d1e7dd; display: flex; justify-content: center; align-items: center; overflow: hidden;">

                        @if ($receiverInfo && $receiverInfo['type'] === 'group')
                            <img src="{{ $receiverInfo['photo'] }}" alt="Group Icon"
                                style="width: 40px; height: 40px; object-fit: cover;">
                        @else
                            <img src="{{ $receiverInfo['photo'] }}" alt="User Icon"
                                style="width: 40px; height: 40px; object-fit: cover;">
                        @endif
                    </div>


                    <div>
                        <div class="fw-bold" style="font-size: 1rem;">
                            {{ $receiverInfo['name'] ?? 'Select a chat' }}
                        </div>

                    </div>
                </div>

                <div>
                    <i class="bi bi-search text-muted me-3" style="cursor: pointer;"></i>
                    <i class="bi bi-three-dots-vertical text-muted" style="cursor: pointer;"></i>
                </div>
            </div>


            <input type="hidden" id="pusher_key" value="{{ config('broadcasting.connections.pusher.key') }}">
            <input type="hidden" id="pusher_cluster"
                value="{{ config('broadcasting.connections.pusher.options.cluster') }}">
            <input type="hidden" id="current_company_id" value="{{ currentCompanyId() }}">
            <input type="hidden" id="current_user_id" value="{{ auth()->id() }}">
            <input type="hidden" id="currentReceiverId" value="{{ $receiverId }}">



            {{-- Messages --}}

            <div class="flex-grow-1 p-4 main-chat-area" style="overflow-y: auto;" id="chatScroll">
                @php $lastDate = null; @endphp
                @foreach ($messages as $msg)
                    @php
                        $messageDate = $msg->created_at->format('Y-m-d');

                        $today = Carbon::today()->format('Y-m-d');
                        $yesterday = Carbon::yesterday()->format('Y-m-d');

                        $showDateDivider = false;

                        if ($lastDate !== $messageDate) {
                            $lastDate = $messageDate;
                            $showDateDivider = true;

                            if ($messageDate == $today) {
                                $dateText = 'Today';
                            } elseif ($messageDate == $yesterday) {
                                $dateText = 'Yesterday';
                            } else {
                                $dateText = $msg->created_at->format('d M, Y');
                            }
                        }
                    @endphp

                    @if ($showDateDivider)
                        <p class="text-center text-muted my-3" style="font-size: 0.8rem;">{{ $dateText }}</p>
                    @endif


                    @php

                        $message = $msg->message;

                        // 1️⃣ Static Company Admin highlight
                        $message = preg_replace(
                            '/@Company Admin/u',
                            '<span style="background-color: rgb(9, 58, 219); color: white; font-weight: bold; padding: 2px 4px; border-radius: 3px;">@Company Admin</span>',
                            $message,
                        );

                        // 2️⃣ Fetch current company employees (without global scopes)
                        $companyId = currentCompanyId(); // assuming helper function exists
                        $employees = Employee::withoutGlobalScopes()->where('company_id', $companyId)->get();

                        // 3️⃣ Loop through employees and highlight mentions
                        foreach ($employees as $employee) {
                            $names = [];

                            // full name
                            $fullName = trim(($employee->f_name ?? '') . ' ' . ($employee->l_name ?? ''));
                            if (!empty($fullName)) {
                                $names[] = $fullName;
                            }

                            // email
                            if (!empty($employee->email)) {
                                $names[] = $employee->email;
                            }

                            foreach ($names as $name) {
                                // skip if Company Admin (already handled)
                                if (strtolower($name) === 'company admin') {
                                    continue;
                                }

                                $pattern = '/@' . preg_quote($name, '/') . '/u';
                                $replacement =
                                    '<span style="background-color: rgb(9, 58, 219); color: white; font-weight: bold; padding: 2px 4px; border-radius: 3px;">@' .
                                    $name .
                                    '</span>';

                                $message = preg_replace($pattern, $replacement, $message);
                            }
                        }
                    @endphp



                    <div
                        class="mb-3 d-flex {{ $msg->sender_id == auth()->id() ? 'justify-content-end' : 'justify-content-start' }}">
                        <div
                            class="d-flex flex-column align-items-{{ $msg->sender_id == auth()->id() ? 'end' : 'start' }}">

                            <div class="message-bubble {{ $msg->sender_id == auth()->id() ? 'outgoing-message' : 'incoming-message' }}"
                                style="border-radius: 12px; position: relative; padding-right: 1.5rem; width: 300px;">
                                {!! $message !!}



                                @if ($msg->attachment_url)
                                    @if ($msg->attachment_type === 'image' || $msg->attachment_type === 'gif')
                                        <img src="{{ $msg->attachment_url }}" style="max-width:200px;"
                                            class="rounded">
                                    @elseif($msg->attachment_type === 'video')
                                        <video controls style="max-width:200px;">
                                            <source src="{{ $msg->attachment_url }}">
                                        </video>
                                    @else
                                        <a href="{{ $msg->attachment_url }}"
                                            target="_blank">{{ basename($msg->media_path) }}</a>
                                    @endif
                                @endif

                                @if ($msg->sender_id == auth()->id())
                                    <i class="fas fa-check-double"
                                        style="position: absolute; right: 4px; bottom: 4px; font-size: 0.6rem; color: #9bbbd4;"></i>
                                @endif
                            </div>


                            <!-- Sender Info Below Bubble -->
                            <small class="text-muted mt-1" style="font-size: 0.7rem;">
                                {{ $msg->sender->user_type === 'company' ? 'Company Admin' : trim($msg->sender->f_name . ' ' . $msg->sender->l_name) }}
                                .
                                {{ $msg->created_at->format('H:i') }}
                            </small>

                        </div>
                    </div>
                @endforeach
            </div>


            {{-- <div id="typingIndicator" class="text-red mb-2" style="font-size:0.8rem; display:none;">
                <span id="typingUser"></span> is typing...
            </div> --}}

            <div class="text-center my-2" wire:loading.flex wire:target="sendMessage, loadMore">
                <span class="spinner-border spinner-border-sm me-2" role="status"></span> Loading...
            </div>




            {{-- Message Input --}}
            <div class="p-3 border-top" style="background: #fff;">
                <div class="input-group position-relative">

                    <div class="input-group mb-2 position-relative">
                        <input type="text" id="message_input" class="form-control ps-5"
                            placeholder="Write something..." wire:model.defer="messageText"
                            wire:keydown.enter="sendMessage" wire:loading.attr="readonly" wire:target="sendMessage"
                            style="border-radius: 25px; padding-right: 120px; border-color: #ddd;">

                        <span wire:loading wire:target="sendMessage"
                            class="position-absolute end-0 top-50 translate-middle-y me-2">
                            <span class="spinner-border spinner-border-sm"></span>
                        </span>

                        <span class="input-group-text bg-white border-0 position-absolute end-0"
                            style="z-index: 10; padding: 0.375rem 1rem;">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                    </div>

                    <!-- LEFT ACTIONS -->
                    <div class="d-flex position-absolute start-0 top-50 translate-middle-y ms-2 align-items-center">

                        <!-- Attachment Button -->
                        <div class="position-relative me-2">
                            <button type="button" class="btn btn-light rounded-circle p-2" id="attachmentBtn"
                                title="Attachment">
                                <i class="fas fa-paperclip"></i>
                            </button>

                            <!-- Attachment Popup -->
                            <!-- Attachment Popup -->
                            <div id="attachmentPopup" class="bg-white border rounded shadow-sm p-2 position-absolute"
                                style="bottom: 50px; left: 0; display: {{ $showAttachmentPopup ? 'block' : 'none' }}; width: 220px; z-index:1000;">
                                <div class="d-flex justify-content-between">
                                    <!-- Image -->
                                    <label class="btn btn-light d-flex flex-column align-items-center p-2 me-2">
                                        <i class="fas fa-image fa-lg mb-1"></i>
                                        <small>Image</small>
                                        <input type="file" wire:model="attachment" accept="image/*" hidden>
                                    </label>

                                    <!-- Video -->
                                    <label class="btn btn-light d-flex flex-column align-items-center p-2 me-2">
                                        <i class="fas fa-video fa-lg mb-1"></i>
                                        <small>Video</small>
                                        <input type="file" wire:model="attachment" accept="video/*" hidden>
                                    </label>

                                    <!-- GIF -->
                                    <label class="btn btn-light d-flex flex-column align-items-center p-2 me-2">
                                        <i class="fas fa-file-video fa-lg mb-1"></i>
                                        <small>GIF</small>
                                        <input type="file" wire:model="attachment" accept=".gif" hidden>
                                    </label>

                                    <!-- File -->
                                    <label class="btn btn-light d-flex flex-column align-items-center p-2">
                                        <i class="fas fa-file-alt fa-lg mb-1"></i>
                                        <small>File</small>
                                        <input type="file" wire:model="attachment" hidden accept=".pdf">
                                    </label>
                                </div>


                            </div>




                        </div>

                        <!-- Emoji Button -->
                        <button id="show_emoji_box" style="width:40px;height:40px;">😊</button>



                        <button type="button" class="btn btn-light rounded-circle p-2" wire:click="toggleMentionBox"
                            title="Mention">@</button>

                        <!-- Mention Dropdown -->
                        <div id="mentionWrapper" class="position-relative">
                            @if ($showMentionBox)
                                <div id="mentionBox" class="position-absolute bg-white border shadow-sm p-2"
                                    style="top:-150px; left:50px; z-index:1000; width:200px; max-height:200px; overflow-y:auto;">

                                    <input type="text" class="form-control mb-1" placeholder="Search..."
                                        wire:model="mentionSearch">

                                    @foreach ($mentionUsers as $user)
                                        @php
                                            $displayName =
                                                $user->user_type === 'company'
                                                    ? 'Company Admin'
                                                    : trim(($user->f_name ?? '') . ' ' . ($user->l_name ?? ''));
                                            $displayName = $displayName ?: $user->email;
                                        @endphp
                                        <div class="p-2 hover-bg-light cursor-pointer"
                                            wire:click="selectMention({{ $user->id }})">
                                            {{ $displayName }}
                                        </div>
                                    @endforeach

                                    @if (empty($mentionUsers))
                                        <div class="text-muted p-2">No users found</div>
                                    @endif
                                </div>
                            @endif
                        </div>


                    </div>

                    <!-- INPUT FIELD -->




                </div>
            </div>


        </div>

    </div>




    <div class="modal fade @if ($showAttachmentModal) show @endif" tabindex="-1"
        style="@if ($showAttachmentModal) display:block; @else display:none; @endif; background: rgba(0,0,0,0.5);">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Send Attachment</h5>
                    <button type="button" class="btn-close"
                        wire:click="$set('showAttachmentModal', false)"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <input type="text" id="message_input" class="form-control ps-5"
                            placeholder="Write something..." wire:model.defer="messageText"
                            wire:keydown.enter="sendAttachmentMessage" wire:loading.attr="readonly"
                            wire:target="sendAttachmentMessage"
                            style="border-radius: 25px; padding-right: 120px; border-color: #ddd;">
                    </div>
                    @if ($attachment)
                        @php
                            $extension = strtolower($attachment->getClientOriginalExtension());
                        @endphp
                        <div class="mt-2">
                            <strong>Attachment:</strong> {{ $attachment->getClientOriginalName() }}

                            @if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif']))
                                <div class="mt-2">
                                    <img src="{{ $attachment->temporaryUrl() }}" class="img-fluid rounded"
                                        style="max-width: 100%;">
                                </div>
                            @elseif (in_array($extension, ['mp4', 'mov', 'avi']))
                                <div class="mt-2">
                                    <video controls class="w-100" style="max-height:300px;">
                                        <source src="{{ $attachment->temporaryUrl() }}">
                                    </video>
                                </div>
                            @elseif (in_array($extension, ['pdf']))
                                @php
                                    $extension = strtolower($attachment->getClientOriginalExtension());

                                    $previewPath =
                                        'chat/attachments/tmp/' . uniqid() . '_' . $attachment->getClientOriginalName();
                                    \Storage::disk('public')->putFileAs(
                                        'chat/attachments/tmp',
                                        $attachment,
                                        basename($previewPath),
                                    );
                                    $previewUrl = asset('storage/' . $previewPath);
                                @endphp
                                <div class="mt-2">
                                    <iframe src="{{ $previewUrl }}" style="width:100%; height:400px;"
                                        frameborder="0"></iframe>
                                </div>
                            @else
                                <div class="mt-2 p-2 border rounded bg-light">
                                    <strong>File:</strong> {{ $attachment->getClientOriginalName() }}
                                    <br>
                                    <small>Preview not available. Will send as download.</small>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" wire:click="cancelAttachment" wire:loading.attr="disabled"
                        wire:target="sendAttachmentMessage">
                        Cancel
                    </button>


                    <button class="btn btn-primary" wire:click="sendAttachmentMessage" wire:loading.attr="disabled"
                        wire:target="sendAttachmentMessage">
                        <span wire:loading.remove wire:target="sendAttachmentMessage">Send</span>
                        <span wire:loading wire:target="sendAttachmentMessage">
                            <i class="fas fa-spinner fa-spin"></i> Sending...
                        </span>
                    </button>

                </div>

            </div>
        </div>
    </div>


    <!-- NEW CHAT MODAL -->
    <div class="modal fade" id="newChatModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
            <div class="modal-content" style="border-radius: 15px;" data-bs-backdrop="static"
                data-bs-keyboard="false">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Start New Chat</h5>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border:none;">
                        <i class="fas fa-times" style="color:black;"></i>
                    </button>
                </div>

                <div class="modal-body" style="max-height: 400px; overflow-y: auto;">

                    <!-- 🔍 SEARCH FIELD -->
                    <input type="text" class="form-control mb-3" placeholder="Search users..."
                        wire:model="searchUser" wire:keyup="set('searchUser', $event.target.value)">

                    <!-- USER LIST -->
                    @foreach ($newChatUsers as $user)
                        @php
                            if ($user->user_type === 'company') {
                                $displayName = 'Company Admin';
                            } else {
                                $displayName = trim(($user->f_name ?? '') . ' ' . ($user->l_name ?? ''));
                                if (!$displayName) {
                                    $displayName = $user->email;
                                }
                            }

                            // Avatar logic
                            if ($user->user_type === 'employee') {
                                $avatar =
                                    $user->employee && $user->employee->avatar_url
                                        ? asset($user->employee->avatar_url)
                                        : asset('/assets/img/default-avatar.png');
                            } elseif ($user->user_type === 'company') {
                                $avatar =
                                    $user->company && $user->company->company_logo_url
                                        ? asset($user->company->company_logo_url)
                                        : asset('/assets/img/default-image.jpg');
                            } else {
                                $avatar = asset('/assets/img/default-image.jpg');
                            }
                        @endphp


                        <div class="d-flex align-items-center p-2 hover-bg-light rounded mb-2" style="cursor:pointer;"
                            wire:click="startNewChat({{ $user->id }})" data-bs-dismiss="modal">

                            <img src="{{ $avatar }}" class="rounded-circle me-3"
                                style="width:45px;height:45px;object-fit:cover;">

                            <div>
                                <div class="fw-bold">{{ $displayName }}</div>

                            </div>
                        </div>
                    @endforeach

                    @if ($newChatUsers->isEmpty())
                        <p class="text-center text-muted mt-3">No users found.</p>
                    @endif

                </div>
            </div>
        </div>
    </div>



</div>


<script src="https://js.pusher.com/7.2/pusher.min.js"></script>




<script src="{{ asset('js/company/chat.js') }}"></script>

<script>
    Livewire.on("scrollToBottom", () => {
        const box = document.getElementById("chatScroll");
        setTimeout(() => (box.scrollTop = box.scrollHeight), 50);
    });



    document.addEventListener("insert-mention", (e) => {
        const input = document.getElementById("message_input");
        if (!input) return;

        const mentionHtml = e.detail?.[0]?.html || '';
        if (!mentionHtml) return;

        // Convert <span> mention HTML to plain text, e.g., "@sahed"
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = mentionHtml;
        const mentionText = tempDiv.innerText;

        // Insert at current cursor position
        const start = input.selectionStart;
        const end = input.selectionEnd;
        input.value = input.value.substring(0, start) + mentionText + input.value.substring(end);

        // Move cursor after inserted mention
        const cursorPos = start + mentionText.length;
        input.setSelectionRange(cursorPos, cursorPos);

        // Trigger Livewire update
        input.dispatchEvent(new Event('input', {
            bubbles: true
        }));
    });

    let chatBox = document.getElementById('chatScroll');
    chatBox.addEventListener('scroll', () => {
        if (chatBox.scrollTop === 0) {
            @this.loadMore();
        }
    });

    document.addEventListener('click', function(event) {
        const wrapper = document.getElementById('mentionWrapper');
        const box = document.getElementById('mentionBox');


        if (box && !wrapper.contains(event.target)) {
            @this.set('showMentionBox', false);
        }
    });
</script>
