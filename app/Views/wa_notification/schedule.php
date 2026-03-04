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

                        <!-- Timeline View -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card shadow-sm" style="border-radius: 15px; border: none;">
                                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center" style="border-radius: 15px 15px 0 0;">
                                        <h5 class="mb-0"><i class="feather icon-calendar mr-2 text-primary"></i>Jadwal Broadcast</h5>
                                        <a href="<?= base_url('WaNotification') ?>" class="btn btn-outline-primary" style="border-radius: 10px;">
                                            <i class="feather icon-arrow-left mr-1"></i> Kembali
                                        </a>
                                    </div>
                                    <div class="card-body">
                                        <?php if(empty($schedule)): ?>
                                            <div class="text-center py-5">
                                                <div style="width: 100px; height: 100px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="feather icon-calendar text-white" style="font-size: 40px;"></i>
                                                </div>
                                                <h5 class="text-muted">Belum ada jadwal broadcast</h5>
                                                <p class="text-muted">Generate laporan mingguan untuk membuat jadwal</p>
                                                <a href="<?= base_url('WeeklyReport/generate') ?>" class="btn btn-primary" style="border-radius: 10px;">
                                                    <i class="feather icon-plus mr-1"></i> Generate Laporan
                                                </a>
                                            </div>
                                        <?php else: ?>
                                            <div class="timeline-container">
                                                <?php foreach($schedule as $day): ?>
                                                    <?php
                                                    $isToday = $day['scheduled_date'] == date('Y-m-d');
                                                    $isPast = $day['scheduled_date'] < date('Y-m-d');
                                                    $isFuture = $day['scheduled_date'] > date('Y-m-d');
                                                    $progressPercent = $day['total'] > 0 ? round(($day['sent'] / $day['total']) * 100) : 0;
                                                    ?>
                                                    <div class="timeline-item mb-4 <?= $isToday ? 'timeline-today' : '' ?>">
                                                        <div class="row align-items-center">
                                                            <div class="col-md-2 text-center">
                                                                <div class="timeline-date <?= $isToday ? 'bg-primary text-white' : ($isPast ? 'bg-light' : 'bg-white border') ?>" style="padding: 15px; border-radius: 15px;">
                                                                    <h4 class="mb-0"><?= date('d', strtotime($day['scheduled_date'])) ?></h4>
                                                                    <small><?= date('M Y', strtotime($day['scheduled_date'])) ?></small>
                                                                    <?php if($isToday): ?>
                                                                        <span class="badge badge-light d-block mt-2">Hari Ini</span>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-10">
                                                                <div class="card mb-0" style="border-radius: 15px; border: <?= $isToday ? '2px solid #667eea' : '1px solid #e9ecef' ?>;">
                                                                    <div class="card-body">
                                                                        <div class="row align-items-center">
                                                                            <div class="col-md-6">
                                                                                <h5 class="mb-2">
                                                                                    <i class="feather icon-send mr-2 text-primary"></i>
                                                                                    <?= $day['total'] ?> Pesan
                                                                                </h5>
                                                                                <p class="text-muted mb-0">
                                                                                    Periode: <?= date('d/m', strtotime($day['week_start'])) ?> - <?= date('d/m Y', strtotime($day['week_end'])) ?>
                                                                                </p>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="d-flex justify-content-between mb-2">
                                                                                    <span>Progress</span>
                                                                                    <span class="font-weight-bold"><?= $progressPercent ?>%</span>
                                                                                </div>
                                                                                <div class="progress" style="height: 10px; border-radius: 5px;">
                                                                                    <div class="progress-bar bg-success" style="width: <?= $progressPercent ?>%; border-radius: 5px;"></div>
                                                                                </div>
                                                                                <div class="d-flex justify-content-between mt-2">
                                                                                    <span class="badge badge-warning"><?= $day['pending'] ?> Pending</span>
                                                                                    <span class="badge badge-success"><?= $day['sent'] ?> Terkirim</span>
                                                                                    <?php if($day['failed'] > 0): ?>
                                                                                        <span class="badge badge-danger"><?= $day['failed'] ?> Gagal</span>
                                                                                    <?php endif; ?>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
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
    .timeline-item { position: relative; }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: calc(8.33% + 20px);
        top: 60px;
        bottom: -20px;
        width: 2px;
        background: linear-gradient(180deg, #667eea, #764ba2);
    }
    .timeline-item:last-child::before { display: none; }
    .timeline-today { animation: pulse-border 2s infinite; }
    @keyframes pulse-border {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }
    .card { transition: transform 0.3s ease; }
    .card:hover { transform: translateY(-3px); }
</style>
