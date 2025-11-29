<?php

namespace App\Livewire\Backend\Chat;

use App\Events\MessageSent;
use App\Events\UserTyping;
use App\Livewire\Backend\Components\BaseComponent;
use App\Models\ChatMessage;


class ChatIndex extends BaseComponent
{
    public $messages;
    public $messageText;
    public $tab = 'all';
    public $searchTerm = '';
    public $chatUsers;
    public $receiverId = 'group';

    public $typing = false;
    public $typingUser = null;

    public $showMentionBox = false;
    public $mentionUsers = [];
    public $mentionSearch = '';
    public $selectedMention = null;

    protected $listeners = ['incomingMessage'];



    public function mount()
    {
        // Empty collection default
        $this->chatUsers = collect();

        if (auth()->user()->user_type == "company") {
            if (auth()->user()->company) {
                $this->chatUsers = auth()->user()->company
                    ->employees()
                    ->where('user_id', '!=', auth()->id())
                    ->where('is_active', 1)
                    ->get();
            }
        } else {
            if (auth()->user()->employee && auth()->user()->employee->company) {
                $this->chatUsers = auth()->user()->employee->company
                    ->employees()
                    ->where('user_id', '!=', auth()->id())
                    ->where('is_active', 1)
                    ->get();
            }
        }

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
            $this->dispatch('scrollToBottom');
        }
    }

    public function userTyping()
    {
        if (!$this->receiverId) return;

        if ($this->receiverId === 'group') {
            broadcast(new UserTyping(auth()->user(), 'chat-between-all-users'))->toOthers();
        } else {
            broadcast(new UserTyping(auth()->user(), $this->receiverId))->toOthers();
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
}
