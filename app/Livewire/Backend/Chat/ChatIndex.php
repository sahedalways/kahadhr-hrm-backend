<?php

namespace App\Livewire\Backend\Chat;

use App\Events\MessageSent;
use App\Events\UserTyping;
use App\Livewire\Backend\Components\BaseComponent;
use App\Models\ChatGroup;
use App\Models\ChatMessage;
use App\Models\ChatMessageRead;
use App\Models\Employee;
use App\Models\Team;
use App\Models\User;
use Livewire\WithFileUploads;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

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

    public $teamStep = 1;
    public $teamName = '';
    public $teamDescription = '';
    public $teamImage;
    public $teamMemberSearch = '';
    public $teamMemberList;
    public $selectedTeamMembers = [];
    public $existingTeams = [];
    public $selectedTeamMembersList = [];
    public $teamGroups;
    public $selectedTeamId = null;

    public $manualTeam = false;


    protected $rules = [
        'attachment' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,mp4,mov,avi,pdf,doc,docx,xls,xlsx,txt',
    ];



    public function mount()
    {
        // Empty collection default
        $this->chatUsers = collect();
        $this->teamGroups = collect();

        $this->loadConversationUsers();
        $this->loadNewChatUsers();
        $this->loadLastMessages();
        $this->loadTeamGroups();
        $this->loadCompanyTeams();
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

        $query = ChatMessage::where('company_id', currentCompanyId());

        if ($this->receiverId === 'group') {
            $query->whereNull('receiver_id')
                ->whereNull('team_id');
        } elseif (str_starts_with($this->receiverId, 'teamGroup_')) {
            $groupId = intval(str_replace('teamGroup_', '', $this->receiverId));
            $query->where('team_id', $groupId);
        } else {
            $query->where(function ($q) {
                $q->where('sender_id', auth()->id())
                    ->where('receiver_id', $this->receiverId)
                    ->orWhere('sender_id', $this->receiverId)
                    ->where('receiver_id', auth()->id());
            });
        }

        $query->orderBy('id', 'desc');

        $this->totalMessages = $query->count();

        $messages = $query->skip(($this->page - 1) * $this->perPage)
            ->take($this->perPage)
            ->get()
            ->reverse();

        foreach ($messages as $msg) {
            if (
                ($this->receiverId === 'group' && $msg->sender_id !== auth()->id()) ||
                (str_starts_with($this->receiverId, 'teamGroup_') && $msg->sender_id !== auth()->id()) ||
                ($this->receiverId !== 'group' && !str_starts_with($this->receiverId, 'teamGroup_') && $msg->sender_id === $this->receiverId)
            ) {
                ChatMessageRead::updateOrCreate(
                    [
                        'message_id' => $msg->id,
                        'user_id' => auth()->id()
                    ],
                    ['read_at' => now()]
                );
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


        $data = [
            'company_id' => currentCompanyId(),
            'sender_id'  => auth()->id(),
            'message'    => $this->messageText,
        ];

        if ($this->receiverId === 'group') {
            $data['receiver_id'] = null;
            $data['team_id'] = null;
        } elseif (str_starts_with($this->receiverId, 'teamGroup_')) {
            $groupId = intval(str_replace('teamGroup_', '', $this->receiverId));
            $data['team_id'] = $groupId;
            $data['receiver_id'] = null;
        } else {
            $data['receiver_id'] = $this->receiverId;
            $data['team_id'] = null;
        }

        $msg = ChatMessage::create($data);

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

        if (!$msg || $this->messages->contains('id', $msg->id)) {
            return;
        }

        $receiver = $this->receiverId;
        $isCurrentChat = false;


        if ($msg->receiver_id === null && $msg->team_id === null && $receiver === 'group') {
            $isCurrentChat = true;
        }

        if ($msg->team_id !== null && $receiver === 'teamGroup_' . $msg->team_id) {
            $isCurrentChat = true;
        }


        if ($msg->receiver_id == auth()->id() && $receiver == $msg->sender_id) {
            $isCurrentChat = true;
        }



        ChatMessageRead::updateOrCreate(
            [
                'message_id' => $msg->id,
                'user_id' => auth()->id()
            ],
            ['read_at' => now()]
        );




        $this->loadConversationUsers();
        $this->loadLastMessages();
        $this->sortChatUsersByLastMessage();

        if (!$isCurrentChat) {
            return;
        }


        $this->messages->push($msg);
        $this->dispatch('scrollToBottom');
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

    public function startNewChat($id)
    {
        $this->receiverId = $id;
        $this->messageText = '';

        if ($id === 'group') {
            // All users group chat
            $this->receiverInfo = [
                'type'     => 'group',
                'name'     => "All users' team chat",
                'subtitle' => "All employees & company can see messages",
                'photo'    => asset('/assets/img/chat/group-icon.png'),
            ];
        } elseif (str_starts_with($id, 'teamGroup_')) {
            $groupId = intval(str_replace('teamGroup_', '', $id));
            $group = ChatGroup::find($groupId);

            if (!$group) return;

            $this->receiverInfo = [
                'type'     => 'teamGroup',
                'name'     => $group->name,
                'subtitle' => "Team members chat",
                'photo'    => $group->image ? asset($group->image_url) : asset('/assets/img/chat/group-icon.png'),
            ];
        } else {
            $user = User::withoutGlobalScopes()->find($id);
            $this->markAsReadPersonal($id);
            if ($user) {
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
        }

        $this->loadMessages();
    }




    public function markAsReadPersonal($userId)
    {
        ChatMessage::where('company_id', currentCompanyId())
            ->where('sender_id', $userId)
            ->where('receiver_id', auth()->id())
            ->where('is_read', 0)
            ->update(['is_read' => 1]);

        $this->unreadCounts[$userId] = 0;
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

    public function loadCompanyTeams()
    {
        if (auth()->user()->user_type === 'company') {
            $this->existingTeams = Team::where('company_id', auth()->user()->company->id)->get();
        } else {
            $this->existingTeams = collect();
        }
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


        /* ----------------------
       GROUP MESSAGES
    -----------------------*/
        $groupMsg = ChatMessage::where('company_id', $companyId)
            ->whereNull('receiver_id')
            ->whereNull('team_id')
            ->latest('id')
            ->first();

        if ($groupMsg) {
            $sender = $groupMsg->sender;
            $senderName = $sender->id === auth()->id()
                ? 'Me'
                : ($sender->user_type === 'company' ? 'Company Admin' : ($sender->f_name ?? $sender->email));

            $this->lastMessages['group'] = $senderName . ': ' . $groupMsg->message;
            $this->lastMessageTimes['group'] = $groupMsg->created_at;

            // unread count from chat_message_reads table
            $this->unreadCounts['group'] = ChatMessage::where('company_id', $companyId)
                ->whereNull('receiver_id')
                ->whereNull('team_id')
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


        /* ----------------------
       TEAM GROUP MESSAGES
    -----------------------*/
        foreach ($this->teamGroups as $group) {

            $key = 'teamGroup_' . $group->id;

            $lastMsg = ChatMessage::where('company_id', $companyId)
                ->where('team_id', $group->id)
                ->latest('id')
                ->first();

            if ($lastMsg) {
                $sender = $lastMsg->sender;
                $senderName = $sender->id === auth()->id()
                    ? 'Me'
                    : ($sender->user_type === 'company' ? 'Company Admin' : ($sender->f_name ?? $sender->email));

                $this->lastMessages[$key]  = $senderName . ': ' . $lastMsg->message;
                $this->lastMessageTimes[$key] = $lastMsg->created_at;

                // unread from chat_message_reads table
                $this->unreadCounts[$key] = ChatMessage::where('company_id', $companyId)
                    ->where('team_id', $group->id)
                    ->where('sender_id', '!=', auth()->id())
                    ->whereDoesntHave('reads', function ($q) {
                        $q->where('user_id', auth()->id());
                    })
                    ->count();
            } else {
                $this->lastMessages[$key] = null;
                $this->lastMessageTimes[$key] = null;
                $this->unreadCounts[$key] = 0;
            }
        }


        /* ----------------------
       PERSONAL MESSAGES
    -----------------------*/
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

                // PERSONAL unread uses is_read column
                $this->unreadCounts[$user->id] = ChatMessage::where('company_id', $companyId)
                    ->where('sender_id', $user->id)
                    ->where('receiver_id', auth()->id())
                    ->where('is_read', 0)
                    ->count();
                if ($this->receiverId == $user->id) {
                    $this->markAsReadPersonal($user->id);
                }
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



    public function updatedMentionSearch()
    {
        $this->searchMentionUsers();
    }

    public function searchMentionUsers()
    {
        if (!$this->newChatUsers) {
            $this->loadNewChatUsers();
        }

        $search = strtolower($this->mentionSearch);


        $this->mentionUsers = $this->newChatUsers->filter(function ($user) use ($search) {
            $name = strtolower(trim(($user->f_name ?? '') . ' ' . ($user->l_name ?? '')));
            $email = strtolower($user->email ?? '');

            return str_contains($name, $search) || str_contains($email, $search);
        })->take(20)->values();
    }


    public function openNewTeamModal()
    {
        $this->teamStep = 1;
        $this->teamName = '';
        $this->teamDescription = '';
        $this->teamImage = null;
        $this->selectedTeamMembers = [];


        $this->teamMemberList = $this->newChatUsers;
    }


    public function nextTeamStep()
    {

        $rules = [
            'teamName' => 'required|string|min:3|max:75',
            'teamDescription' => 'nullable|string|max:255',
            'teamImage'       => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];

        $data = [
            'teamName' => $this->teamName,
            'teamDescription' => $this->teamDescription,
            'teamImage' => $this->teamImage,
        ];

        $validator = Validator::make($data, $rules);
        $validator->validate();




        $this->teamStep = 2;
    }



    public function prevTeamStep()
    {
        $this->resetValidation(['teamName', 'teamDescription', 'teamImage']);

        $this->teamStep = 1;
    }


    public function updatedTeamMemberSearch()
    {
        $search = strtolower($this->teamMemberSearch);

        $this->teamMemberList = $this->newChatUsers->filter(function ($user) use ($search) {
            $name = strtolower(trim(($user->f_name ?? '') . ' ' . ($user->l_name ?? '')));
            $email = strtolower($user->email ?? '');

            return str_contains($name, $search) || str_contains($email, $search);
        })->values();
    }



    public function createTeam()
    {
        $data = [
            'teamName' => $this->teamName,
            'teamDescription' => $this->teamDescription,
            'teamImage' => $this->teamImage,
            'selectedTeamMembers' => $this->selectedTeamMembers,
            'selectedTeamId' => $this->selectedTeamId,
            'manualTeam' => $this->manualTeam,
        ];

        $rules = [
            'teamName' => 'required|string|min:3|max:75',
            'teamDescription' => 'nullable|string|max:255',
            'teamImage' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];

        $validator = Validator::make($data, $rules);


        $validator->sometimes('selectedTeamMembers', 'required|array|min:1', function ($input) {
            return $input->manualTeam == true;
        });

        $validator->sometimes('selectedTeamId', 'required', function ($input) {
            return $input->manualTeam == false;
        });

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }


        $imagePath = null;
        if ($this->teamImage instanceof UploadedFile) {
            $imagePath = uploadImage($this->teamImage, 'chat/group/image', null);
        }


        if ($this->manualTeam) {

            $group = ChatGroup::create([
                'company_id' => auth()->user()->company->id,
                'name' => $this->teamName,
                'created_by' => auth()->id(),
                'desc' => $this->teamDescription,
                'image' => $imagePath,
            ]);

            $group->members()->sync($this->selectedTeamMembers);
        } else {
            $this->selectedTeamMembers = Employee::where('team_id', $this->selectedTeamId)
                ->pluck('user_id')
                ->toArray();


            $group = ChatGroup::create([
                'company_id' => auth()->user()->company->id,
                'name' => $this->teamName,
                'created_by' => auth()->id(),
                'desc' => $this->teamDescription,
                'image' => $imagePath,
                'team_id' => $this->selectedTeamId,
            ]);

            $group->members()->sync($this->selectedTeamMembers);
        }

        $this->reset(['teamStep', 'teamName', 'teamDescription', 'teamImage', 'selectedTeamMembers', 'selectedTeamId']);
        $this->teamStep = 1;



        $this->startNewChat('teamGroup_' . $group->id);
        $this->loadTeamGroups();

        $this->toast("Team created successfully.", 'success');
        $this->dispatch('closemodal');
    }


    public function addTeamMember($memberId)
    {
        if (!in_array($memberId, $this->selectedTeamMembers)) {
            $this->selectedTeamMembers[] = $memberId;

            $member = $this->newChatUsers->firstWhere('id', $memberId);
            if ($member) {
                $this->selectedTeamMembersList[] = $member;
            }
        }
    }

    public function removeTeamMember($memberId)
    {
        $this->selectedTeamMembers = array_filter($this->selectedTeamMembers, fn($id) => $id != $memberId);
        $this->selectedTeamMembersList = array_filter($this->selectedTeamMembersList, fn($member) => $member->id != $memberId);
    }


    public function loadTeamGroups()
    {
        $user = auth()->user();

        if (in_array($user->user_type, ['employee', 'teamLead'])) {
            $this->teamGroups = ChatGroup::whereHas('members', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
                ->where('company_id', currentCompanyId())
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $this->teamGroups = ChatGroup::where('company_id', currentCompanyId())
                ->orderBy('created_at', 'desc')
                ->get();
        }
    }
}
