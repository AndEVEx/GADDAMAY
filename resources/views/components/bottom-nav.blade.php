@php $role = Auth::user()->role; @endphp
<nav class="bottom-nav d-lg-none">
    @if(in_array($role, ['guru', 'ketua_mgmp']))
        <a href="{{ route('guru.dashboard') }}" class="nav-item {{ request()->routeIs('guru.dashboard') ? 'active' : '' }}" wire:navigate>
            <i class="bi bi-house-fill"></i>
            <span>Beranda</span>
        </a>
        <a href="{{ route('guru.kktp-hub') }}" class="nav-item {{ request()->routeIs('guru.kktp*') ? 'active' : '' }}" wire:navigate>
            <i class="bi bi-list-check"></i>
            <span>KKTP</span>
        </a>
        <a href="{{ route('guru.izin') }}" class="nav-item {{ request()->routeIs('guru.izin*') ? 'active' : '' }}" wire:navigate>
            <i class="bi bi-calendar-x"></i>
            <span>Izin</span>
        </a>
        <a href="{{ route('guru.jurnal') }}" class="nav-item {{ request()->routeIs('guru.jurnal*') ? 'active' : '' }}" wire:navigate>
            <i class="bi bi-journal-text"></i>
            <span>Jurnal</span>
        </a>
    @elseif($role === 'admin')
        <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" wire:navigate>
            <i class="bi bi-house-fill"></i>
            <span>Beranda</span>
        </a>
        <a href="{{ route('admin.jadwal') }}" class="nav-item {{ request()->routeIs('admin.jadwal') ? 'active' : '' }}" wire:navigate>
            <i class="bi bi-calendar3"></i>
            <span>Jadwal</span>
        </a>
        <a href="{{ route('admin.verifikasi-izin') }}" class="nav-item {{ request()->routeIs('admin.verifikasi-izin') ? 'active' : '' }}" wire:navigate>
            <i class="bi bi-patch-check"></i>
            <span>Izin</span>
        </a>
        <a href="{{ route('monitoring.dashboard') }}" class="nav-item {{ request()->routeIs('monitoring.*') ? 'active' : '' }}" wire:navigate>
            <i class="bi bi-graph-up"></i>
            <span>Monitor</span>
        </a>
    @elseif(in_array($role, ['kepsek', 'waka']))
        <a href="{{ route('monitoring.dashboard') }}" class="nav-item {{ request()->routeIs('monitoring.dashboard') ? 'active' : '' }}" wire:navigate>
            <i class="bi bi-house-fill"></i>
            <span>Monitor</span>
        </a>
        <a href="{{ route('monitoring.progress') }}" class="nav-item {{ request()->routeIs('monitoring.progress') ? 'active' : '' }}" wire:navigate>
            <i class="bi bi-graph-up"></i>
            <span>Progress KKTP</span>
        </a>
        @if($role === 'waka')
        <a href="{{ route('waka.verifikasi-izin') }}" class="nav-item {{ request()->routeIs('waka.verifikasi-izin') ? 'active' : '' }}" wire:navigate>
            <i class="bi bi-patch-check"></i>
            <span>Izin Guru</span>
        </a>
        @endif
    @elseif($role === 'ketua_kelas')
        <a href="{{ route('ketua.verifikasi') }}" class="nav-item {{ request()->routeIs('ketua.verifikasi*') || request()->routeIs('ketua.foto*') ? 'active' : '' }}" wire:navigate>
            <i class="bi bi-qr-code-scan"></i>
            <span>Verifikasi</span>
        </a>
        <a href="{{ route('ketua.anggota') }}" class="nav-item {{ request()->routeIs('ketua.anggota*') ? 'active' : '' }}" wire:navigate>
            <i class="bi bi-people-fill"></i>
            <span>Anggota</span>
        </a>
    @endif
</nav>
