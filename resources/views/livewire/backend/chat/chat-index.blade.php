@php
    use Illuminate\Support\Str;
    use Carbon\Carbon;
    use App\Models\Employee;
    $lastDate = null;
@endphp

<div class="d-flex h-100">
    {{-- will work till tablate --}}
    <div class="chat-app-container d-md-block d-none">
        <div class="d-flex h-100">

            {{-- SIDEBAR --}}
            <div class="d-flex flex-column p-0 sidebar">

                {{-- Add new --}}
                <div style="min-height: 77px" class="p-3 d-flex justify-content-between align-items-center border-bottom">
                    <div class="dropdown" wire:ignore>
                        <button class="mb-0 btn btn-primary gap-1 d-flex align-items-center dropdown-toggle"
                            type="button" id="addNewDropdown" data-bs-toggle="dropdown"
                            style="border-radius: 20px; font-weight: 600;">
                            <i class="fa-solid fa-plus"></i> Add new
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
                                    <a class="dropdown-item d-flex align-items-center" href="#"
                                        data-bs-toggle="modal" data-bs-target="#newTeamModal"
                                        wire:click="openNewTeamModal">

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
                        <input type="text" class="form-control" placeholder="Search" wire:model="searchTerm"
                            wire:keyup="set('searchTerm', $event.target.value)">
                    </div>

                    {{-- Tabs --}}
                    <div class="d-flex flex-wrap mt-3" id="chat-filters">
                        <button wire:click="$set('tab', 'all')"
                            class="btn px-3 btn-sm me-2 fw-bold {{ $tab == 'all' ? 'btn-primary text-white' : 'btn-light text-muted' }}"
                            style="border-radius: 15px;">All</button>
                        <button wire:click="$set('tab', 'unread')"
                            class="btn px-3 btn-sm me-2 fw-bold {{ $tab == 'unread' ? 'btn-primary text-white' : 'btn-light text-muted' }}"
                            style="border-radius: 15px;">Unread</button>
                        <button wire:click="$set('tab', 'teams')"
                            class="btn px-3 btn-sm me-2 fw-bold {{ $tab == 'teams' ? 'btn-primary text-white' : 'btn-light text-muted' }}"
                            style="border-radius: 15px;">Teams</button>
                    </div>
                </div>

                {{-- Sidebar User List --}}
                <div class="flex-grow-1 overflow-auto mt-2 pe-3 ps-1">
                    @if ($tab == 'all')
                        {{-- Always show All users' team chat first --}}
                        <div class="chat-list-item {{ $receiverId === 'group' ? 'active-chat border-start border-3 border-primary' : '' }}"
                            wire:click="startNewChat('group')"
                            style="
        {{ $receiverId === 'group' ? 'background-color:#f0f0f0;' : '' }}
        {{ isset($unreadCounts['group']) && $unreadCounts['group'] > 0 ? 'background-color:#ffe5e5;' : '' }}
     ">

                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center flex-wrap">
                                    <div class="rounded-circle p-2 me-3"
                                        style="width: 40px; height: 40px; display:flex; justify-content:center; align-items:center;">
                                        <img src="{{ asset('/assets/img/chat/group-icon.png') }}" alt="Group Icon"
                                            style="width:40px;height:40px;object-fit:cover;">
                                    </div>
                                    <div>
                                        <div class="fw-bold">All users' team chat</div>
                                        <small class="text-muted text-break">
                                            {{ isset($lastMessages['group']) ? Str::limit($lastMessages['group'], 50) : 'Start a conversation' }}
                                        </small>


                                        <div class="text-muted small">

                                            {{ isset($lastMessageTimes['group']) ? $lastMessageTimes['group']->format('d M, h:i A') : '' }}
                                        </div>

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


                        @foreach ($teamGroups as $group)
                            <div x-data="{ showMenu: false }" @contextmenu.prevent="showMenu = true"
                                @click.away="showMenu = false"
                                class="chat-list-item position-relative {{ $receiverId === 'teamGroup_' . $group->id ? 'active-chat border-start border-3 border-primary' : '' }}"
                                wire:click="startNewChat('teamGroup_{{ $group->id }}')"
                                style="
            {{ $receiverId === 'teamGroup_' . $group->id ? 'background-color:#f0f0f0;' : '' }}
            {{ isset($unreadCounts['teamGroup_' . $group->id]) && $unreadCounts['teamGroup_' . $group->id] > 0 ? 'background-color:#ffe5e5;' : '' }}
         ">

                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center flex-wrap">
                                        <div class="rounded-circle p-2 me-3"
                                            style="width: 40px; height: 40px; display:flex; justify-content:center; align-items:center;">
                                            <img src="{{ $group->image ? asset($group->image_url) : asset('/assets/img/chat/group-icon.png') }}"
                                                alt="{{ $group->name }}"
                                                style="width:40px;height:40px;object-fit:cover;">
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $group->name }}</div>
                                            <small class="text-muted">
                                                {{ isset($lastMessages['teamGroup_' . $group->id]) ? Str::limit($lastMessages['teamGroup_' . $group->id], 50) : 'Start a conversation' }}
                                            </small>
                                            <div class="text-muted small">
                                                {{ isset($lastMessageTimes['teamGroup_' . $group->id]) ? $lastMessageTimes['teamGroup_' . $group->id]->format('d M, h:i A') : '' }}
                                            </div>
                                            @if (isset($unreadCounts['teamGroup_' . $group->id]) && $unreadCounts['teamGroup_' . $group->id] > 0)
                                                <span
                                                    class="badge bg-danger">{{ $unreadCounts['teamGroup_' . $group->id] }}</span>
                                            @endif
                                        </div>
                                    </div>

                                </div>



                                <div style="display:flex; align-items:flex-end;">
                                    <div class="dropdown" wire:ignore>
                                        <button class="btn btn-sm btn-light border-0 px-md-3 px-2" type="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v text-muted"></i>
                                        </button>

                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                            <li>
                                                <button type="button"
                                                    class="dropdown-item text-danger d-flex align-items-center"
                                                    wire:click.stop="deleteConversation('teamGroup_{{ $group->id }}')"
                                                    wire:loading.attr="disabled"
                                                    wire:target="deleteConversation('teamGroup_{{ $group->id }}')">

                                                    <!-- Spinner while deleting -->
                                                    <span wire:loading
                                                        wire:target="deleteConversation('teamGroup_{{ $group->id }}')"
                                                        class="spinner-border spinner-border-sm me-2"></span>

                                                    <span wire:loading.remove
                                                        wire:target="deleteConversation('teamGroup_{{ $group->id }}')">
                                                        <i class="bi bi-trash me-1"></i> Delete Conversation
                                                    </span>

                                                    <!-- Optional: text change during loading -->
                                                    <span wire:loading
                                                        wire:target="deleteConversation('teamGroup_{{ $group->id }}')">
                                                        Deleting...
                                                    </span>

                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>


                            </div>
                        @endforeach


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
                                $itemKey = $user->id;
                            @endphp

                            <div x-data="{ showMenu: false }" @click.away="showMenu = false"
                                @contextmenu.prevent="showMenu = true"
                                class="chat-list-item {{ $receiverId == $itemKey ? 'active-chat border-start border-3 border-primary' : '' }}"
                                wire:click="startNewChat({{ $itemKey }})"
                                style="
            {{ $receiverId == $itemKey ? 'background-color:#f0f0f0;' : '' }}
            {{ isset($unreadCounts[$itemKey]) && $unreadCounts[$itemKey] > 0 ? 'background-color:#ffe5e5;' : '' }}
        ">

                                <div class="d-flex align-items-center">
                                    <img src="{{ $avatar }}" class="rounded-circle me-3"
                                        style="width:40px;height:40px;object-fit:cover;">
                                    <div>
                                        <div class="fw-bold">{{ $displayName }}</div>

                                        <small class="text-muted">
                                            @if (isset($lastMessages[$itemKey]) && $lastMessages[$itemKey])
                                                {{ Str::limit($lastMessages[$itemKey], 50) }}
                                            @elseif(isset($lastAttachments[$itemKey]) && $lastAttachments[$itemKey])
                                                Sent an attachment
                                            @else
                                                Start a conversation
                                            @endif
                                        </small>

                                        @if (isset($unreadCounts[$itemKey]) && $unreadCounts[$itemKey] > 0)
                                            <span class="badge bg-danger ms-1">{{ $unreadCounts[$itemKey] }}</span>
                                        @endif

                                        <div class="text-muted small">
                                            {{ isset($lastMessageTimes[$itemKey]) ? $lastMessageTimes[$itemKey]->format('d M, h:i A') : '' }}
                                        </div>
                                    </div>
                                </div>

                                <!-- RIGHT CLICK MENU -->
                                <div x-show="showMenu" class="position-absolute bg-white shadow p-2 rounded"
                                    style="top:10px; right:10px; width:150px; z-index:1000;">
                                    <button class="dropdown-item text-danger"
                                        wire:click.stop="deleteConversation('{{ $itemKey }}')">
                                        <i class="bi bi-trash"></i> Delete Conversation
                                    </button>
                                </div>

                            </div>
                        @endforeach
                    @elseif($tab == 'teams')
                        <div class="chat-list-item {{ $receiverId === 'group' ? 'active-chat border-start border-3 border-primary' : '' }}"
                            wire:click="startNewChat('group')"
                            style="
        {{ $receiverId === 'group' ? 'background-color:#f0f0f0;' : '' }}
        {{ isset($unreadCounts['group']) && $unreadCounts['group'] > 0 ? 'background-color:#ffe5e5;' : '' }}
     ">

                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center flex-wrap">
                                    <div class="rounded-circle p-2 me-3"
                                        style="width: 40px; height: 40px; display:flex; justify-content:center; align-items:center;">
                                        <img src="{{ asset('/assets/img/chat/group-icon.png') }}" alt="Group Icon"
                                            style="width:40px;height:40px;object-fit:cover;">
                                    </div>
                                    <div>
                                        <div class="fw-bold">All users' team chat</div>



                                        <small class="text-muted">
                                            @if (isset($lastMessages['group']) && $lastMessages['group'])
                                                {{ Str::limit($lastMessages['group'], 50) }}
                                            @elseif(isset($lastAttachments['group']) && $lastAttachments['group'])
                                                Sent an attachment
                                            @else
                                                Start a conversation
                                            @endif
                                        </small>


                                        <div class="text-muted small">

                                            {{ isset($lastMessageTimes['group']) ? $lastMessageTimes['group']->format('d M, h:i A') : '' }}
                                        </div>

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


                        @foreach ($teamGroups as $group)
                            <div x-data="{ showMenu: false }" @contextmenu.prevent="showMenu = true"
                                @click.away="showMenu = false"
                                class="chat-list-item position-relative {{ $receiverId === 'teamGroup_' . $group->id ? 'active-chat border-start border-3 border-primary' : '' }}"
                                wire:click="startNewChat('teamGroup_{{ $group->id }}')"
                                style="
            {{ $receiverId === 'teamGroup_' . $group->id ? 'background-color:#f0f0f0;' : '' }}
            {{ isset($unreadCounts['teamGroup_' . $group->id]) && $unreadCounts['teamGroup_' . $group->id] > 0 ? 'background-color:#ffe5e5;' : '' }}
         ">

                                <div class="d-flex align-items-center justify-content-between">

                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle p-2 me-3 flex-wrap"
                                            style="width: 40px; height: 40px; display:flex; justify-content:center; align-items:center;">
                                            <img src="{{ $group->image ? asset($group->image_url) : asset('/assets/img/chat/group-icon.png') }}"
                                                alt="{{ $group->name }}"
                                                style="width:40px;height:40px;object-fit:cover;">
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $group->name }}</div>
                                            <small class="text-muted">
                                                {{ isset($lastMessages['teamGroup_' . $group->id]) ? Str::limit($lastMessages['teamGroup_' . $group->id], 50) : 'Start a conversation' }}
                                            </small>
                                            <div class="text-muted small">
                                                {{ isset($lastMessageTimes['teamGroup_' . $group->id]) ? $lastMessageTimes['teamGroup_' . $group->id]->format('d M, h:i A') : '' }}
                                            </div>
                                            @if (isset($unreadCounts['teamGroup_' . $group->id]) && $unreadCounts['teamGroup_' . $group->id] > 0)
                                                <span
                                                    class="badge bg-danger">{{ $unreadCounts['teamGroup_' . $group->id] }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div style="display:flex; align-items:flex-end;">
                                    <div class="dropdown" wire:ignore>
                                        <button class="btn btn-sm btn-light border-0 px-md-3 px-2" type="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v text-muted"></i>
                                        </button>

                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                            <li>
                                                <button type="button"
                                                    class="dropdown-item text-danger d-flex align-items-center"
                                                    wire:click.stop="deleteConversation('teamGroup_{{ $group->id }}')"
                                                    wire:loading.attr="disabled"
                                                    wire:target="deleteConversation('teamGroup_{{ $group->id }}')">

                                                    <!-- Spinner while deleting -->
                                                    <span wire:loading
                                                        wire:target="deleteConversation('teamGroup_{{ $group->id }}')"
                                                        class="spinner-border spinner-border-sm me-2"></span>

                                                    <span wire:loading.remove
                                                        wire:target="deleteConversation('teamGroup_{{ $group->id }}')">
                                                        <i class="bi bi-trash me-1"></i> Delete Conversation
                                                    </span>

                                                    <!-- Optional: text change during loading -->
                                                    <span wire:loading
                                                        wire:target="deleteConversation('teamGroup_{{ $group->id }}')">
                                                        Deleting...
                                                    </span>

                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>


                            </div>
                        @endforeach
                    @endif

                    @if ($tab == 'unread')
                        @php
                            // Personal unread users
                            $unreadUsers = $chatUsers->filter(
                                fn($user) => isset($unreadCounts[$user->id]) && $unreadCounts[$user->id] > 0,
                            );

                            // Group unread
                            $unreadGroups = collect();
                            if (isset($unreadCounts['group']) && $unreadCounts['group'] > 0) {
                                $unreadGroups->push(
                                    (object) [
                                        'id' => 'group',
                                        'name' => "All users' team chat",
                                        'avatar' => asset('/assets/img/chat/group-icon.png'),
                                    ],
                                );
                            }

                            // TeamGroup unread
                            $teamGroupsUnread = collect();
                            foreach ($teamGroups as $group) {
                                $key = 'teamGroup_' . $group->id;
                                if (isset($unreadCounts[$key]) && $unreadCounts[$key] > 0) {
                                    $teamGroupsUnread->push(
                                        (object) [
                                            'id' => $key,
                                            'name' => $group->name,
                                            'avatar' => $group->image
                                                ? asset($group->image_url)
                                                : asset('/assets/img/chat/group-icon.png'),
                                        ],
                                    );
                                }
                            }

                            $allUnread = $unreadGroups->merge($teamGroupsUnread)->merge($unreadUsers);
                        @endphp

                        @if ($allUnread->isEmpty())
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-chat-left-dots fs-3 d-block mb-2"></i>
                                No unread messages
                            </div>
                        @else
                            @foreach ($allUnread as $item)
                                @php
                                    if (isset($item->email)) {
                                        $displayName =
                                            $item->user_type === 'company'
                                                ? 'Company Admin'
                                                : trim(($item->f_name ?? '') . ' ' . ($item->l_name ?? ''));
                                        $displayName = $displayName ?: $item->email;
                                        $avatar = $item->employee->avatar_url ?? asset('assets/img/default-image.jpg');
                                    } else {
                                        $displayName = $item->name;
                                        $avatar = $item->avatar;
                                    }

                                    $count = $unreadCounts[$item->id] ?? 0;
                                @endphp

                                <div class="chat-list-item {{ $receiverId == $item->id ? 'active-chat border-start border-3 border-primary' : '' }}"
                                    wire:click="startNewChat('{{ $item->id }}')"
                                    style="background-color:#ffe5e5;">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $avatar }}" class="rounded-circle me-3"
                                            style="width:40px;height:40px;object-fit:cover;">
                                        <div>
                                            <div class="fw-bold">{{ $displayName }}</div>
                                            <small class="text-muted">
                                                {{ $lastMessages[$item->id] ?? 'Start a conversation' }}
                                            </small>

                                            @if ($count > 0)
                                                <span class="badge bg-danger ms-1">{{ $count }}</span>
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
            <div class= "d-flex flex-column p-0 flex-grow-1 chat-area">

                {{-- Chat Header --}}
                <div style="min-height: 75px"
                    class="p-3 border-bottom d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">


                        <div class="rounded-circle me-2"
                            style="width:45px; min-width:45px; height:45px; background-color:#2299dd; display:flex; justify-content:center; align-items:center; overflow:hidden; font-weight:bold; color:#fff;">

                            @if ($receiverInfo && ($receiverInfo['type'] === 'group' || $receiverInfo['type'] === 'teamGroup'))
                                <img src="{{ $receiverInfo['photo'] }}" alt="Group Icon"
                                    style="width:100%; height:auto; object-fit:cover; max-height:200px; border-radius:8px;">
                            @else
                                @php
                                    $nameParts = explode(' ', $receiverInfo['name'] ?? '');
                                    $fInitial = strtoupper(substr($nameParts[0] ?? '', 0, 1));
                                    $lInitial = strtoupper(substr(end($nameParts) ?? '', 0, 1));
                                    $initials = $fInitial . $lInitial;
                                @endphp

                                <span>{{ $initials }}</span>
                            @endif

                        </div>


                        <div>
                            <div class="fw-bold" style="font-size: 1rem;">
                                {{ $receiverInfo['name'] ?? 'Select a chat' }}
                            </div>

                        </div>
                    </div>



                </div>


                <input type="hidden" id="pusher_key" value="{{ config('broadcasting.connections.pusher.key') }}">
                <input type="hidden" id="pusher_cluster"
                    value="{{ config('broadcasting.connections.pusher.options.cluster') }}">
                <input type="hidden" id="current_company_id" value="{{ currentCompanyId() }}">
                <input type="hidden" id="current_user_id" value="{{ auth()->id() }}">
                <input type="hidden" id="currentReceiverId" value="{{ $receiverId }}">



                {{-- Messages --}}

                <div class="flex-grow-1 p-md-4 p-3 main-chat-area" style="overflow-y: auto;" id="chatScroll">
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



                                    @if (isset($msg->attachment_url) && $msg->attachment_url)
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
                                    {{ $msg->created_at->format('h:i A') }}
                                </small>

                            </div>
                        </div>
                    @endforeach
                </div>



                <div class="text-center my-2 d-none align-items-center px-4" wire:loading.flex
                    wire:target="sendMessage, loadMore">
                    <span class="spinner-border spinner-border-sm me-2" role="status"></span> Loading...
                </div>




                {{-- Message Input --}}
                <div class="p-3 border-top bg-white">

                    <div class="message-box border rounded p-2 bg-white position-relative">

                        <!-- Input at TOP -->
                        <div class="mb-2">
                            <input type="text" id="message_input" class="form-control"
                                placeholder="Write something..." wire:model.defer="messageText"
                                wire:keydown.enter="sendMessage" wire:loading.attr="readonly"
                                wire:target="sendMessage">
                        </div>

                        <!-- BOTTOM actions -->
                        <div class="d-flex justify-content-between align-items-center mt-2">

                            <!-- Left Actions -->
                            <div class="d-flex align-items-center gap-2">

                                <!-- Attachment -->
                                <div class="position-relative">
                                    <button type="button" class="btn btn-light rounded-circle icon-30"
                                        id="attachmentBtn">
                                        <i class="fas fa-paperclip"></i>
                                    </button>

                                    <!-- Popup -->
                                    <div id="attachmentPopup"
                                        class="bg-white border rounded shadow-sm p-2 position-absolute"
                                        style="display: {{ $showAttachmentPopup ? 'block' : 'none' }}; bottom:40px; left:0; z-index:2000;"
                                        wire:ignore>
                                        <div class="d-flex gap-2">
                                            <label style="min-width: 50px;"
                                                class="btn btn-light d-flex flex-column align-items-center p-2">
                                                <i class="fas fa-image mb-1"></i>
                                                <small>Image</small>
                                                <input type="file" wire:model="attachment" accept="image/*"
                                                    hidden>
                                            </label>
                                            <label style="min-width: 50px;"
                                                class="btn btn-light d-flex flex-column align-items-center p-2">
                                                <i class="fas fa-video mb-1"></i>
                                                <small>Video</small>
                                                <input type="file" wire:model="attachment" accept="video/*"
                                                    hidden>
                                            </label>
                                            <label style="min-width: 50px;"
                                                class="btn btn-light d-flex flex-column align-items-center p-2">
                                                <i class="fas fa-file-video mb-1"></i>
                                                <small>GIF</small>
                                                <input type="file" wire:model="attachment" accept=".gif" hidden>
                                            </label>
                                            <label style="min-width: 50px;"
                                                class="btn btn-light d-flex flex-column align-items-center p-2">
                                                <i class="fas fa-file-alt mb-1"></i>
                                                <small>File</small>
                                                <input type="file" wire:model="attachment" accept=".pdf" hidden>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Emoji -->
                                <button id="show_emoji_box" class="btn btn-light rounded-circle icon-30">😊</button>


                                @if (!is_numeric($this->receiverId))
                                    <div class="position-relative" x-data
                                        @click.outside="$wire.set('showMentionBox', false)">

                                        <button type="button" class="btn btn-light rounded-circle icon-30"
                                            wire:click="toggleMentionBox">
                                            @
                                        </button>

                                        @if ($showMentionBox)
                                            <div id="mentionBox"
                                                class="position-absolute bg-white border rounded shadow-sm p-2 dropdown-scroll"
                                                style="bottom:40px; left:0; width:200px; z-index:2000;">

                                                <input type="text" class="form-control mb-1"
                                                    placeholder="Search..." wire:model.live="mentionSearch">

                                                @forelse ($mentionUsers as $user)
                                                    @php
                                                        $displayName =
                                                            $user->user_type === 'company'
                                                                ? 'Company Admin'
                                                                : trim(
                                                                    ($user->f_name ?? '') . ' ' . ($user->l_name ?? ''),
                                                                );
                                                        $displayName = $displayName ?: $user->email;
                                                    @endphp

                                                    <div class="p-2 hover-bg-light cursor-pointer"
                                                        wire:click="selectMention({{ $user->id }})">
                                                        {{ $displayName }}
                                                    </div>
                                                @empty
                                                    <div class="text-muted p-2">No users found</div>
                                                @endforelse

                                            </div>
                                        @endif
                                    </div>
                                @endif




                            </div>

                            <!-- Send icon (Right) -->
                            <div class="d-flex align-items-center">

                                <span wire:loading wire:target="sendMessage" class="me-3">
                                    <span class="spinner-border spinner-border-sm"></span>
                                </span>

                                <span
                                    class="btn btn-primary rounded-circle icon-30 d-flex justify-content-center align-items-center"
                                    wire:click="sendMessage">
                                    <i class="fa-solid fa-paper-plane text-white"></i>
                                </span>

                            </div>

                        </div>

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
                            <input type="text" id="message_input" class="form-control"
                                placeholder="Write something..." wire:model.defer="messageText"
                                wire:keydown.enter="sendAttachmentMessage" wire:loading.attr="readonly"
                                wire:target="sendAttachmentMessage">
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
                                            'chat/attachments/tmp/' .
                                            uniqid() .
                                            '_' .
                                            $attachment->getClientOriginalName();
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


                        <button class="btn btn-primary" wire:click="sendAttachmentMessage"
                            wire:loading.attr="disabled" wire:target="sendAttachmentMessage">
                            <span wire:loading.remove wire:target="sendAttachmentMessage"
                                wire:keydown.enter.prevent="sendAttachmentMessage">Send</span>
                            <span wire:loading wire:target="sendAttachmentMessage">
                                <i class="fas fa-spinner fa-spin"></i> Sending...
                            </span>
                        </button>

                    </div>

                </div>
            </div>
        </div>


        <div class="modal fade" id="newTeamModal" tabindex="-1" aria-hidden="true" wire:ignore.self
            aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
                <div class="modal-content" style="border-radius: 15px;">

                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Create New Team</h5>
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border:none;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="modal-body">

                        <!-- STEP NAVIGATION -->
                        <div class="d-flex justify-content-center mb-3">
                            <span class="badge {{ $teamStep == 1 ? 'bg-primary' : 'bg-secondary' }} me-2">1</span>
                            <span class="badge {{ $teamStep == 2 ? 'bg-primary' : 'bg-secondary' }}">2</span>
                        </div>

                        <!-- STEP 1 -->
                        @if ($teamStep == 1)
                            <div>

                                <label class="form-label fw-bold">Team Image <span
                                        class="text-danger">*</span></label>

                                <div onclick="document.getElementById('teamImageInput').click()"
                                    style="
        width:130px;
        height:130px;
        border-radius:50%;
        overflow:hidden;
        border:3px solid #ddd;
        cursor:pointer;
        position:relative;
        transition:0.3s;
        background:#f8f9fa;
        display:flex;
        align-items:center;
        justify-content:center;
    "
                                    onmouseover="this.style.borderColor='#3b82f6'"
                                    onmouseout="this.style.borderColor='#ddd'">


                                    @if ($teamImage)
                                        <img src="{{ $teamImage->temporaryUrl() }}"
                                            style="width:100%; height:100%; object-fit:cover;">
                                    @else
                                        <img src="{{ asset('assets/img/default-image.jpg') }}"
                                            style="width:100%; height:100%; object-fit:cover;">
                                    @endif

                                    <div wire:loading.flex wire:target="teamImage"
                                        style="
            position:absolute;
            top:0;
            left:0;
            width:100%;
            height:100%;
            background:rgba(255,255,255,0.6);
            align-items:center;
            justify-content:center;
            font-size:24px;
            display:none; /* initial hidden */
         ">
                                        <div class="spinner-border text-primary" role="status"
                                            style="width:40px; height:40px;">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>

                                </div>

                                <input type="file" id="teamImageInput" wire:model="teamImage"
                                    style="display:none;" accept="image/*">


                                @error('teamImage')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror



                                <label class="form-label fw-bold mt-3">Team Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" wire:model="teamName" required>

                                @error('teamName')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror

                                <label class="form-label fw-bold mt-3">Description</label>
                                <textarea class="form-control" rows="3" wire:model="teamDescription"></textarea>

                                @error('teamDescription')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror

                                <button class="btn btn-primary mt-3 w-100" wire:click="nextTeamStep">
                                    Next →
                                </button>

                            </div>
                        @endif

                        <!-- STEP 2 -->
                        @if ($teamStep == 2)
                            <div>

                                {{-- Manual Team Member Addition --}}
                                @if ($manualTeam)

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Department <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select shadow-sm" wire:model="department_id" required>
                                            <option value="">Select Department</option>
                                            @foreach ($departments as $dep)
                                                <option value="{{ $dep->id }}">{{ $dep->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('department_id')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>


                                    <label class="form-label fw-bold">Add Members <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control mb-2"
                                        placeholder="Search members by  name, email" wire:model="teamMemberSearch"
                                        wire:keyup="set('teamMemberSearch', $event.target.value)">
                                    @error('selectedTeamMembers')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror

                                    <div style="max-height: 300px; overflow-y: auto;">
                                        @if (!empty($teamMemberList))
                                            @foreach ($teamMemberList as $member)
                                                <div
                                                    class="d-flex justify-content-between align-items-center p-2 border-bottom">
                                                    <div>
                                                        {{ $member->f_name }} {{ $member->l_name }}
                                                        <br>
                                                        <small class="text-muted">{{ $member->email }}</small>
                                                    </div>
                                                    <button type="button" class="btn btn-sm btn-primary"
                                                        wire:click.prevent="addTeamMember({{ $member->id }})"
                                                        @if (in_array($member->id, $selectedTeamMembers ?? [])) disabled @endif>
                                                        Add
                                                    </button>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="text-muted p-2">No members available.</div>
                                        @endif

                                    </div>
                                @endif

                                {{-- Selected Members List --}}
                                @if (!empty($selectedTeamMembersList))
                                    <div class="mt-3">
                                        <label class="form-label fw-bold">Selected Members</label>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach ($selectedTeamMembersList as $member)
                                                <div class="badge bg-secondary d-flex align-items-center gap-3">
                                                    {{ $member->f_name }} {{ $member->l_name }}


                                                    <a href="javascript:void(0)"
                                                        class="icon-20 rounded-circle bg-white"
                                                        wire:click.prevent="removeTeamMember({{ $member->id }})">
                                                        <i class="fas fa-times"></i>
                                                    </a>



                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                {{-- Navigation --}}
                                <div class="d-flex mt-3">
                                    <button class="btn btn-secondary w-50 me-2" wire:click="prevTeamStep">←
                                        Back</button>
                                    <button class="btn btn-success w-50" wire:click="createTeam"
                                        wire:loading.attr="disabled" wire:target="createTeam">

                                        <span wire:loading.remove wire:target="createTeam">Create Team</span>


                                        <span wire:loading wire:target="createTeam">
                                            <span class="spinner-border spinner-border-sm me-2" role="status"
                                                aria-hidden="true"></span>
                                            Creating...
                                        </span>
                                    </button>
                                </div>
                            </div>
                        @endif



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
                        <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal"
                            aria-label="Close">
                            <i class="fas fa-times"></i>
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

                                if ($user->user_type === 'employee') {
                                    $avatar = null;
                                    $showInitials = true;
                                    $initials = strtoupper(
                                        substr($user->employee->f_name ?? '', 0, 1) .
                                            substr($user->employee->l_name ?? '', 0, 1),
                                    );
                                } elseif ($user->user_type === 'company') {
                                    $avatar =
                                        $user->company && $user->company->company_logo_url
                                            ? asset($user->company->company_logo_url)
                                            : asset('/assets/img/default-image.jpg');
                                    $showInitials = false;
                                } else {
                                    $avatar = asset('/assets/img/default-image.jpg');
                                    $showInitials = false;
                                }

                            @endphp


                            <div class="d-flex align-items-center p-2 hover-bg-light rounded mb-2"
                                style="cursor:pointer;" wire:click="startNewChat({{ $user->id }})"
                                data-bs-dismiss="modal">

                                @if ($showInitials)
                                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                                        style="width:45px; height:45px; background-color:#6c757d; color:#fff; font-weight:bold;">
                                        {{ $initials }}
                                    </div>
                                @else
                                    <img src="{{ $avatar }}" class="rounded-circle me-3"
                                        style="width:45px;height:45px;object-fit:cover;">
                                @endif

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

    {{-- will work after tablate --}}
    <div class="chat-app-container d-md-none d-block mobile-chat-container">
        <div class="d-flex h-100">

            {{-- SIDEBAR --}}
            <div class="d-flex flex-column p-0 sidebar mobile-chat-sidebar">

                {{-- Add new --}}
                <div style="min-height: 77px"
                    class="p-3 d-flex justify-content-between align-items-center border-bottom gap-2">
                    <button class="btn btn-outline-danger d-md-none" id="sidebarClose">
                        <i class="fa-solid fa-xmark text-danger"></i>
                    </button>
                    <div class="dropdown" wire:ignore>
                        <button class="mb-0 btn btn-primary gap-1 d-flex align-items-center dropdown-toggle"
                            type="button" id="addNewDropdown" data-bs-toggle="dropdown"
                            style="border-radius: 20px; font-weight: 600;">
                            <i class="fa-solid fa-plus"></i> Add new
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="addNewDropdown">
                            <li>
                                <a class="dropdown-item d-flex align-items-center" href="#"
                                    data-bs-toggle="modal" data-bs-target="#newChatModal"
                                    wire:click="$set('searchUser', '')">

                                    <i class="bi bi-chat-left-text me-2"></i> New Chat
                                </a>
                            </li>
                            @if (auth()->user()->user_type === 'company')
                                <li>
                                    <a class="dropdown-item d-flex align-items-center" href="#"
                                        data-bs-toggle="modal" data-bs-target="#newTeamModal"
                                        wire:click="openNewTeamModal">

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
                        <input type="text" class="form-control" placeholder="Search" wire:model="searchTerm"
                            wire:keyup="set('searchTerm', $event.target.value)">
                    </div>

                    {{-- Tabs --}}
                    <div class="d-flex flex-wrap mt-3" id="chat-filters">
                        <button wire:click="$set('tab', 'all')"
                            class="btn px-3 btn-sm me-2 fw-bold {{ $tab == 'all' ? 'btn-primary text-white' : 'btn-light text-muted' }}"
                            style="border-radius: 15px;">All</button>
                        <button wire:click="$set('tab', 'unread')"
                            class="btn px-3 btn-sm me-2 fw-bold {{ $tab == 'unread' ? 'btn-primary text-white' : 'btn-light text-muted' }}"
                            style="border-radius: 15px;">Unread</button>
                        <button wire:click="$set('tab', 'teams')"
                            class="btn px-3 btn-sm me-2 fw-bold {{ $tab == 'teams' ? 'btn-primary text-white' : 'btn-light text-muted' }}"
                            style="border-radius: 15px;">Teams</button>
                    </div>
                </div>

                {{-- Sidebar User List --}}
                <div class="flex-grow-1 overflow-auto mt-2 pe-3 ps-1">
                    @if ($tab == 'all')
                        {{-- Always show All users' team chat first --}}
                        <div class="chat-list-item {{ $receiverId === 'group' ? 'active-chat border-start border-3 border-primary' : '' }}"
                            wire:click="startNewChat('group')"
                            style="
        {{ $receiverId === 'group' ? 'background-color:#f0f0f0;' : '' }}
        {{ isset($unreadCounts['group']) && $unreadCounts['group'] > 0 ? 'background-color:#ffe5e5;' : '' }}
     ">

                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center flex-wrap">
                                    <div class="rounded-circle p-2 me-3"
                                        style="width: 40px; height: 40px; display:flex; justify-content:center; align-items:center;">
                                        <img src="{{ asset('/assets/img/chat/group-icon.png') }}" alt="Group Icon"
                                            style="width:40px;height:40px;object-fit:cover;">
                                    </div>
                                    <div>
                                        <div class="fw-bold">All users' team chat</div>
                                        <small class="text-muted text-break">
                                            {{ isset($lastMessages['group']) ? Str::limit($lastMessages['group'], 50) : 'Start a conversation' }}
                                        </small>


                                        <div class="text-muted small">

                                            {{ isset($lastMessageTimes['group']) ? $lastMessageTimes['group']->format('d M, h:i A') : '' }}
                                        </div>

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


                        @foreach ($teamGroups as $group)
                            <div x-data="{ showMenu: false }" @contextmenu.prevent="showMenu = true"
                                @click.away="showMenu = false"
                                class="chat-list-item position-relative {{ $receiverId === 'teamGroup_' . $group->id ? 'active-chat border-start border-3 border-primary' : '' }}"
                                wire:click="startNewChat('teamGroup_{{ $group->id }}')"
                                style="
            {{ $receiverId === 'teamGroup_' . $group->id ? 'background-color:#f0f0f0;' : '' }}
            {{ isset($unreadCounts['teamGroup_' . $group->id]) && $unreadCounts['teamGroup_' . $group->id] > 0 ? 'background-color:#ffe5e5;' : '' }}
         ">

                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center flex-wrap">
                                        <div class="rounded-circle p-2 me-3"
                                            style="width: 40px; height: 40px; display:flex; justify-content:center; align-items:center;">
                                            <img src="{{ $group->image ? asset($group->image_url) : asset('/assets/img/chat/group-icon.png') }}"
                                                alt="{{ $group->name }}"
                                                style="width:40px;height:40px;object-fit:cover;">
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $group->name }}</div>
                                            <small class="text-muted">
                                                {{ isset($lastMessages['teamGroup_' . $group->id]) ? Str::limit($lastMessages['teamGroup_' . $group->id], 50) : 'Start a conversation' }}
                                            </small>
                                            <div class="text-muted small">
                                                {{ isset($lastMessageTimes['teamGroup_' . $group->id]) ? $lastMessageTimes['teamGroup_' . $group->id]->format('d M, h:i A') : '' }}
                                            </div>
                                            @if (isset($unreadCounts['teamGroup_' . $group->id]) && $unreadCounts['teamGroup_' . $group->id] > 0)
                                                <span
                                                    class="badge bg-danger">{{ $unreadCounts['teamGroup_' . $group->id] }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Dropdown context menu -->
                                <div x-show="showMenu" x-cloak
                                    class="position-absolute bg-white border rounded shadow-sm"
                                    style="top: 0.5rem; right: 2rem; z-index:1000; min-width: 140px;">
                                    <button type="button" class="dropdown-item text-danger"
                                        wire:click.stop="deleteConversation('teamGroup_{{ $group->id }}')">
                                        <i class="bi bi-trash me-1"></i> Delete Conversation
                                    </button>
                                </div>

                            </div>
                        @endforeach


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
                                $itemKey = $user->id;
                            @endphp

                            <div x-data="{ showMenu: false }" @click.away="showMenu = false"
                                @contextmenu.prevent="showMenu = true"
                                class="chat-list-item {{ $receiverId == $itemKey ? 'active-chat border-start border-3 border-primary' : '' }}"
                                wire:click="startNewChat({{ $itemKey }})"
                                style="
            {{ $receiverId == $itemKey ? 'background-color:#f0f0f0;' : '' }}
            {{ isset($unreadCounts[$itemKey]) && $unreadCounts[$itemKey] > 0 ? 'background-color:#ffe5e5;' : '' }}
        ">

                                <div class="d-flex align-items-center">
                                    <img src="{{ $avatar }}" class="rounded-circle me-3"
                                        style="width:40px;height:40px;object-fit:cover;">
                                    <div>
                                        <div class="fw-bold">{{ $displayName }}</div>

                                        <small class="text-muted">
                                            @if (isset($lastMessages[$itemKey]) && $lastMessages[$itemKey])
                                                {{ Str::limit($lastMessages[$itemKey], 50) }}
                                            @elseif(isset($lastAttachments[$itemKey]) && $lastAttachments[$itemKey])
                                                Sent an attachment
                                            @else
                                                Start a conversation
                                            @endif
                                        </small>

                                        @if (isset($unreadCounts[$itemKey]) && $unreadCounts[$itemKey] > 0)
                                            <span class="badge bg-danger ms-1">{{ $unreadCounts[$itemKey] }}</span>
                                        @endif

                                        <div class="text-muted small">
                                            {{ isset($lastMessageTimes[$itemKey]) ? $lastMessageTimes[$itemKey]->format('d M, h:i A') : '' }}
                                        </div>
                                    </div>
                                </div>

                                <!-- RIGHT CLICK MENU -->
                                <div x-show="showMenu" class="position-absolute bg-white shadow p-2 rounded"
                                    style="top:10px; right:10px; width:150px; z-index:1000;">
                                    <button class="dropdown-item text-danger"
                                        wire:click.stop="deleteConversation('{{ $itemKey }}')">
                                        <i class="bi bi-trash"></i> Delete Conversation
                                    </button>
                                </div>

                            </div>
                        @endforeach
                    @elseif($tab == 'teams')
                        <div class="chat-list-item {{ $receiverId === 'group' ? 'active-chat border-start border-3 border-primary' : '' }}"
                            wire:click="startNewChat('group')"
                            style="
        {{ $receiverId === 'group' ? 'background-color:#f0f0f0;' : '' }}
        {{ isset($unreadCounts['group']) && $unreadCounts['group'] > 0 ? 'background-color:#ffe5e5;' : '' }}
     ">

                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center flex-wrap">
                                    <div class="rounded-circle p-2 me-3"
                                        style="width: 40px; height: 40px; display:flex; justify-content:center; align-items:center;">
                                        <img src="{{ asset('/assets/img/chat/group-icon.png') }}" alt="Group Icon"
                                            style="width:40px;height:40px;object-fit:cover;">
                                    </div>
                                    <div>
                                        <div class="fw-bold">All users' team chat</div>



                                        <small class="text-muted">
                                            @if (isset($lastMessages['group']) && $lastMessages['group'])
                                                {{ Str::limit($lastMessages['group'], 50) }}
                                            @elseif(isset($lastAttachments['group']) && $lastAttachments['group'])
                                                Sent an attachment
                                            @else
                                                Start a conversation
                                            @endif
                                        </small>


                                        <div class="text-muted small">

                                            {{ isset($lastMessageTimes['group']) ? $lastMessageTimes['group']->format('d M, h:i A') : '' }}
                                        </div>

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


                        @foreach ($teamGroups as $group)
                            <div x-data="{ showMenu: false }" @contextmenu.prevent="showMenu = true"
                                @click.away="showMenu = false"
                                class="chat-list-item position-relative {{ $receiverId === 'teamGroup_' . $group->id ? 'active-chat border-start border-3 border-primary' : '' }}"
                                wire:click="startNewChat('teamGroup_{{ $group->id }}')"
                                style="
            {{ $receiverId === 'teamGroup_' . $group->id ? 'background-color:#f0f0f0;' : '' }}
            {{ isset($unreadCounts['teamGroup_' . $group->id]) && $unreadCounts['teamGroup_' . $group->id] > 0 ? 'background-color:#ffe5e5;' : '' }}
         ">

                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle p-2 me-3 flex-wrap"
                                            style="width: 40px; height: 40px; display:flex; justify-content:center; align-items:center;">
                                            <img src="{{ $group->image ? asset($group->image_url) : asset('/assets/img/chat/group-icon.png') }}"
                                                alt="{{ $group->name }}"
                                                style="width:40px;height:40px;object-fit:cover;">
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $group->name }}</div>
                                            <small class="text-muted">
                                                {{ isset($lastMessages['teamGroup_' . $group->id]) ? Str::limit($lastMessages['teamGroup_' . $group->id], 50) : 'Start a conversation' }}
                                            </small>
                                            <div class="text-muted small">
                                                {{ isset($lastMessageTimes['teamGroup_' . $group->id]) ? $lastMessageTimes['teamGroup_' . $group->id]->format('d M, h:i A') : '' }}
                                            </div>
                                            @if (isset($unreadCounts['teamGroup_' . $group->id]) && $unreadCounts['teamGroup_' . $group->id] > 0)
                                                <span
                                                    class="badge bg-danger">{{ $unreadCounts['teamGroup_' . $group->id] }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Dropdown context menu -->
                                <div x-show="showMenu" x-cloak
                                    class="position-absolute bg-white border rounded shadow-sm"
                                    style="top: 0.5rem; right: 2rem; z-index:1000; min-width: 140px;">
                                    <button type="button" class="dropdown-item text-danger"
                                        wire:click.stop="deleteConversation('teamGroup_{{ $group->id }}')">
                                        <i class="bi bi-trash me-1"></i> Delete Conversation
                                    </button>
                                </div>

                            </div>
                        @endforeach
                    @endif

                    @if ($tab == 'unread')
                        @php
                            // Personal unread users
                            $unreadUsers = $chatUsers->filter(
                                fn($user) => isset($unreadCounts[$user->id]) && $unreadCounts[$user->id] > 0,
                            );

                            // Group unread
                            $unreadGroups = collect();
                            if (isset($unreadCounts['group']) && $unreadCounts['group'] > 0) {
                                $unreadGroups->push(
                                    (object) [
                                        'id' => 'group',
                                        'name' => "All users' team chat",
                                        'avatar' => asset('/assets/img/chat/group-icon.png'),
                                    ],
                                );
                            }

                            // TeamGroup unread
                            $teamGroupsUnread = collect();
                            foreach ($teamGroups as $group) {
                                $key = 'teamGroup_' . $group->id;
                                if (isset($unreadCounts[$key]) && $unreadCounts[$key] > 0) {
                                    $teamGroupsUnread->push(
                                        (object) [
                                            'id' => $key,
                                            'name' => $group->name,
                                            'avatar' => $group->image
                                                ? asset($group->image_url)
                                                : asset('/assets/img/chat/group-icon.png'),
                                        ],
                                    );
                                }
                            }

                            $allUnread = $unreadGroups->merge($teamGroupsUnread)->merge($unreadUsers);
                        @endphp

                        @if ($allUnread->isEmpty())
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-chat-left-dots fs-3 d-block mb-2"></i>
                                No unread messages
                            </div>
                        @else
                            @foreach ($allUnread as $item)
                                @php
                                    if (isset($item->email)) {
                                        $displayName =
                                            $item->user_type === 'company'
                                                ? 'Company Admin'
                                                : trim(($item->f_name ?? '') . ' ' . ($item->l_name ?? ''));
                                        $displayName = $displayName ?: $item->email;
                                        $avatar = $item->employee->avatar_url ?? asset('assets/img/default-image.jpg');
                                    } else {
                                        $displayName = $item->name;
                                        $avatar = $item->avatar;
                                    }

                                    $count = $unreadCounts[$item->id] ?? 0;
                                @endphp

                                <div class="chat-list-item {{ $receiverId == $item->id ? 'active-chat border-start border-3 border-primary' : '' }}"
                                    wire:click="startNewChat('{{ $item->id }}')"
                                    style="background-color:#ffe5e5;">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $avatar }}" class="rounded-circle me-3"
                                            style="width:40px;height:40px;object-fit:cover;">
                                        <div>
                                            <div class="fw-bold">{{ $displayName }}</div>
                                            <small class="text-muted">
                                                {{ $lastMessages[$item->id] ?? 'Start a conversation' }}
                                            </small>

                                            @if ($count > 0)
                                                <span class="badge bg-danger ms-1">{{ $count }}</span>
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
            <div class= "d-flex flex-column p-0 flex-grow-1 chat-area">

                {{-- Chat Header --}}
                <div style="min-height: 75px"
                    class="p-3 border-bottom d-flex justify-content-between align-items-center gap-2">
                    <button class="btn btn-outline-secondary d-md-none" id="sidebarToggle" type="button">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <div class="d-flex align-items-center">


                        <div class="rounded-circle me-2"
                            style="width:45px; min-width:45px; height:45px; background-color:#2299dd; display:flex; justify-content:center; align-items:center; overflow:hidden; font-weight:bold; color:#fff;">

                            @if ($receiverInfo && ($receiverInfo['type'] === 'group' || $receiverInfo['type'] === 'teamGroup'))
                                <img src="{{ $receiverInfo['photo'] }}" alt="Group Icon"
                                    style="width:100%; height:auto; object-fit:cover; max-height:200px; border-radius:8px;">
                            @else
                                @php
                                    $nameParts = explode(' ', $receiverInfo['name'] ?? '');
                                    $fInitial = strtoupper(substr($nameParts[0] ?? '', 0, 1));
                                    $lInitial = strtoupper(substr(end($nameParts) ?? '', 0, 1));
                                    $initials = $fInitial . $lInitial;
                                @endphp

                                <span>{{ $initials }}</span>
                            @endif

                        </div>


                        <div>
                            <div class="fw-bold" style="font-size: 1rem;">
                                {{ $receiverInfo['name'] ?? 'Select a chat' }}
                            </div>

                        </div>
                    </div>


                </div>


                <input type="hidden" id="pusher_key" value="{{ config('broadcasting.connections.pusher.key') }}">
                <input type="hidden" id="pusher_cluster"
                    value="{{ config('broadcasting.connections.pusher.options.cluster') }}">
                <input type="hidden" id="current_company_id" value="{{ currentCompanyId() }}">
                <input type="hidden" id="current_user_id" value="{{ auth()->id() }}">
                <input type="hidden" id="currentReceiverId" value="{{ $receiverId }}">



                {{-- Messages --}}

                <div class="flex-grow-1 p-md-4 p-3 main-chat-area" style="overflow-y: auto;" id="chatScroll">
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
                            <p class="text-center text-muted my-3" style="font-size: 0.8rem;">{{ $dateText }}
                            </p>
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



                                    @if (isset($msg->attachment_url) && $msg->attachment_url)
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




                <div class="text-center my-2 d-none align-items-center px-4" wire:loading.flex
                    wire:target="sendMessage, loadMore">
                    <span class="spinner-border spinner-border-sm me-2" role="status"></span> Loading...
                </div>




                {{-- Message Input --}}
                <div class="p-3 border-top bg-white">

                    <div class="message-box border rounded p-2 bg-white position-relative">

                        <!-- Input at TOP -->
                        <div class="mb-2">
                            <input type="text" id="message_input" class="form-control"
                                placeholder="Write something..." wire:model.defer="messageText"
                                wire:keydown.enter="sendMessage" wire:loading.attr="readonly"
                                wire:target="sendMessage">
                        </div>

                        <!-- BOTTOM actions -->
                        <div class="d-flex justify-content-between align-items-center mt-2">

                            <!-- Left Actions -->
                            <div class="d-flex align-items-center gap-2">

                                <!-- Attachment -->
                                <div class="position-relative">
                                    <button type="button" class="btn btn-light rounded-circle icon-30"
                                        id="attachmentBtn">
                                        <i class="fas fa-paperclip"></i>
                                    </button>

                                    <!-- Popup -->
                                    <div id="attachmentPopup"
                                        class="bg-white border rounded shadow-sm p-2 position-absolute"
                                        style="display: {{ $showAttachmentPopup ? 'block' : 'none' }}; bottom:40px; left:0; z-index:2000;"
                                        wire:ignore>
                                        <div class="d-flex gap-2">
                                            <label style="min-width: 50px;"
                                                class="btn btn-light d-flex flex-column align-items-center p-2">
                                                <i class="fas fa-image mb-1"></i>
                                                <small>Image</small>
                                                <input type="file" wire:model="attachment" accept="image/*"
                                                    hidden>
                                            </label>
                                            <label style="min-width: 50px;"
                                                class="btn btn-light d-flex flex-column align-items-center p-2">
                                                <i class="fas fa-video mb-1"></i>
                                                <small>Video</small>
                                                <input type="file" wire:model="attachment" accept="video/*"
                                                    hidden>
                                            </label>
                                            <label style="min-width: 50px;"
                                                class="btn btn-light d-flex flex-column align-items-center p-2">
                                                <i class="fas fa-file-video mb-1"></i>
                                                <small>GIF</small>
                                                <input type="file" wire:model="attachment" accept=".gif" hidden>
                                            </label>
                                            <label style="min-width: 50px;"
                                                class="btn btn-light d-flex flex-column align-items-center p-2">
                                                <i class="fas fa-file-alt mb-1"></i>
                                                <small>File</small>
                                                <input type="file" wire:model="attachment" accept=".pdf" hidden>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Emoji -->
                                <button id="show_emoji_box" class="btn btn-light rounded-circle icon-30">😊</button>


                                @if (!is_numeric($this->receiverId))
                                    <div class="position-relative" x-data
                                        @click.outside="$wire.set('showMentionBox', false)">

                                        <button type="button" class="btn btn-light rounded-circle icon-30"
                                            wire:click="toggleMentionBox">
                                            @
                                        </button>

                                        @if ($showMentionBox)
                                            <div id="mentionBox"
                                                class="position-absolute bg-white border rounded shadow-sm p-2 dropdown-scroll"
                                                style="bottom:45px; left:0; width:200px; z-index:2000;">

                                                <input type="text" class="form-control mb-1"
                                                    placeholder="Search..." wire:model.live="mentionSearch">

                                                @forelse ($mentionUsers as $user)
                                                    @php
                                                        $displayName =
                                                            $user->user_type === 'company'
                                                                ? 'Company Admin'
                                                                : trim(
                                                                    ($user->f_name ?? '') . ' ' . ($user->l_name ?? ''),
                                                                );
                                                        $displayName = $displayName ?: $user->email;
                                                    @endphp

                                                    <div class="p-2 hover-bg-light cursor-pointer"
                                                        wire:click="selectMention({{ $user->id }})">
                                                        {{ $displayName }}
                                                    </div>
                                                @empty
                                                    <div class="text-muted p-2">No users found</div>
                                                @endforelse

                                            </div>
                                        @endif
                                    </div>
                                @endif




                            </div>

                            <!-- Send icon (Right) -->
                            <div class="d-flex align-items-center">

                                <span wire:loading wire:target="sendMessage" class="me-3">
                                    <span class="spinner-border spinner-border-sm"></span>
                                </span>

                                <span
                                    class="btn btn-primary rounded-circle icon-30 d-flex justify-content-center align-items-center"
                                    wire:click="sendMessage">
                                    <i class="fa-solid fa-paper-plane text-white"></i>
                                </span>

                            </div>

                        </div>

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
                            <input type="text" id="message_input" class="form-control"
                                placeholder="Write something..." wire:model.defer="messageText"
                                wire:keydown.enter="sendAttachmentMessage" wire:loading.attr="readonly"
                                wire:target="sendAttachmentMessage">
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
                                            'chat/attachments/tmp/' .
                                            uniqid() .
                                            '_' .
                                            $attachment->getClientOriginalName();
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


                        <button class="btn btn-primary" wire:click="sendAttachmentMessage"
                            wire:loading.attr="disabled" wire:target="sendAttachmentMessage">
                            <span wire:loading.remove wire:target="sendAttachmentMessage"
                                wire:keydown.enter.prevent="sendAttachmentMessage">Send</span>
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
                        <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal"
                            aria-label="Close">
                            <i class="fas fa-times"></i>
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

                                if ($user->user_type === 'employee') {
                                    $avatar = null;
                                    $showInitials = true;
                                    $initials = strtoupper(
                                        substr($user->employee->f_name ?? '', 0, 1) .
                                            substr($user->employee->l_name ?? '', 0, 1),
                                    );
                                } elseif ($user->user_type === 'company') {
                                    $avatar =
                                        $user->company && $user->company->company_logo_url
                                            ? asset($user->company->company_logo_url)
                                            : asset('/assets/img/default-image.jpg');
                                    $showInitials = false;
                                } else {
                                    $avatar = asset('/assets/img/default-image.jpg');
                                    $showInitials = false;
                                }

                            @endphp


                            <div class="d-flex align-items-center p-2 hover-bg-light rounded mb-2"
                                style="cursor:pointer;" wire:click="startNewChat({{ $user->id }})"
                                data-bs-dismiss="modal">

                                @if ($showInitials)
                                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                                        style="width:45px; height:45px; background-color:#6c757d; color:#fff; font-weight:bold;">
                                        {{ $initials }}
                                    </div>
                                @else
                                    <img src="{{ $avatar }}" class="rounded-circle me-3"
                                        style="width:45px;height:45px;object-fit:cover;">
                                @endif

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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.querySelector('.mobile-chat-container');
        const openBtn = document.getElementById('sidebarToggle');
        const closeBtn = document.getElementById('sidebarClose');
        const overlay = document.getElementById('sidebarOverlay');

        function openSidebar() {
            container.classList.add('sidebar-open');
        }

        function closeSidebar() {
            container.classList.remove('sidebar-open');
        }

        openBtn?.addEventListener('click', openSidebar);
        closeBtn?.addEventListener('click', closeSidebar);
        overlay?.addEventListener('click', closeSidebar);
    });
</script>
