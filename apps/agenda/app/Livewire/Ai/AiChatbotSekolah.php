<?php

namespace App\Livewire\Ai;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('DamAI (Digital Assistant Mu berbasis AI) - SMKN 2 Indramayu')]
class AiChatbotSekolah extends Component
{
    public string $openWebUiBaseUrl = '';
    public string $iframeUrl = '';
    public bool $isServerOnline = true;
    public bool $isChecking = false;
    public string $errorMessage = '';
    public string $userSessionToken = '';
    public string $selectedCategory = 'semua';

    // Daftar Prompt Teks Edukatif Ramah Siswa
    public array $quickPrompts = [
        [
            'category' => 'penjelasan',
            'icon' => 'bi-lightbulb-fill',
            'title' => 'Jelaskan Konsep Sederhana',
            'text' => 'Tolong jelaskan materi ini dengan bahasa yang mudah dipahami siswa SMK: ',
        ],
        [
            'category' => 'ringkasan',
            'icon' => 'bi-card-text',
            'title' => 'Ringkas Bacaan Pelajaran',
            'text' => 'Bantu buat ringkasan poin-poin penting dari teks materi berikut: ',
        ],
        [
            'category' => 'latihan',
            'icon' => 'bi-patch-question-fill',
            'title' => 'Buat Pertanyaan Latihan',
            'text' => 'Buatkan 3 pertanyaan reflektif untuk menguji pemahaman saya tentang topik: ',
        ],
        [
            'category' => 'kejuruan',
            'icon' => 'bi-tools',
            'title' => 'Tanya Istilah Kejuruan',
            'text' => 'Jelaskan apa fungsi dan contoh penerapan istilah kejuruan ini di dunia kerja: ',
        ],
    ];

    public function mount()
    {
        $this->openWebUiBaseUrl = rtrim(config('services.ai_chatbot.openwebui_url', 'https://ai.smkn2indramayu.sch.id'), '/');
        $this->prepareSecureSession();
        $this->checkServerHealth();
    }

    /**
     * Mempersiapkan token sesi terisolasi per akun siswa/guru
     */
    public function prepareSecureSession(): void
    {
        $user = Auth::user();
        if (!$user) {
            $this->iframeUrl = $this->openWebUiBaseUrl;
            return;
        }

        // Generate token isolasi unik berbasis HMAC app key + user id
        $appKey = config('app.key') ?: 'gaddamay_secret_salt_2026';
        $this->userSessionToken = hash_hmac('sha256', $user->id . '|' . ($user->email ?? $user->username), $appKey);

        // Parameter query untuk Open-WebUI / Reverse Proxy Tunnel
        $queryParams = http_build_query([
            'uid' => $user->id,
            'role' => $user->role,
            'name' => $user->name,
            'session_token' => substr($this->userSessionToken, 0, 32),
            'mode' => 'text_only_edu', // Parameter penanda text-only edukasi
            'theme' => 'light',
        ]);

        $this->iframeUrl = $this->openWebUiBaseUrl . '?' . $queryParams;
    }

    /**
     * Pemeriksaan status koneksi ke Cloudflare Tunnel / Server AI Lokal
     */
    public function checkServerHealth(): void
    {
        $this->isChecking = true;

        if (empty($this->openWebUiBaseUrl) || !filter_var($this->openWebUiBaseUrl, FILTER_VALIDATE_URL)) {
            $this->isServerOnline = false;
            $this->errorMessage = 'Alamat URL server DamAI belum dikonfigurasi dengan benar.';
            $this->isChecking = false;
            return;
        }

        try {
            $timeout = (int) config('services.ai_chatbot.timeout_seconds', 4);
            
            // HTTP Probe ringan ke server Open-WebUI via Cloudflare Tunnel
            $response = Http::withoutVerifying()
                ->timeout($timeout)
                ->connectTimeout(2)
                ->get($this->openWebUiBaseUrl);

            // Jika menerima respon HTTP (meski 401/403/200), berarti tunnel & server hidup
            if ($response->status() >= 200 && $response->status() < 500) {
                $this->isServerOnline = true;
                $this->errorMessage = '';
            } else {
                $this->isServerOnline = false;
                $this->errorMessage = 'Layanan DamAI sedang beristirahat atau server lokal sekolah sedang nonaktif (Kode: ' . $response->status() . ').';
            }
        } catch (\Throwable $e) {
            // Server offline, tunnel putus, atau timeout
            $this->isServerOnline = false;
            $this->errorMessage = 'Layanan DamAI sedang beristirahat atau koneksi Cloudflare Tunnel terputus.';
        }

        $this->isChecking = false;
    }

    /**
     * Memuat ulang status koneksi saat ditekan oleh pengguna
     */
    public function refreshConnection(): void
    {
        $this->checkServerHealth();
        if ($this->isServerOnline) {
            $this->dispatch('iframe-reload');
            session()->flash('status', 'Koneksi ke DamAI berhasil terhubung kembali!');
        }
    }

    public function render()
    {
        return view('livewire.ai.ai-chatbot-sekolah');
    }
}
