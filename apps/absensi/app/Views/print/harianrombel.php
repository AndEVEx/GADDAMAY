<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?></title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 8px; text-align: left; }
        .text-center { text-align: center; }
        .header { margin-bottom: 30px; text-align: center; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; cursor: pointer;">Cetak PDF/Printer</button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 16px; cursor: pointer;">Tutup</button>
    </div>

    <div class="header">
        <h2>Laporan Harian Absensi Siswa</h2>
        <p>Kelas: <?= $nmRombel ?><br>Tanggal: <?= formatTanggal($getTanggal) ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center">#</th>
                <th>No. Induk</th>
                <th>Nama Siswa</th>
                <th class="text-center">Jam Masuk</th>
                <th class="text-center">Status</th>
                <th class="text-center">Jam Pulang</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no=0;
            foreach ($getSiswa as $data) {  
            $no++;
            $id_siswa =$data['id_siswa'];
            ?>
            <tr>
                <td class="text-center"><?= $no ?></td>
                <td><?= $data['no_induk'] ?></td>
                <td><?= $data['nm_siswa'] ?></td>
                <td class="text-center"><?= jammasuk($id_siswa,$getTanggal) ?></td>
                <td class="text-center"><?= sts_absen($id_siswa,$getTanggal) ?></td>
                <td class="text-center"><?= jampulang($id_siswa,$getTanggal) ?></td>
            </tr>    
            <?php } ?>
        </tbody>
    </table>
    <script>
        window.onload = function() { window.print(); }
    </script>
</body>
</html>
