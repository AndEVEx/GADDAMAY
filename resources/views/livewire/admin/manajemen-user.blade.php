<div>
    <div class="page-header">
        <h1><i class="bi bi-people-fill me-2"></i>Manajemen User</h1>
    </div>

    {{-- Import Section --}}
    <div class="card mb-3 animate-fade-in-up">
        <div class="card-header bg-success bg-opacity-10">
            <h6 class="mb-0"><i class="bi bi-cloud-upload me-2"></i>Import Data User</h6>
        </div>
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2 mb-3">
                <button wire:click="downloadTemplate" class="btn btn-outline-info" style="min-height: 48px;">
                    <i class="bi bi-download me-2"></i>Download Template (.xlsx)
                </button>
                <button wire:click="exportExcel" class="btn btn-outline-success" style="min-height: 48px;">
                    <i class="bi bi-file-earmark-excel me-2"></i>Export Excel (.xlsx)
                </button>
            </div>
            <div class="mb-3">
                <input type="file" wire:model="importFile" accept=".xlsx,.xls,.csv" class="form-control" style="min-height: 48px;">
            </div>
            <button wire:click="importData" class="btn btn-success w-100" style="min-height: 48px;" {{ !$importFile ? 'disabled' : '' }}>
                <span wire:loading.remove wire:target="importData"><i class="bi bi-cloud-upload me-2"></i>Import User</span>
                <span wire:loading wire:target="importData"><span class="spinner-border spinner-border-sm me-2"></span>Mengimport...</span>
            </button>
        </div>
    </div>

    {{-- Search & Filter --}}
    <div class="card mb-3 animate-fade-in-up">
        <div class="card-body p-3">
            <div class="row g-2">
                <div class="col-8">
                    <input type="search" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Cari nama atau email..." style="min-height: 48px;">
                </div>
                <div class="col-4">
                    <select wire:model.live="filterRole" class="form-select" style="min-height: 48px;">
                        <option value="">Semua Role</option>
                        <option value="admin">Admin</option>
                        <option value="kepsek">Kepsek</option>
                        <option value="waka">Waka</option>
                        <option value="ketua_mgmp">Ketua MGMP</option>
                        <option value="guru">Guru</option>
                        <option value="ketua_kelas">Ketua Kelas</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Add & Export Buttons --}}
    <div class="d-flex gap-2 mb-3 flex-wrap">
        <button wire:click="create" class="btn btn-primary" style="min-height: 48px;">
            <i class="bi bi-plus-circle me-1"></i> Tambah User
        </button>
        <button wire:click="exportExcel" class="btn btn-outline-success" style="min-height: 48px;">
            <i class="bi bi-file-earmark-excel me-2"></i>Export Excel
        </button>
    </div>

    {{-- Form Modal / Card --}}
    @if($showForm)
    <div class="card mb-3 border-primary animate-fade-in-up">
        <div class="card-header bg-primary text-white">
            <i class="bi bi-{{ $editing ? 'pencil' : 'plus-circle' }} me-2"></i>{{ $editing ? 'Edit' : 'Tambah' }} User
        </div>
        <div class="card-body">
            <form wire:submit="save">
                <div class="mb-3">
                    <label class="form-label font-weight-bold">Nama Lengkap</label>
                    <input type="text" wire:model="nama" class="form-control @error('nama') is-invalid @enderror" placeholder="Nama user" style="min-height: 48px;">
                    @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label font-weight-bold">Email</label>
                    <input type="email" wire:model="email" class="form-control @error('email') is-invalid @enderror" placeholder="user@smkn2indramayu.sch.id" style="min-height: 48px;">
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label font-weight-bold">Password {{ $editing ? '(kosongkan jika tidak diubah)' : '' }}</label>
                    <input type="password" wire:model="password" class="form-control @error('password') is-invalid @enderror" placeholder="******" style="min-height: 48px;">
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label font-weight-bold">Role Hak Akses</label>
                    <select wire:model="role" class="form-select @error('role') is-invalid @enderror" style="min-height: 48px;">
                        <option value="guru">Guru</option>
                        <option value="admin">Admin</option>
                        <option value="kepsek">Kepala Sekolah</option>
                        <option value="waka">Waka</option>
                        <option value="ketua_mgmp">Ketua MGMP</option>
                        <option value="ketua_kelas">Ketua Kelas</option>
                    </select>
                    @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill" style="min-height: 48px;"><i class="bi bi-check me-1"></i> Simpan</button>
                    <button type="button" wire:click="$set('showForm', false)" class="btn btn-outline-secondary" style="min-height: 48px;">Batal</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Delete Confirmation --}}
    @if($confirmDelete)
    <div class="alert alert-danger animate-fade-in-up">
        <strong>Hapus user "{{ $deleteName }}"?</strong>
        <p class="small mb-2">Data jadwal & perannya di sistem akan ikut dihapus.</p>
        <div class="d-flex gap-2">
            <button wire:click="deleteUser" class="btn btn-danger" style="min-height: 48px;"><i class="bi bi-trash me-1"></i> Hapus</button>
            <button wire:click="$set('confirmDelete', false)" class="btn btn-outline-secondary" style="min-height: 48px;">Batal</button>
        </div>
    </div>
    @endif

    {{-- User List --}}
    <div class="card animate-fade-in-up">
        <div class="card-body p-0">
            @forelse($users as $user)
            <div class="d-flex align-items-center gap-3 p-3 border-bottom">
                <div class="flex-fill">
                    <div class="fw-bold fs-6">{{ $user->name }}</div>
                    <div class="text-muted small">{{ $user->email }}</div>
                </div>
                <span class="badge bg-{{ match($user->role) { 'admin' => 'danger', 'kepsek' => 'primary', 'waka' => 'info', 'ketua_mgmp' => 'success', 'guru' => 'secondary', 'ketua_kelas' => 'warning' } }} bg-opacity-10 text-{{ match($user->role) { 'admin' => 'danger', 'kepsek' => 'primary', 'waka' => 'info', 'ketua_mgmp' => 'success', 'guru' => 'secondary', 'ketua_kelas' => 'warning' } }}">
                    {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                </span>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="dropdown" style="min-height: 48px; min-width: 48px;">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><button class="dropdown-item py-2" wire:click="edit('{{ $user->id }}')"><i class="bi bi-pencil me-2"></i>Edit</button></li>
                        <li><button class="dropdown-item py-2" wire:click="resetPassword('{{ $user->id }}')"><i class="bi bi-key me-2"></i>Reset Password</button></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><button class="dropdown-item text-danger py-2" wire:click="confirmDeleteUser('{{ $user->id }}')"><i class="bi bi-trash me-2"></i>Hapus</button></li>
                    </ul>
                </div>
            </div>
            @empty
            <div class="p-4 text-center text-muted">
                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                Belum ada data user.
            </div>
            @endforelse
        </div>
    </div>

    <div class="mt-3">{{ $users->links() }}</div>
</div>
