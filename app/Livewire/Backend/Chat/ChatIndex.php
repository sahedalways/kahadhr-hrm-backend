<?php

namespace App\Livewire\Backend\Chat;

use App\Events\MessageSent;
use App\Events\UserTyping;
use App\Livewire\Backend\Components\BaseComponent;
use App\Models\ChatMessage;
use App\Models\ChatMessageRead;
use App\Models\User;
use Livewire\WithFileUploads;

class ChatIndex extends BaseComponent
{
    use WithFileUploads;
    public $messages;
    public $messageText;
    public $tab = 'all';
    public $searchTerm = '';
    public $chatUsers;
    public $newChatUsers;
    public $receiverId = 'group';

    public $typing = false;
    public $typingUser = null;

    public $showMentionBox = false;
    public $mentionUsers = [];
    public $lastMessages = [];
    public $mentionSearch = '';
    public $selectedMention = null;

    public $searchUser = '';
    public $receiverInfo = null;
    public $lastMessageTimes = [];
    public $unreadCounts = [];

    public $page = 1;
    public $perPage = 20;
    public $totalMessages = 0;

    protected $listeners = ['incomingMessage'];

    public $attachment;
    public $showAttachmentPopup = false;
    public $showAttachmentModal = false;
    public $attachmentSending = false;


    protected $rules = [
        'attachment' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,mp4,mov,avi,pdf,doc,docx,xls,xlsx,txt',
    ];



    public function mount()
    {
        // Empty collection default
        $this->chatUsers = collect();

        $this->loadConversationUsers();
        $this->loadNewChatUsers();
        $this->loadLastMessages();
        $this->sortChatUsersByLastMessage();
        $this->startNewChat($this->receiverId);

        $this->loadMessages();
    }

    public function updatedTab()
    {
        $this->loadMessages();
    }


    public function loadMessages($resetPage = true)
    {
        if ($resetPage) $this->page = 1;

        if ($this->receiverId === 'group') {
            $query = ChatMessage::where('company_id', currentCompanyId())
                ->whereNull('receiver_id')
                ->orderBy('id', 'desc');

            $messages = $query->skip(($this->page - 1) * $this->perPage)
                ->take($this->perPage)
                ->get()
                ->reverse();

            foreach ($messages as $msg) {
                if ($msg->sender_id !== auth()->id()) {
                    ChatMessageRead::updateOrCreate(
                        [
                            'message_id' => $msg->id,
                            'user_id' => auth()->id()
                        ],
                        ['read_at' => now()]
                    );
                }
            }
        } else {
            // Personal chat
            $query = ChatMessage::where('company_id', currentCompanyId())
                ->where(function ($q) {
                    $q->where('sender_id', auth()->id())
                        ->where('receiver_id', $this->receiverId)
                        ->orWhere('sender_id', $this->receiverId)
                        ->where('receiver_id', auth()->id());
                })
                ->orderBy('id', 'desc');

            $this->totalMessages = $query->count();

            $messages = $query->skip(($this->page - 1) * $this->perPage)
                ->take($this->perPage)
                ->get()
                ->reverse();

            // Mark only receiver messages as read
            foreach ($messages as $msg) {
                if ($msg->sender_id === $this->receiverId) {
                    ChatMessageRead::updateOrCreate(
                        [
                            'message_id' => $msg->id,
                            'user_id' => auth()->id()
                        ],
                        ['read_at' => now()]
                    );
                }
            }
        }

        if ($this->page === 1) {
            $this->messages = $messages;
        } else {
            $this->messages = $messages->merge($this->messages);
        }


        $this->loadLastMessages();
    }


    public function sendMessage()
    {
        if (empty(trim($this->messageText))) return;

        $msg = ChatMessage::create([
            'company_id'  => currentCompanyId(),
            'sender_id'   => auth()->id(),
            'receiver_id' => $this->receiverId === 'group' ? null : $this->receiverId,
            'message'     => $this->messageText,
        ]);

        broadcast(new MessageSent($msg))->toOthers();

        $this->messages->push($msg);

        $this->messageText = "";

        $this->loadConversationUsers();
        $this->loadLastMessages();
        $this->loadMessages();
        $this->sortChatUsersByLastMessage();
        $this->dispatch('scrollToBottom');
    }


    public function selectReceiver($id)
    {
        $this->receiverId = $id;
        $this->loadMessages();
    }

    public function getReceiverProperty()
    {
        if ($this->receiverId === 'group') return null;
        return $this->chatUsers->firstWhere('id', $this->receiverId);
    }

    public function render()
    {

        $filteredUsers = $this->chatUsers->filter(function ($user) {
            return stripos($user->f_name . ' ' . $user->l_name, $this->searchTerm) !== false;
        });

        return view('livewire.backend.chat.chat-index', [
            'chatUsers' => $filteredUsers
        ]);
    }


    public function incomingMessage($id)
    {
        $msg = ChatMessage::find($id);


        if ($msg && !$this->messages->contains('id', $msg->id)) {

            if (($msg->receiver_id === null && $this->receiverId === 'group') ||
                ($msg->receiver_id !== null && $msg->sender_id == $this->receiverId)
            ) {

                ChatMessageRead::updateOrCreate(
                    [
                        'message_id' => $msg->id,
                        'user_id' => auth()->id()
                    ],
                    [
                        'read_at' => now()
                    ]
                );
            }


            $this->messages->push($msg);
            $this->loadConversationUsers();
            $this->loadLastMessages();
            $this->sortChatUsersByLastMessage();
            $this->dispatch('scrollToBottom');
        }
    }

    public function userTyping()
    {
        if (!$this->receiverId) return;

        $companyId = currentCompanyId();

        if ($this->receiverId === 'group') {
            broadcast(new UserTyping(auth()->user(), "$companyId-between-all-users"))->toOthers();
        } else {
            broadcast(new UserTyping(auth()->user(), "$companyId-{$this->receiverId}"))->toOthers();
        }
    }


    public function toggleMentionBox()
    {
        $this->showMentionBox = !$this->showMentionBox;

        if ($this->showMentionBox) {
            $this->mentionUsers = $this->newChatUsers;
        }
    }

    // Insert mention
    public function selectMention($id)
    {

        $employee = collect($this->mentionUsers)->firstWhere('id', $id);
        if (!$employee) return;


        if ($employee->user_type === 'company') {
            $displayName = 'Company Admin';
        } else {
            $displayName = trim(($employee->f_name ?? '') . ' ' . ($employee->l_name ?? ''));
            $displayName = $displayName ?: ($employee->email ?? 'Unknown');
        }


        $mentionText = '<span style="color: #0d6efd">@' . $displayName . '</span>&nbsp;';


        $this->dispatch('insert-mention', ['html' => $mentionText]);


        $this->showMentionBox = false;
    }


    public function startNewChat($userId)
    {
        $this->receiverId = $userId;
        $this->messageText = '';

        if ($userId === 'group') {
            $this->receiverInfo = [
                'type'     => 'group',
                'name'     => "All users' team chat",
                'subtitle' => "All employees & company can see messages",
                'photo'    => asset('/assets/img/chat/group-icon.png'),
            ];
            $this->loadMessages();
            return;
        }

        $user = User::withoutGlobalScopes()->find($userId);

        if ($user) {
            // Determine display name
            $fullName = $user->user_type === 'company'
                ? 'Company Admin'
                : trim($user->f_name . ' ' . $user->l_name);

            // Determine photo
            if ($user->user_type === 'employee') {
                $employee = $user->employee()->withoutGlobalScopes()->first();
                $photo = $employee && $employee->avatar_url
                    ? asset($employee->avatar_url)
                    : asset('/assets/img/default-avatar.png');
            } elseif ($user->user_type === 'company') {
                $photo = $user->company && $user->company->company_logo_url
                    ? asset($user->company->company_logo_url)
                    : asset('/assets/img/default-image.jpg');
            } else {
                $photo = asset('/assets/img/default-image.jpg');
            }

            $this->receiverInfo = [
                'type'     => 'user',
                'name'     => $fullName !== '' ? $fullName : $user->email,
                'subtitle' => $user->email,
                'photo'    => $photo,
            ];
        }

        $this->loadMessages();
        $this->messageText = "";
    }



    public function updatedSearchUser()
    {
        $this->loadChatUsers();
    }

    public function updatedSearchTerm()
    {
        $this->loadConversationUsers();
    }


    public function loadChatUsers()
    {
        $users = collect();

        if (auth()->user()->user_type === 'company') {
            // Company user: load all employees
            if (auth()->user()->company) {
                $users = auth()->user()->company
                    ->employees()
                    ->where('user_id', '!=', auth()->id())
                    ->where('is_active', 1)
                    ->with('user')
                    ->get()
                    ->map(fn($employee) => $employee->user);
            }
        } else {
            // Employee user: load colleagues + company admin
            if (auth()->user()->employee && auth()->user()->employee->company) {
                $employees = auth()->user()->employee->company
                    ->employees()
                    ->where('user_id', '!=', auth()->id())
                    ->where('is_active', 1)
                    ->with('user')
                    ->get()
                    ->map(fn($employee) => $employee->user);

                $companyAdmin = auth()->user()->employee->company->user;

                $users = $employees->push($companyAdmin);
            }
        }

        // Apply search filter
        if ($this->searchUser) {
            $searchTerm = strtolower($this->searchUser);

            $users = $users->filter(function ($user) use ($searchTerm) {
                // Display name logic
                $displayName = $user->user_type === 'company'
                    ? 'Company Admin'
                    : trim(($user->f_name ?? '') . ' ' . ($user->l_name ?? ''));

                $displayName = $displayName ?: $user->email;

                return str_contains(strtolower($displayName), $searchTerm)
                    || str_contains(strtolower($user->email ?? ''), $searchTerm);
            })->values();
        }

        $this->newChatUsers = $users;
    }



    public function loadConversationUsers()
    {
        $companyId = currentCompanyId();

        $userIds = ChatMessage::where('company_id', $companyId)
            ->where(function ($q) {
                $q->where('sender_id', auth()->id())
                    ->orWhere('receiver_id', auth()->id());
            })
            ->pluck('sender_id')
            ->merge(
                ChatMessage::where('company_id', $companyId)
                    ->where(function ($q) {
                        $q->where('sender_id', auth()->id())
                            ->orWhere('receiver_id', auth()->id());
                    })
                    ->pluck('receiver_id')
            )
            ->unique()
            ->filter(fn($id) => $id != auth()->id()) // remove self
            ->values();

        // Load users from these IDs
        $users = User::withoutGlobalScopes()
            ->with([
                'employee' => fn($q) => $q->withoutGlobalScopes(),
                'company'
            ])
            ->whereIn('id', $userIds)
            ->get();

        // Apply search filter
        if ($this->searchTerm) {
            $search = strtolower($this->searchTerm);

            $users = $users->filter(function ($user) use ($search) {

                $fullName = strtolower(trim(($user->f_name ?? '') . ' ' . ($user->l_name ?? '')));
                $email = strtolower($user->email ?? '');

                // MATCH cases
                return str_contains($fullName, $search)
                    || str_contains($email, $search)
                    || ($user->user_type == 'company' && str_contains('company admin', $search));
            })->values();
        }

        $this->chatUsers = $users;
    }





    public function loadNewChatUsers()
    {
        if (auth()->user()->user_type == "company") {
            if (auth()->user()->company) {
                $this->newChatUsers = auth()->user()->company
                    ->employees()
                    ->where('user_id', '!=', auth()->id())
                    ->where('is_active', 1)
                    ->with('user')
                    ->get()
                    ->map(fn($employee) => $employee->user);
            }
        } else {
            if (auth()->user()->employee && auth()->user()->employee->company) {
                $employees = auth()->user()->employee->company
                    ->employees()
                    ->where('user_id', '!=', auth()->id())
                    ->where('is_active', 1)
                    ->with('user')
                    ->get()
                    ->map(fn($employee) => $employee->user);

                $companyAdmin = auth()->user()->employee->company->user;

                $users = $employees->push($companyAdmin);
            }


            $this->newChatUsers = $users;
        }
    }

    public function loadLastMessages()
    {
        $companyId = currentCompanyId();
        $this->lastMessages = [];
        $this->lastMessageTimes = [];
        $this->unreadCounts = [];

        // Group message
        $groupMsg = ChatMessage::where('company_id', $companyId)
            ->whereNull('receiver_id')
            ->latest('id')
            ->first();

        if ($groupMsg) {
            $sender = $groupMsg->sender;
            $senderName = $sender->id === auth()->id()
                ? 'Me'
                : ($sender->user_type === 'company' ? 'Company Admin' : ($sender->f_name ?? $sender->email));

            $this->lastMessages['group'] = $senderName . ': ' . $groupMsg->message;
            $this->lastMessageTimes['group'] = $groupMsg->created_at;

            // unread count for group
            $this->unreadCounts['group'] = ChatMessage::where('company_id', $companyId)
                ->whereNull('receiver_id')
                ->where('sender_id', '!=', auth()->id())
                ->whereDoesntHave('reads', function ($q) {
                    $q->where('user_id', auth()->id());
                })
                ->count();
        } else {
            $this->lastMessages['group'] = null;
            $this->lastMessageTimes['group'] = null;
            $this->unreadCounts['group'] = 0;
        }

        // Personal messages
        foreach ($this->chatUsers as $user) {
            $lastMsg = ChatMessage::where('company_id', $companyId)
                ->where(function ($q) use ($user) {
                    $q->where('sender_id', auth()->id())->where('receiver_id', $user->id)
                        ->orWhere('sender_id', $user->id)->where('receiver_id', auth()->id());
                })
                ->latest('id')
                ->first();

            if ($lastMsg) {
                $sender = $lastMsg->sender;
                $senderName = $sender->id === auth()->id()
                    ? 'Me'
                    : ($sender->user_type === 'company' ? 'Company Admin' : ($sender->f_name ?? $sender->email));

                $this->lastMessages[$user->id] = $senderName . ': ' . $lastMsg->message;
                $this->lastMessageTimes[$user->id] = $lastMsg->created_at;


                $this->unreadCounts[$user->id] = ChatMessage::where('company_id', $companyId)
                    ->where('sender_id', $user->id)
                    ->where('receiver_id', auth()->id())
                    ->whereDoesntHave('reads', function ($q) {
                        $q->where('user_id', auth()->id());
                    })
                    ->count();
            } else {
                $this->lastMessages[$user->id] = null;
                $this->lastMessageTimes[$user->id] = null;
                $this->unreadCounts[$user->id] = 0;
            }
        }
    }


    public function sortChatUsersByLastMessage()
    {
        $this->chatUsers = $this->chatUsers->sortByDesc(function ($user) {
            return $this->lastMessageTimes[$user->id] ?? null;
        })->values();
    }


    public function loadMore()
    {
        $this->page++;
        $this->loadMessages(false);
    }


    public function updatedAttachment()
    {
        if ($this->attachment) {
            // Open modal when a file is selected
            $this->showAttachmentModal = true;
        }
    }

    public function sendAttachmentMessage()
    {
        if (!$this->attachment) return;

        $this->attachmentSending = true;


        $path = $this->attachment->store('chat/attachments', 'public');

        $tempPath = public_path('storage/chat/attachments/tmp/' . $this->attachment->getClientOriginalName());
        if (file_exists($tempPath)) {
            unlink($tempPath);
        }


        $msg = ChatMessage::create([
            'company_id'  => currentCompanyId(),
            'sender_id'   => auth()->id(),
            'receiver_id' => $this->receiverId === 'group' ? null : $this->receiverId,
            'message'     => $this->messageText,
            'media_path'  => $path,
        ]);


        broadcast(new MessageSent($msg))->toOthers();

        $this->messages->push($msg);

        $this->messageText = "";

        $this->loadConversationUsers();
        $this->attachmentSending = false;
        $this->showAttachmentModal = false;
        $this->loadLastMessages();
        $this->loadMessages();
        $this->sortChatUsersByLastMessage();
        $this->dispatch('scrollToBottom');
    }

    public function cancelAttachment()
    {
        if ($this->attachment) {

            $this->attachment->delete();


            $this->attachment = null;
        }

        $this->showAttachmentModal = false;
    }
}
