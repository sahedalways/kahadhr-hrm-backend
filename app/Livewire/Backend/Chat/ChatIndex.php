<?php

namespace App\Livewire\Backend\Chat;

use App\Events\MessageSent;
use App\Events\UserTyping;
use App\Livewire\Backend\Components\BaseComponent;
use App\Models\ChatMessage;
use App\Models\Employee;
use App\Models\User;

class ChatIndex extends BaseComponent
{
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

    protected $listeners = ['incomingMessage'];



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

    public function loadMessages()
    {
        if ($this->receiverId === 'group') {
            // Group chat: all messages from company
            $this->messages = ChatMessage::where('company_id', currentCompanyId())
                ->whereNull('receiver_id')
                ->orderBy('id', 'asc')
                ->get();
        } else {
            // Personal chat
            $this->messages = ChatMessage::where('company_id', currentCompanyId())
                ->where(function ($q) {
                    $q->where('sender_id', auth()->id())
                        ->where('receiver_id', $this->receiverId)
                        ->orWhere('sender_id', $this->receiverId)
                        ->where('receiver_id', auth()->id());
                })
                ->orderBy('id', 'asc')
                ->get();
        }
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
            $this->mentionUsers = auth()->user()->company
                ? auth()->user()->company->employees()
                ->where('user_id', '!=', auth()->id())
                ->where('is_active', 1)
                ->get()
                : collect();
        }
    }

    // Insert mention
    public function selectMention($id)
    {
        $employee = collect($this->mentionUsers)->firstWhere('id', $id);
        if (!$employee) return;


        $displayName = trim(($employee->f_name ?? '') . ' ' . ($employee->l_name ?? ''));
        if (empty($displayName)) {
            $displayName = $employee->email ?? 'Unknown';
        }

        $mentionText = '<span style="color: #0d6efd">@' . $displayName . '</span>&nbsp;';

        $this->dispatch('insert-mention', ['html' => $mentionText]);
        $this->showMentionBox = false;
    }


    public function startNewChat($userId)
    {
        $this->receiverId = $userId;

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
            ->filter(fn($id) => $id != auth()->id())
            ->values();

        $this->chatUsers = User::withoutGlobalScopes()
            ->with(['employee' => function ($q) {
                $q->withoutGlobalScopes();
            }])
            ->whereIn('id', $userIds)
            ->get();
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
        $this->lastMessageTimes = []; // track timestamps for sorting

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
        } else {
            $this->lastMessages['group'] = null;
            $this->lastMessageTimes['group'] = null;
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
            } else {
                $this->lastMessages[$user->id] = null;
                $this->lastMessageTimes[$user->id] = null;
            }
        }
    }

    public function sortChatUsersByLastMessage()
    {
        $this->chatUsers = $this->chatUsers->sortByDesc(function ($user) {
            return $this->lastMessageTimes[$user->id] ?? null;
        })->values();
    }
}
