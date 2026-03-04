<!-- [ Main Content ] start -->
<div class="pcoded-main-container">
    <div class="pcoded-wrapper">
        <div class="pcoded-content">
            <div class="pcoded-inner-content">
                <div class="main-body">
                    <div class="page-wrapper">
                        <!-- [ breadcrumb ] start -->
                        <div class="page-header">
                            <div class="page-block">
                                <div class="row align-items-center">
                                    <div class="col-md-12">
                                        <div class="page-header-title">
                                            <h5 class="m-b-10"><?= $title; ?></h5>
                                        </div>
                                        <ul class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="<?= base_url('Home'); ?>"><i class="feather icon-home"></i></a></li>
                                            <li class="breadcrumb-item"><a href="<?= base_url('WaNotification'); ?>">WhatsApp</a></li>
                                            <li class="breadcrumb-item"><a href="#"><?= $nav; ?></a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- [ breadcrumb ] end -->

                        <div class="row">
                            <!-- Student Info Card -->
                            <div class="col-lg-4">
                                <div class="card shadow-sm" style="border-radius: 15px; border: none;">
                                    <div class="card-body text-center">
                                        <div class="student-avatar mb-3" style="width: 100px; height: 100px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                                            <i class="feather icon-user text-white" style="font-size: 40px;"></i>
                                        </div>
                                        <h4 class="mb-1"><?= $student['nm_siswa'] ?></h4>
                                        <p class="text-muted mb-0">NIS: <?= $student['no_induk'] ?></p>
                                        <span class="badge badge-primary mt-2" style="padding: 8px 16px; border-radius: 20px;"><?= $student['nm_rombel'] ?></span>
                                    </div>
                                </div>

                                <!-- Attendance Summary -->
                                <div class="card shadow-sm mt-4" style="border-radius: 15px; border: none;">
                                    <div class="card-header bg-white border-0" style="border-radius: 15px 15px 0 0;">
                                        <h5 class="mb-0"><i class="feather icon-bar-chart-2 mr-2 text-primary"></i>Ringkasan Minggu Ini</h5>
                                    </div>
                                    <div class="card-body">
                                        <p class="text-muted mb-3">
                                            <i class="feather icon-calendar mr-1"></i>
                                            <?= date('d M', strtotime($monday)) ?> - <?= date('d M Y', strtotime($friday)) ?>
                                        </p>
                                        
                                        <?php
                                        $hadir = 0; $terlambat = 0; $alpha = 0;
                                        foreach($attendance as $data) {
                                            if($data['masuk']) { $hadir++; if($data['terlambat']) $terlambat++; }
                                            else $alpha++;
                                        }
                                        ?>
                                        
                                        <div class="row text-center">
                                            <div class="col-4">
                                                <div class="p-3 rounded" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                                                    <h3 class="text-white mb-0"><?= $hadir ?></h3>
                                                    <small class="text-white">Hadir</small>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="p-3 rounded" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                                    <h3 class="text-white mb-0"><?= $terlambat ?></h3>
                                                    <small class="text-white">Terlambat</small>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="p-3 rounded" style="background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);">
                                                    <h3 class="text-white mb-0"><?= $alpha ?></h3>
                                                    <small class="text-white">Alpha</small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Attendance Detail -->
                                        <div class="mt-4">
                                            <h6 class="mb-3">Kehadiran per Hari:</h6>
                                            <?php foreach($attendance as $data): ?>
                                                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                                    <span><strong><?= $data['hari'] ?></strong></span>
                                                    <span>
                                                        <?php if($data['masuk']): ?>
                                                            <span class="badge badge-<?= $data['terlambat'] ? 'warning' : 'success' ?>">
                                                                <?= $data['masuk'] ?><?= $data['terlambat'] ? ' ⚠️' : '' ?>
                                                            </span>
                                                            <?php if($data['pulang']): ?>
                                                                <i class="feather icon-arrow-right mx-1"></i>
                                                                <span class="badge badge-info"><?= $data['pulang'] ?></span>
                                                            <?php endif; ?>
                                                        <?php else: ?>
                                                            <span class="badge badge-danger">❌ Tidak Hadir</span>
                                                        <?php endif; ?>
                                                    </span>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Message Preview -->
                            <div class="col-lg-8">
                                <div class="card shadow-sm" style="border-radius: 15px; border: none;">
                                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center" style="border-radius: 15px 15px 0 0;">
                                        <h5 class="mb-0"><i class="feather icon-smartphone mr-2 text-success"></i>Preview Pesan WhatsApp</h5>
                                        <a href="<?= base_url('WaNotification') ?>" class="btn btn-outline-primary" style="border-radius: 10px;">
                                            <i class="feather icon-arrow-left mr-1"></i> Kembali
                                        </a>
                                    </div>
                                    <div class="card-body p-0">
                                        <!-- Phone Mockup -->
                                        <div class="phone-mockup" style="background: #0a1628; padding: 20px; border-radius: 0 0 15px 15px;">
                                            <div class="phone-frame" style="max-width: 400px; margin: 0 auto; background: #111b21; border-radius: 30px; overflow: hidden; box-shadow: 0 25px 50px rgba(0,0,0,0.3);">
                                                <!-- Phone Header -->
                                                <div style="background: #202c33; padding: 15px; display: flex; align-items: center;">
                                                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #25D366, #128C7E); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 10px;">
                                                        <span style="color: white; font-weight: bold;">S2</span>
                                                    </div>
                                                    <div>
                                                        <div style="color: white; font-weight: 600;">SMKN 2 Indramayu</div>
                                                        <small style="color: #8696a0;">School Notification</small>
                                                    </div>
                                                </div>
                                                
                                                <!-- Chat Area -->
                                                <div style="background: url('data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 200 200\"><defs><pattern id=\"a\" patternUnits=\"userSpaceOnUse\" width=\"20\" height=\"20\"><circle cx=\"10\" cy=\"10\" r=\"1\" fill=\"%23ffffff08\"/></pattern></defs><rect width=\"200\" height=\"200\" fill=\"%230b141a\"/><rect width=\"200\" height=\"200\" fill=\"url(%23a)\"/></svg>'); padding: 20px; min-height: 400px;">
                                                    
                                                    <!-- Message Bubble -->
                                                    <div class="message-bubble" style="background: #005c4b; color: white; padding: 15px; border-radius: 10px 10px 10px 0; max-width: 100%; box-shadow: 0 2px 5px rgba(0,0,0,0.2); margin-bottom: 10px;">
                                                        <pre style="white-space: pre-wrap; margin: 0; font-family: 'Segoe UI', sans-serif; font-size: 14px; line-height: 1.5;"><?= htmlspecialchars($message) ?></pre>
                                                        <div style="text-align: right; margin-top: 10px;">
                                                            <small style="color: rgba(255,255,255,0.6);"><?= date('H:i') ?> <i class="feather icon-check" style="font-size: 12px;"></i></small>
                                                        </div>
                                                    </div>
                                                    
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="card shadow-sm mt-4" style="border-radius: 15px; border: none;">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6><i class="feather icon-info mr-2 text-info"></i>Informasi Siswa</h6>
                                                <table class="table table-sm">
                                                    <tr><td>Nama</td><td><strong><?= $student['nm_siswa'] ?></strong></td></tr>
                                                    <tr><td>NIS</td><td><?= $student['no_induk'] ?></td></tr>
                                                    <tr><td>Kelas</td><td><?= $student['nm_rombel'] ?></td></tr>
                                                    <tr><td>No HP Ortu</td><td><?= $student['hp'] ?: '<span class="text-danger">Tidak tersedia</span>' ?></td></tr>
                                                </table>
                                            </div>
                                            <div class="col-md-6">
                                                <h6><i class="feather icon-copy mr-2 text-warning"></i>Copy Pesan</h6>
                                                <p class="text-muted small">Klik tombol di bawah untuk menyalin pesan ke clipboard</p>
                                                <button class="btn btn-success btn-block" style="border-radius: 10px;" onclick="copyMessage()">
                                                    <i class="feather icon-copy mr-2"></i> Salin Pesan
                                                </button>
                                                <textarea id="messageText" style="position: absolute; left: -9999px;"><?= htmlspecialchars($message) ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card { transition: transform 0.3s ease; }
    .card:hover { transform: translateY(-3px); }
    .phone-mockup { animation: float 4s ease-in-out infinite; }
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }
</style>

<script>
function copyMessage() {
    const text = document.getElementById('messageText').value;
    navigator.clipboard.writeText(text).then(() => {
        const btn = event.target;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="feather icon-check mr-2"></i> Tersalin!';
        btn.classList.remove('btn-success');
        btn.classList.add('btn-outline-success');
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.classList.remove('btn-outline-success');
            btn.classList.add('btn-success');
        }, 2000);
    });
}
</script>
