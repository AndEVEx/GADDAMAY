<div>
    <div class="page-header d-flex align-items-center justify-content-between mb-3">
        <div>
            <h1><i class="bi bi-key-fill me-2 text-warning"></i>Ganti Password</h1>
            <p class="subtitle mb-0">Ubah password akun Anda secara berkala untuk keamanan</p>
        </div>
        <a href="javascript:history.back()" class="btn btn-outline-secondary" style="min-height: 44px; display: inline-flex; align-items: center;">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="card shadow-sm animate-fade-in-up">
                <div class="card-header bg-primary text-white py-3">
                    <h6 class="mb-0"><i class="bi bi-shield-lock me-2"></i>Form Pembaruan Password</h6>
                </div>
                <div class="card-body p-4">
                    <form wire:submit="updatePassword">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Password Saat Ini</label>
                            <input type="password" wire:model="password_lama" class="form-control @error('password_lama') is-invalid @enderror" placeholder="Masukkan password lama Anda" style="min-height: 48px;">
                            @error('password_lama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Password Baru</label>
                            <input type="password" wire:model="password_baru" class="form-control @error('password_baru') is-invalid @enderror" placeholder="Minimal 6 karakter" style="min-height: 48px;">
                            @error('password_baru') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Konfirmasi Password Baru</label>
                            <input type="password" wire:model="password_baru_confirmation" class="form-control" placeholder="Ulangi password baru Anda" style="min-height: 48px;">
                        </div>

                        <button type="submit" class="btn btn-success w-100 fw-bold py-2" style="min-height: 48px;">
                            <span wire:loading.remove wire:target="updatePassword"><i class="bi bi-check-circle me-2"></i>Simpan Password Baru</span>
                            <span wire:loading wire:target="updatePassword"><span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
