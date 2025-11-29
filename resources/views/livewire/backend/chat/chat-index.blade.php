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
                        <li><a class="dropdown-item d-flex align-items-center" href="#"><i
                                    class="bi bi-chat-left-text me-2"></i> New Chat</a></li>
                        <li><a class="dropdown-item d-flex align-items-center" href="#"><i
                                    class="bi bi-people-fill me-2"></i> New Team</a></li>
                    </ul>
                </div>
            </div>

            {{-- Search --}}
            <div class="p-3 border-bottom">
                {{-- Search --}}
                <div class="input-group mb-2">
                    <input type="text" class="form-control" placeholder="Search" style="border-radius: 20px;"
                        wire:model="searchTerm">
                    <span class="input-group-text bg-white border-0 position-absolute end-0"
                        style="z-index: 10; padding: 0.375rem 1rem;">
                        <i class="bi bi-search text-muted"></i>
                    </span>
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
                    <div class="chat-list-item {{ $receiverId == 'group' ? 'active-chat border-start border-3 border-primary' : '' }}"
                        wire:click="$set('receiverId', 'group')"
                        style="{{ $receiverId == 'group' ? 'background-color:#f0f0f0;' : '' }}">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle p-2 me-3"
                                style="width: 40px; height: 40px; display:flex; justify-content:center; align-items:center;">
                                <img src="{{ asset('/assets/img/chat/group-icon.png') }}" alt="Group Icon"
                                    style="width: 40px; height: 40px; object-fit: cover;">
                            </div>
                            <div>
                                <div class="fw-bold">All users' team chat</div>
                                <small class="text-muted">All employees & company can see messages</small>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Employee list --}}
                @foreach ($chatUsers as $user)
                    @if ($tab != 'teams' || $tab == 'teams')
                        {{-- show in all and teams tab --}}
                        <div class="chat-list-item {{ $receiverId == $user->id ? 'active-chat border-start border-3 border-primary' : '' }}"
                            wire:click="$set('receiverId', {{ $user->id }})"
                            style="{{ $receiverId == $user->id ? 'background-color:#f0f0f0;' : '' }}">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle p-2 me-3"
                                    style="width: 40px; height: 40px; background-color:#cfe2ff; display:flex; justify-content:center; align-items:center;">
                                    <i class="bi bi-person-fill text-primary"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">{{ $user->f_name . ' ' . $user->l_name }}</div>
                                    <small class="text-muted">Tap to chat</small>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>


        </div>

        {{-- MAIN CHAT AREA --}}
        <div class="col-md-8 col-lg-9 d-flex flex-column p-0">

            {{-- Chat Header --}}
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle p-2 me-2"
                        style="width: 45px; height: 45px; background-color: #d1e7dd; display: flex; justify-content: center; align-items: center; overflow: hidden;">
                        <img src="{{ asset('/assets/img/chat/group-icon.png') }}" alt="Group Icon"
                            style="width: 40px; height: 40px; object-fit: cover;">
                    </div>
                    <div>
                        <div class="fw-bold" style="font-size: 1rem;">All users' team chat</div>
                        <small class="text-muted" style="font-size: 0.85rem;">All employees & company can see
                            messages</small>
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


            {{-- Messages --}}
            <div class="flex-grow-1 p-4 main-chat-area" style="overflow-y: auto;" id="chatScroll">
                <p class="text-center text-muted my-3" style="font-size: 0.8rem;">Today</p>

                @foreach ($messages as $msg)
                    @php
                        $employees = auth()->user()->company ? auth()->user()->company->employees()->get() : collect();

                        $message = $msg->message;

                        // Escape the rest of the message to prevent XSS
                        $message = e($message);

                        foreach ($employees as $user) {
                            $displayName = trim(($user->f_name ?? '') . ' ' . ($user->l_name ?? ''));
                            if (empty($displayName)) {
                                $displayName = $user->email ?? '';
                            }

                            // Only keep first word if mention is typed as single word
                            $displayName = explode(' ', $displayName)[0];

                            // Match @name (case-insensitive)
                            $pattern = '/@' . preg_quote($displayName, '/') . '/i';
                            $replacement = '<span class="mention">@' . $displayName . '</span>';

                            $message = preg_replace($pattern, $replacement, $message);
                        }
                    @endphp



                    <div
                        class="mb-3 d-flex {{ $msg->sender_id == auth()->id() ? 'justify-content-end' : 'justify-content-start' }}">
                        <div
                            class="d-flex flex-column align-items-{{ $msg->sender_id == auth()->id() ? 'end' : 'start' }}">

                            <div class="message-bubble {{ $msg->sender_id == auth()->id() ? 'outgoing-message' : 'incoming-message' }}"
                                style="border-radius: 12px; position: relative; padding-right: 1.5rem; width: 300px;">
                                {!! $message !!}

                                @if ($msg->sender_id == auth()->id())
                                    <i class="fas fa-check-double"
                                        style="position: absolute; right: 4px; bottom: 4px; font-size: 0.6rem; color: #9bbbd4;"></i>
                                @endif
                            </div>


                            <!-- Sender Info Below Bubble -->
                            <small class="text-muted mt-1" style="font-size: 0.7rem;">
                                {{ $msg->sender->f_name . ' ' . $msg->sender->l_name }} ·
                                {{ $msg->created_at->format('H:i') }}
                            </small>

                        </div>
                    </div>
                @endforeach
            </div>


            <div id="typingIndicator" class="text-red mb-2" style="font-size:0.8rem; display:none;">
                <span id="typingUser"></span> is typing...
            </div>



            {{-- Message Input --}}
            <div class="p-3 border-top" style="background: #fff;">
                <div class="input-group position-relative">

                    <input type="text" class="form-control ps-5" placeholder="Write something..."
                        wire:model.defer="messageText" wire:keydown="userTyping" wire:keydown.enter="sendMessage"
                        style="border-radius: 25px; padding-right: 120px; border-color: #ddd;" id="message_input">

                    <!-- LEFT ACTIONS -->
                    <div class="d-flex position-absolute start-0 top-50 translate-middle-y ms-2 align-items-center">

                        <!-- Attachment Button -->
                        <div class="position-relative me-2">
                            <button type="button" class="btn btn-light rounded-circle p-2" id="attachmentBtn"
                                title="Attachment">
                                <i class="fas fa-paperclip"></i>
                            </button>

                            <!-- Attachment Popup -->
                            <div id="attachmentPopup" class="bg-white border rounded shadow-sm p-2 position-absolute"
                                style="bottom: 50px; left: 0; display:none; width: 220px; z-index:1000;">
                                <div class="d-flex justify-content-between">
                                    <button class="btn btn-light d-flex flex-column align-items-center p-2 me-2">
                                        <i class="fas fa-image fa-lg mb-1"></i>
                                        <small>Image</small>
                                    </button>
                                    <button class="btn btn-light d-flex flex-column align-items-center p-2 me-2">
                                        <i class="fas fa-video fa-lg mb-1"></i>
                                        <small>Video</small>
                                    </button>
                                    <button class="btn btn-light d-flex flex-column align-items-center p-2 me-2">
                                        <i class="fas fa-file-video fa-lg mb-1"></i>
                                        <small>GIF</small>
                                    </button>
                                    <button class="btn btn-light d-flex flex-column align-items-center p-2">
                                        <i class="fas fa-file-alt fa-lg mb-1"></i>
                                        <small>File</small>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Emoji Button -->
                        <button id="show_emoji_box" style="width:40px;height:40px;">😊</button>



                        <button type="button" class="btn btn-light rounded-circle p-2" wire:click="toggleMentionBox"
                            title="Mention">@</button>

                        <!-- Mention Dropdown -->
                        @if ($showMentionBox)
                            <div class="position-absolute bg-white border shadow-sm p-2"
                                style="top:-150px; left:50px; z-index:1000; width:200px; max-height:200px; overflow-y:auto;">
                                <input type="text" class="form-control mb-1" placeholder="Search..."
                                    wire:model="mentionSearch">

                                @foreach ($mentionUsers->filter(fn($u) => stripos($u->f_name . ' ' . $u->l_name ?: $u->email, $mentionSearch) !== false) as $user)
                                    <div class="p-2 hover-bg-light cursor-pointer"
                                        wire:click="selectMention({{ $user->id }})">
                                        {{ trim($user->f_name . ' ' . $user->l_name) ?: $user->email }}
                                    </div>
                                @endforeach


                                @if ($mentionUsers->filter(fn($u) => stripos($u->f_name . ' ' . $u->l_name, $mentionSearch) !== false)->isEmpty())
                                    <div class="text-muted p-2">No users found</div>
                                @endif
                            </div>
                        @endif

                    </div>

                    <!-- INPUT FIELD -->


                    <!-- RIGHT ACTIONS -->
                    <div class="position-absolute end-0 top-50 translate-middle-y me-2 d-flex align-items-center">

                        <button class="btn btn-primary rounded-circle p-0" wire:click="sendMessage"
                            style="width:38px;height:38px;display:flex;justify-content:center;align-items:center;">
                            <i class="fas fa-paper-plane"></i>
                        </button>
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
</script>
