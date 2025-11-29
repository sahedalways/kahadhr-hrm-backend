<?php

namespace App\Livewire\Backend\Chat;

use App\Events\MessageSent;
use App\Livewire\Backend\Components\BaseComponent;
use App\Models\ChatMessage;


class ChatIndex extends BaseComponent
{
    public $messages;
    public $messageText;
    public $tab = 'all';
    public $searchTerm = '';
    public $chatUsers;
    public $receiverId = 'group'; // default to group chat

    protected $listeners = ['incomingMessage' => 'loadMessages'];

    public function mount()
    {
        $this->chatUsers = auth()->user()->company->employees()->get();
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
}
