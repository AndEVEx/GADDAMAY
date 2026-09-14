<div class="animate-fade-in-up">
    <div class="card border-0 shadow-lg" style="border-radius: 1.5rem;">
        <div class="card-body p-4">
            {{-- Logo & Title --}}
            <div class="text-center mb-4">
                <img src="{{ \App\Helpers\LogoHelper::getBase64() }}" alt="Logo SMKN 2 Indramayu" 
                     class="mb-3" style="width: 80px; height: 80px; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                <h1 class="h4 fw-bold text-dark mb-1">AgenDAmay</h1>
                <p class="text-muted small">Menginspirasi Tanpa Henti, Terdata Rapi Setiap Hari</p>
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
                <div class="mb-3" x-data="{ show: false }">
                    <label for="password" class="form-label">
                        <i class="bi bi-lock me-1"></i>Password
                    </label>
                    <div class="input-group">
                        <input :type="show ? 'text' : 'password'" id="password" class="form-control @error('password') is-invalid @enderror"
                               wire:model="password" placeholder="Masukkan password">
                        <button type="button" class="btn btn-outline-secondary d-flex align-items-center" @click="show = !show" tabindex="-1" style="min-width: 48px; min-height: 48px;">
                            <i class="bi" :class="show ? 'bi-eye-slash' : 'bi-eye'"></i>
                        </button>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
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
        Made with every kind of <i class="bi bi-heart-fill text-danger"></i> &copy; 2026 SMKN 2 Indramayu
    </p>
</div>
