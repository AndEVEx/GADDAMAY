<!-- [ Pre-loader ] start -->
<div class="loader-bg">
    <div class="loader-track">
        <div class="loader-fill"></div>
    </div>
</div>
<!-- [ Pre-loader ] End -->
<!-- [ navigation menu ] start -->
<nav class="pcoded-navbar menu-light brand-blue">
    <div class="navbar-wrapper">
        <div class="navbar-content scroll-div">
            <div class="">
                <div class="main-menu-header">
                    <?php if (session()->get('level') == 1 || session()->get('level') == 4) { ?>
                        <img class="img-radius" src="<?= base_url() ?>image/<?= session()->get('foto'); ?>"
                            alt="User-Profile-Image">
                    <?php } elseif (session()->get('level') == 2) { ?>
                        <img class="img-radius" src="<?= base_url() ?>image/guru/<?= session()->get('foto'); ?>"
                            alt="User-Profile-Image">
                    <?php } else { ?>
                        <img class="img-radius" src="<?= base_url() ?>image/siswa/<?= session()->get('foto'); ?>"
                            alt="User-Profile-Image">
                    <?php } ?>
                    <div class="user-details">
                        <div id="more-details"><?= session()->get('nama'); ?> <i class="fa fa-caret-down"></i></div>
                    </div>
                </div>
                <div class="collapse" id="nav-user-link">
                    <ul class="list-unstyled">
                        <?php if ((session()->get('level')) == 2) { ?>
                            <li class="list-group-item"><a href="<?= base_url('Profile'); ?>"><i
                                        class="feather icon-user m-r-5"></i>View Profile</a></li>
                        <?php } elseif ((session()->get('level')) == 3) { ?>
                            <li class="list-group-item"><a href="<?= base_url('Siswaprofile'); ?>"><i
                                        class="feather icon-user m-r-5"></i>View Profile</a></li>
                        <?php } ?>
                        <li class="list-group-item"><a href="<?= base_url('Cpanel/logout'); ?>"><i
                                    class="feather icon-log-out m-r-5"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
            <br>
            <ul class="nav pcoded-inner-navbar">
                <li class="nav-item pcoded-menu-caption">
                    <label><?= hari_ini() ?>, <?= date('d F Y') ?></label>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('Home'); ?>" class="nav-link "><span class="pcoded-micon"><i
                                class="feather icon-home"></i></span><span class="pcoded-mtext">Dashboard</span></a>
                </li>
                <?php if ((session()->get('level')) == 4) { ?>
                    <li class="nav-item pcoded-hasmenu">
                        <a href="#!" class="nav-link "><span class="pcoded-micon"><i
                                    class="feather icon-airplay"></i></span><span class="pcoded-mtext">Info Absensi
                                Guru</span></a>
                        <ul class="pcoded-submenu">
                            <li><a href="<?= base_url('Absensi/perpegawai'); ?>">Per Pegawai</a></li>
                            <li><a href="<?= base_url('Absensi'); ?>">Harian</a></li>
                            <li><a href="<?= base_url('Absensi/pertanggal'); ?>">Per tanggal</a></li>
                            <li><a href="<?= base_url('Absensi/pertanggaluser'); ?>">Per tanggal User</a></li>
                            <li><a href="<?= base_url('Absensi/bulanan'); ?>">Bulanan</a></li>
                            <li><a href="<?= base_url('Point'); ?>">Point</a></li>
                            <li><a href="<?= base_url('Absensi/chart'); ?>">Chart Absen</a></li>
                        </ul>
                    </li>
                    <li class="nav-item pcoded-hasmenu">
                        <a href="#!" class="nav-link "><span class="pcoded-micon"><i
                                    class="feather icon-airplay"></i></span><span class="pcoded-mtext">Info Absensi
                                Siswa</span></a>
                        <ul class="pcoded-submenu">
                            <li><a href="<?= base_url('Absensisiswa/persiswa'); ?>">Per Siswa</a></li>
                            <li><a href="<?= base_url('Absensisiswa'); ?>">Harian</a></li>
                            <li><a href="<?= base_url('Absensisiswa/pertanggal'); ?>">Per tanggal</a></li>
                            <li><a href="<?= base_url('Absensisiswa/pertanggalsiswa'); ?>">Per tanggal User</a></li>
                            <li><a href="<?= base_url('Absensisiswa/bulanan'); ?>">Bulanan</a></li>
                            <li><a href="<?= base_url('Pointsiswa'); ?>">Point</a></li>
                            <li><a href="<?= base_url('Absensisiswa/chart'); ?>">Chart Absen</a></li>
                        </ul>
                    </li>

                <?php } ?>

                <?php if ((session()->get('level')) == 1) { ?>
                    <li class="nav-item pcoded-hasmenu">
                        <a href="#!" class="nav-link "><span class="pcoded-micon"><i
                                    class="feather icon-layout"></i></span><span class="pcoded-mtext">Setting</span></a>
                        <ul class="pcoded-submenu">
                            <li><a href="<?= base_url('Setting'); ?>">Aplikasi</a></li>

                            <li><a href="<?= base_url('User'); ?>">User Management</a></li>
                            <li><a href="<?= base_url('Tapel'); ?>">Tapel</a></li>
                            <li><a href="<?= base_url('Liburbesar'); ?>">Libur Hari Besar</a></li>
                        </ul>
                    </li>

                    <li class="nav-item pcoded-hasmenu">
                        <a href="#!" class="nav-link "><span class="pcoded-micon"><i
                                    class="feather icon-box"></i></span><span class="pcoded-mtext">Data Master
                                Guru</span></a>
                        <ul class="pcoded-submenu">
                            <li><a href="<?= base_url('Jeniskerja'); ?>">Jenis Ketenagaan</a></li>
                            <!-- Shift menu hidden to prevent SNAG error -->
                            <!-- <li><a href="<?= base_url('Shift'); ?>">Shift</a></li> -->
                            <li><a href="<?= base_url('Jadwalkhusus'); ?>">Jadwal Khusus</a></li>
                            <li><a href="<?= base_url('Pegawai'); ?>">Data Guru & Karyawan</a></li>

                        </ul>
                    </li>
                    <li class="nav-item pcoded-hasmenu">
                        <a href="#!" class="nav-link "><span class="pcoded-micon"><i
                                    class="feather icon-box"></i></span><span class="pcoded-mtext">Master Siswa</span></a>
                        <ul class="pcoded-submenu">
                            <li><a href="<?= base_url('Siswa'); ?>">Data Siswa</a></li>
                            <li><a href="<?= base_url('Tingkatkelas'); ?>">Tingkat Kelas</a></li>
                            <li><a href="<?= base_url('Rombel'); ?>">Rombel</a></li>
                            <li><a href="<?= base_url('Siswarombel'); ?>">Setting Kelas</a></li>

                        </ul>
                    </li>
                    <li class="nav-item pcoded-hasmenu">
                        <a href="#!" class="nav-link "><span class="pcoded-micon"><i
                                    class="feather icon-edit"></i></span><span class="pcoded-mtext">Koreksi Absen</span></a>
                        <ul class="pcoded-submenu">
                            <li><a href="<?= base_url('Absensi/koreksi'); ?>">Guru</a></li>
                            <li><a href="<?= base_url('Absensisiswa/koreksi'); ?>">Siswa</a></li>

                        </ul>
                    </li>
                    <li class="nav-item pcoded-hasmenu">
                        <a href="#!" class="nav-link "><span class="pcoded-micon"><i
                                    class="feather icon-check"></i></span><span class="pcoded-mtext">Ijin</span></a>
                        <ul class="pcoded-submenu">
                            <?php
                            $db = \Config\Database::connect();
                            $builder_ijin = $db->table('tweb_pegawai_absen');
                            $builder_ijin->where('STS', 0);
                            $jml_ijin = $builder_ijin->countAllResults();

                            $builder_ijinsiswa = $db->table('t_siswa_absen');
                            $builder_ijinsiswa->where('sts_approve', 0);
                            $jml_ijinsiswa = $builder_ijinsiswa->countAllResults();
                            ?>
                            <li><a href="<?= base_url('Approveijin'); ?>">Approve Guru <span
                                        class="badge badge-danger"><?= $jml_ijin ?></span></a></li>
                            <li><a href="<?= base_url('Approveijinsiswa'); ?>">Approve Siswa <span
                                        class="badge badge-danger"><?= $jml_ijinsiswa ?></span></a></li>
                            <li><a href="<?= base_url('Approveijin/report'); ?>">Report Ijin Guru </a></li>
                            <li><a href="<?= base_url('Approveijinsiswa/report'); ?>">Report Ijin Siswa </a></li>

                        </ul>
                    </li>

                    <li class="nav-item pcoded-hasmenu">
                        <a href="#!" class="nav-link "><span class="pcoded-micon"><i
                                    class="feather icon-airplay"></i></span><span class="pcoded-mtext">Info Absensi
                                Guru</span></a>
                        <ul class="pcoded-submenu">
                            <li><a href="<?= base_url('Absensi/perpegawai'); ?>">Per Pegawai</a></li>
                            <li><a href="<?= base_url('Absensi'); ?>">Harian</a></li>
                            <li><a href="<?= base_url('Absensi/pertanggal'); ?>">Per tanggal</a></li>
                            <li><a href="<?= base_url('Absensi/pertanggaluser'); ?>">Per tanggal User</a></li>
                            <li><a href="<?= base_url('Absensi/bulanan'); ?>">Bulanan</a></li>
                            <li><a href="<?= base_url('Point'); ?>">Point</a></li>
                            <li><a href="<?= base_url('Absensi/chart'); ?>">Chart Absen</a></li>
                        </ul>
                    </li>
                    <li class="nav-item pcoded-hasmenu">
                        <a href="#!" class="nav-link "><span class="pcoded-micon"><i
                                    class="feather icon-airplay"></i></span><span class="pcoded-mtext">Info Absensi
                                Siswa</span></a>
                        <ul class="pcoded-submenu">
                            <li><a href="<?= base_url('Absensisiswa/persiswa'); ?>">Per Siswa</a></li>
                            <li><a href="<?= base_url('Absensisiswa'); ?>">Harian</a></li>
                            <li><a href="<?= base_url('Absensisiswa/pertanggal'); ?>">Per tanggal</a></li>
                            <li><a href="<?= base_url('Absensisiswa/pertanggalsiswa'); ?>">Per tanggal User</a></li>
                            <li><a href="<?= base_url('Absensisiswa/bulanan'); ?>">Bulanan</a></li>
                            <li><a href="<?= base_url('Pointsiswa'); ?>">Point</a></li>
                            <li><a href="<?= base_url('Absensisiswa/chart'); ?>">Chart Absen</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('Import'); ?>" class="nav-link "><span class="pcoded-micon"><i
                                    class="feather icon-download"></i></span><span class="pcoded-mtext">Import</span></a>
                    </li>
                    <li class="nav-item pcoded-hasmenu">
                        <a href="#!" class="nav-link "><span class="pcoded-micon"><i
                                    class="feather icon-message-circle"></i></span><span
                                class="pcoded-mtext">WhatsApp</span></a>
                        <ul class="pcoded-submenu">
                            <li><a href="<?= base_url('WaNotification'); ?>">Dashboard</a></li>
                            <li><a href="<?= base_url('WaNotification/queue'); ?>">Antrian Pesan</a></li>
                            <li><a href="<?= base_url('WaNotification/settings'); ?>">Pengaturan GoWA</a></li>
                        </ul>
                    </li>
                    <li class="nav-item pcoded-hasmenu">
                        <a href="#!" class="nav-link "><span class="pcoded-micon"><i
                                    class="feather icon-alert-triangle"></i></span><span class="pcoded-mtext">Peringatan
                                Kehadiran</span></a>
                        <ul class="pcoded-submenu">
                            <li><a href="<?= base_url('AttendanceAlert'); ?>">Daftar Peringatan</a></li>
                            <li><a href="<?= base_url('AttendanceAlert/report'); ?>">Laporan</a></li>
                            <li><a href="<?= base_url('AttendanceAlert/settings'); ?>">Pengaturan</a></li>
                        </ul>
                    </li>

                <?php } elseif ((session()->get('level')) == 2) { ?>

                    <li class="nav-item">
                        <a href="<?= base_url('Absensi/pegawai'); ?>" class="nav-link "><span class="pcoded-micon"><i
                                    class="feather icon-calendar"></i></span><span class="pcoded-mtext">Info
                                Absensi</span></a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('Ajukanizin'); ?>" class="nav-link "><span class="pcoded-micon"><i
                                    class="feather icon-upload"></i></span><span class="pcoded-mtext">Ajukan Izin</span></a>
                    </li>
                    <?php
                    //cek apakah dia wali kelas
                    $db = \Config\Database::connect();
                    $builder_wali = $db->table('t_rombel');
                    $builder_wali->where('id_walikelas', session()->get('id_user'));
                    $builder_wali->where('id_tapel', session()->get('id_tapel'));
                    $all_wali = $builder_wali->countAllResults();
                    if ($all_wali > 0) {
                        ?>
                        <li class="nav-item pcoded-hasmenu">
                            <a href="#!" class="nav-link "><span class="pcoded-micon"><i
                                        class="feather icon-airplay"></i></span><span class="pcoded-mtext">Wali Kelas</span></a>
                            <ul class="pcoded-submenu">
                                <li><a href="<?= base_url('Absensisiswa/koreksiwali'); ?>">Koreksi</a></li>
                                <li><a href="<?= base_url('Reportwal/persiswa'); ?>">Per Siswa</a></li>
                                <li><a href="<?= base_url('Reportwal'); ?>">Harian</a></li>
                                <li><a href="<?= base_url('Reportwal/pertanggal'); ?>">Per tanggal</a></li>
                                <li><a href="<?= base_url('Reportwal/bulanan'); ?>">Bulanan</a></li>
                            </ul>
                        </li>

                        <?php
                    }
                    ?>
                <?php } elseif (session()->get('level') != 4) { ?>
                    <li class="nav-item pcoded-hasmenu">
                        <a href="#!" class="nav-link "><span class="pcoded-micon"><i
                                    class="feather icon-airplay"></i></span><span class="pcoded-mtext">Info
                                Absensi</span></a>
                        <ul class="pcoded-submenu">

                            <li><a href="<?= base_url('Reportsis/pertanggal'); ?>">Per tanggal</a></li>
                            <li><a href="<?= base_url('Reportsis/bulanan'); ?>">Bulanan</a></li>
                            <li><a href="<?= base_url('Reportsis/point'); ?>">Point</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('Ajukanizinsiswa'); ?>" class="nav-link "><span class="pcoded-micon"><i
                                    class="feather icon-upload"></i></span><span class="pcoded-mtext">Ajukan Izin</span></a>
                    </li>
                <?php } ?>

            </ul>
        </div>
    </div>
</nav>
<!-- [ navigation menu ] end -->
<!-- [ Header ] start -->
<header class="navbar pcoded-header navbar-expand-lg navbar-light header-blue">
    <div class="m-header">
        <a class="mobile-menu" id="mobile-collapse" href="#!"><span></span></a>
        <a href="#!" class="b-brand">
            <!-- ========   change your logo hear   ============ -->
            <?php
            $db = \Config\Database::connect();
            $builder = $db->table('t_setting_aplikasi');
            $query = $builder->get();
            $aplikasi = $query->getRow();
            echo strtoupper($aplikasi->nm_aplikasi);
            ?>
            <img src="<?= base_url() ?>template/assets/images/logo-icon.png" alt="" class="logo-thumb">
        </a>
        <a href="#!" class="mob-toggler">
            <i class="feather icon-more-vertical"></i>
        </a>
    </div>
    <div class="collapse navbar-collapse">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a href="#!" class="pop-search"><i class="feather icon-search"></i></a>
                <div class="search-bar">
                    <input type="text" class="form-control border-0 shadow-none" placeholder="Search hear">
                    <button type="button" class="close" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">


        </ul>
    </div>
</header>
<!-- [ Header ] end -->