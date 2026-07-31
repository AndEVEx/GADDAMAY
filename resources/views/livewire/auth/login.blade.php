<div class="animate-fade-in-up">
    <div class="card border-0 shadow-lg" style="border-radius: 1.5rem;">
        <div class="card-body p-4">
            {{-- Logo & Title --}}
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 rounded-circle mb-3" style="width: 72px; height: 72px;">
                    <i class="bi bi-journal-bookmark-fill text-primary" style="font-size: 2rem;"></i>
                </div>
                <h1 class="h4 fw-bold text-dark mb-1">Agenda Guru</h1>
                <p class="text-muted small">SMKN 2 Indramayu</p>
            </div>

            {{-- Login Form --}}
            <form wire:submit="login">
                {{-- Email --}}
                <div class="mb-3">
                    <label for="email" class="form-label">
                        <i class="bi bi-envelope me-1"></i>Email
                    </label>
                    <input type="email" id="email" class="form-control @error('email') is-invalid @enderror"
                           wire:model="email" placeholder="nama@smkn2indramayu.sch.id" autofocus>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="mb-3">
                    <label for="password" class="form-label">
                        <i class="bi bi-lock me-1"></i>Password
                    </label>
                    <input type="password" id="password" class="form-control @error('password') is-invalid @enderror"
                           wire:model="password" placeholder="Masukkan password">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Remember --}}
                <div class="form-check mb-4">
                    <input type="checkbox" id="remember" class="form-check-input" wire:model="remember">
                    <label for="remember" class="form-check-label small">Ingat saya</label>
                </div>

                {{-- Submit --}}
                <button type="submit" class="btn btn-primary w-100" wire:loading.attr="disabled">
                    <span wire:loading.remove>
                        <i class="bi bi-box-arrow-in-right"></i> Masuk
                    </span>
                    <span wire:loading>
                        <span class="spinner-border spinner-border-sm me-2"></span> Memproses...
                    </span>
                </button>
            </form>
        </div>
    </div>

    <p class="text-center text-white-50 small mt-3">
        &copy; {{ date('Y') }} SMKN 2 Indramayu
    </p>
</div>
