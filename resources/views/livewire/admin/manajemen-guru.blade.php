<div>
    <div class="page-header">
        <h1><i class="bi bi-people-fill me-2"></i>Manajemen Guru</h1>
    </div>

    {{-- Search & Filter --}}
    <div class="card mb-3">
        <div class="card-body p-3">
            <div class="row g-2">
                <div class="col-8">
                    <input type="search" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Cari nama/email...">
                </div>
                <div class="col-4">
                    <select wire:model.live="filterRole" class="form-select">
                        <option value="">Semua Role</option>
                        <option value="guru">Guru</option>
                        <option value="admin">Admin</option>
                        <option value="kepsek">Kepsek</option>
                        <option value="waka">Waka</option>
                        <option value="ketua_mgmp">Ketua MGMP</option>
                        <option value="ketua_kelas">Ketua Kelas</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Button --}}
    <button wire:click="create" class="btn btn-primary mb-3">
        <i class="bi bi-plus-circle me-1"></i> Tambah Guru
    </button>

    {{-- Form Modal --}}
    @if($showForm)
    <div class="card mb-3 border-primary animate-fade-in-up">
        <div class="card-header bg-primary text-white">
            <i class="bi bi-{{ $editing ? 'pencil' : 'plus-circle' }} me-2"></i>{{ $editing ? 'Edit' : 'Tambah' }} Guru
        </div>
        <div class="card-body">
            <form wire:submit="save">
                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" wire:model="nama" class="form-control @error('nama') is-invalid @enderror">
                    @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" wire:model="email" class="form-control @error('email') is-invalid @enderror">
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Password {{ $editing ? '(kosongkan jika tidak diubah)' : '' }}</label>
                    <input type="password" wire:model="password" class="form-control @error('password') is-invalid @enderror">
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select wire:model="role" class="form-select">
                        <option value="guru">Guru</option>
                        <option value="admin">Admin</option>
                        <option value="kepsek">Kepala Sekolah</option>
                        <option value="waka">Waka</option>
                        <option value="ketua_mgmp">Ketua MGMP</option>
                        <option value="ketua_kelas">Ketua Kelas</option>
                    </select>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill"><i class="bi bi-check"></i> Simpan</button>
                    <button type="button" wire:click="$set('showForm', false)" class="btn btn-outline-secondary">Batal</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Delete Confirmation --}}
    @if($confirmDelete)
    <div class="alert alert-danger animate-fade-in-up">
        <strong>Hapus "{{ $deleteName }}"?</strong>
        <p class="small mb-2">Data jadwal guru terkait juga akan dihapus (cascade).</p>
        <div class="d-flex gap-2">
            <button wire:click="deleteUser" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i> Hapus</button>
            <button wire:click="$set('confirmDelete', false)" class="btn btn-outline-secondary btn-sm">Batal</button>
        </div>
    </div>
    @endif

    {{-- Table --}}
    <div class="card">
        <div class="card-body p-0">
            @foreach($users as $user)
            <div class="d-flex align-items-center gap-3 p-3 border-bottom">
                <div class="flex-fill">
                    <div class="fw-bold small">{{ $user->name }}</div>
                    <div class="text-muted" style="font-size: 0.75rem;">{{ $user->email }}</div>
                </div>
                <span class="badge bg-{{ match($user->role) { 'admin' => 'danger', 'kepsek' => 'primary', 'waka' => 'info', 'ketua_mgmp' => 'success', 'guru' => 'secondary', 'ketua_kelas' => 'warning' } }} bg-opacity-10 text-{{ match($user->role) { 'admin' => 'danger', 'kepsek' => 'primary', 'waka' => 'info', 'ketua_mgmp' => 'success', 'guru' => 'secondary', 'ketua_kelas' => 'warning' } }}" style="font-size: 0.7rem;">
                    {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                </span>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="dropdown" style="min-height: 36px;">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><button class="dropdown-item" wire:click="edit('{{ $user->id }}')"><i class="bi bi-pencil me-2"></i>Edit</button></li>
                        <li><button class="dropdown-item" wire:click="resetPassword('{{ $user->id }}')"><i class="bi bi-key me-2"></i>Reset Password</button></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><button class="dropdown-item text-danger" wire:click="confirmDeleteUser('{{ $user->id }}')"><i class="bi bi-trash me-2"></i>Hapus</button></li>
                    </ul>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="mt-3">{{ $users->links() }}</div>
</div>
