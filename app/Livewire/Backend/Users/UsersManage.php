<?php

namespace App\Livewire\Backend\Users;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Validation\Rule;


class UsersManage extends BaseComponent
{
    public $users, $user,  $user_id, $first_name, $last_name, $email, $phone_no, $password, $password_confirmation, $is_active = true, $search;

    public $perPage = 10;
    public $loaded;
    public $lastId = null;
    public $hasMore = true;
    public $editMode = false;


    protected $userService;

    protected $listeners = ['deleteUser'];


    public function boot(UserService $userService)
    {
        $this->userService = $userService;
    }


    protected $rules = [
        'first_name' => 'required|string|max:100',
        'last_name'  => 'required|string|max:100',
        'email'      => 'required|email|unique:users,email',
        'phone_no' => 'required|string|max:20|unique:users,phone_no',
        'password'   => 'required|min:8|confirmed',
        'password_confirmation' => 'required|min:8',
        'is_active'  => 'boolean',
    ];



    public function mount()
    {
        $this->loaded = collect();
        $this->loadMore();
    }


    public function render()
    {
        return view('livewire.backend.users.users-manage', [
            'infos' => $this->loaded
        ]);
    }





    /* reset input file */
    public function resetInputFields()
    {
        $this->user = '';
        $this->email = '';
        $this->phone_no = '';
        $this->first_name = '';
        $this->last_name = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->is_active = true;
        $this->resetErrorBag();
    }
    /* store User data */
    public function store()
    {

        $this->validate();

        $this->userService->register([
            'first_name' => $this->first_name,
            'last_name'  => $this->last_name,
            'email'      => $this->email,
            'phone_no'   => $this->phone_no,
            'password'   => $this->password,
            'is_active'  => $this->is_active,
        ]);

        $this->users =  $this->userService->getAllUsers();

        $this->resetInputFields();
        $this->dispatch('closemodal');

        $this->toast('User registered Successfully!', 'success');
        $this->resetLoaded();
    }







    /* view User details to update */
    public function edit($id)
    {
        $this->editMode = true;
        $this->user = $this->userService->getUser($id);

        if (!$this->user) {
            $this->toast('User not found!', 'error');
            return;
        }

        $this->email = $this->user->email;
        $this->first_name = $this->user->f_name;
        $this->last_name = $this->user->l_name;
        $this->phone_no = $this->user->phone_no;
        $this->is_active = (bool) $this->user->is_active;
    }


    /* update user details */
    public function update()
    {
        $this->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->user->id),
            ],
            'phone_no'   => [
                'required',
                'string',
                'max:20',
                Rule::unique('users', 'phone_no')->ignore($this->user->id),
            ],
            'password'   => 'nullable|string|min:6|confirmed',
        ]);

        if (!$this->user) {
            $this->toast('User not found!', 'error');
            return;
        }
        $this->userService->updateUser($this->user, [
            'first_name' => $this->first_name,
            'last_name'  => $this->last_name,
            'email'      => $this->email,
            'phone_no'   => $this->phone_no,
            'password'   => $this->password,
            'is_active'  => $this->is_active,
        ]);


        $this->resetInputFields();
        $this->editMode = false;

        $this->dispatch('closemodal');
        $this->toast('User has been updated!', 'success');
        $this->resetLoaded();
    }


    /* process while update */
    public function searchUsers()
    {
        $this->resetLoaded();
    }



    // Load more function
    public function loadMore()
    {
        if (!$this->hasMore) return;

        $query = User::where('user_type', 'user');

        if ($this->search && $this->search != '') {
            $searchTerm = '%' . $this->search . '%';

            $query->where(function ($q) use ($searchTerm) {
                $q->where('f_name', 'like', $searchTerm)
                    ->orWhere('l_name', 'like', $searchTerm)
                    ->orWhere('email', 'like', $searchTerm)
                    ->orWhere('phone_no', 'like', $searchTerm);
            });
        }

        if ($this->lastId) {
            $query->where('id', '<', $this->lastId);
        }

        $items = $query->orderBy('id', 'desc')
            ->limit($this->perPage)
            ->get();

        if ($items->count() == 0) {
            $this->hasMore = false;
            return;
        }

        if ($items->count() < $this->perPage) {
            $this->hasMore = false;
        }

        $this->lastId = $items->last()->id;
        $this->loaded = $this->loaded->merge($items);
    }


    // Reset loaded collection
    private function resetLoaded()
    {
        $this->loaded = collect();
        $this->lastId = null;
        $this->hasMore = true;
        $this->loadMore();
    }



    public function deleteUser($id)
    {
        $this->userService->deleteUser($id);


        $this->toast('User has been deleted!', 'success');
        $this->resetLoaded();
    }


    public function toggleActive($id)
    {
        $item = $this->userService->getUser($id);

        if (!$item) {
            $this->toast('User not found!', 'error');
            return;
        }

        $item->is_active = $item->is_active ? 0 : 1;
        $item->save();

        $this->users =  $this->userService->getAllUsers();


        $this->toast('Status updated successfully!', 'success');
        $this->resetLoaded();
    }
}
