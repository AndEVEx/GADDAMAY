<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Support\Facades\Hash;

#[Layout('components.layouts.app')]
#[Title('Manajemen User')]
class ManajemenUser extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';
    public string $filterRole = '';
    public bool $showForm = false;
    public bool $editing = false;
    public string $editId = '';
    public string $nama = '';
    public string $email = '';
    public string $password = '';
    public string $role = 'guru';
    public bool $confirmDelete = false;
    public string $deleteId = '';
    public string $deleteName = '';

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterRole() { $this->resetPage(); }

    public function create()
    {
        $this->reset(['editId', 'nama', 'email', 'password', 'role', 'editing']);
        $this->role = 'guru';
        $this->showForm = true;
    }

    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        $this->editId = $user->id;
        $this->nama = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->role = $user->role;
        $this->editing = true;
        $this->showForm = true;
    }

    public function save()
    {
        $rules = [
            'nama' => 'required|min:2',
            'email' => 'required|email|unique:users,email' . ($this->editing ? ",{$this->editId}" : ''),
            'role' => 'required|in:admin,kepsek,waka,ketua_mgmp,guru,ketua_kelas',
        ];
        if (!$this->editing) {
            $rules['password'] = 'required|min:6';
        }
        $this->validate($rules);

        if ($this->editing) {
            $user = User::findOrFail($this->editId);
            $old = $user->toArray();
            $user->update([
                'name' => $this->nama,
                'email' => $this->email,
                'role' => $this->role,
            ]);
            if ($this->password) {
                $user->update(['password' => Hash::make($this->password)]);
            }
            AuditLogService::logUpdate($user, $old);
            $this->dispatch('show-toast', message: 'User berhasil diperbarui!', type: 'success');
        } else {
            $user = User::create([
                'name' => $this->nama,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'role' => $this->role,
            ]);
            AuditLogService::logCreate($user);
            $this->dispatch('show-toast', message: 'User berhasil ditambahkan!', type: 'success');
        }

        $this->showForm = false;
        $this->reset(['editId', 'nama', 'email', 'password', 'role', 'editing']);
    }

    public function confirmDeleteUser(string $id)
    {
        $user = User::findOrFail($id);
        $this->deleteId = $id;
        $this->deleteName = $user->name;
        $this->confirmDelete = true;
    }

    public function deleteUser()
    {
        $user = User::findOrFail($this->deleteId);
        AuditLogService::logDelete($user);
        $user->jadwalGuru()->delete();
        $user->delete();
        $this->confirmDelete = false;
        $this->dispatch('show-toast', message: 'User berhasil dihapus!', type: 'success');
    }

    public function resetPassword(string $id)
    {
        $user = User::findOrFail($id);
        $user->update(['password' => Hash::make('password123')]);
        $this->dispatch('show-toast', message: "Password user {$user->name} direset ke 'password123'", type: 'info');
    }

    public function render()
    {
        $users = User::when($this->search, function($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            })
            ->when($this->filterRole, fn($q) => $q->where('role', $this->filterRole))
            ->orderBy('name')
            ->paginate(20);

        return view('livewire.admin.manajemen-user', ['users' => $users]);
    }
}
