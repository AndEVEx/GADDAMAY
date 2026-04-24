<?php
/**
 * Chart Absensi Harian per Jurusan (Today)
 * Displays a bar chart showing Hadir, Terlambat, Tidak Hadir for each class, separated by Jurusan.
 */
$db = \Config\Database::connect();
$tgl = date('Y-m-d');
$rombelList = getAllRombelForChart();

// Build data arrays for the chart grouped by Jurusan
$jurusanData = [];

foreach ($rombelList as $rombel) {
    $parts = explode(" ", trim($rombel->nm_rombel));
    $jur = isset($parts[1]) ? $parts[1] : 'Lainnya';
    if(count($parts) > 3) $jur = $parts[1];
    
    if(!isset($jurusanData[$jur])) {
        $jurusanData[$jur] = [
            'categories' => [],
            'hadir' => [],
            'terlambat' => [],
            'tidak_hadir' => []
        ];
    }
    
    $jurusanData[$jur]['categories'][] = $rombel->nm_rombel;
    $jurusanData[$jur]['hadir'][] = jumHadirKelasHariIni($rombel->id_rombel);
    $jurusanData[$jur]['terlambat'][] = jumTerlambatKelasHariIni($rombel->id_rombel);
    $jurusanData[$jur]['tidak_hadir'][] = jumTidakHadirKelasHariIni($rombel->id_rombel);
}
?>

<script>
'use strict';
$(document).ready(function() {
    setTimeout(function() {
        $(function() {
            <?php foreach($jurusanData as $jur => $data): ?>
            var options_<?=$jur?> = {
                chart: {
                    height: 350,
                    type: 'bar',
                    toolbar: { show: false },
                    zoom: { enabled: false }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '60%',
                        endingShape: 'flat',
                        borderRadius: 2
                    },
                },
                dataLabels: {
                    enabled: false
                },
                colors: ["#0e9e4a", "#ffb64d", "#ff5252"],
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                series: [{
                    name: 'Hadir',
                    data: <?= json_encode($data['hadir']) ?>
                }, {
                    name: 'Terlambat',
                    data: <?= json_encode($data['terlambat']) ?>
                }, {
                    name: 'Tidak Hadir',
                    data: <?= json_encode($data['tidak_hadir']) ?>
                }],
                xaxis: {
                    categories: <?= json_encode($data['categories']) ?>,
                    labels: {
                        rotate: -45,
                        rotateAlways: true,
                        style: { fontSize: '10px' },
                        trim: true,
                        maxHeight: 100
                    },
                    tickPlacement: 'on'
                },
                yaxis: {
                    title: { text: 'Jumlah Murid' },
                    min: 0,
                    tickAmount: 5
                },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val + " murid"
                        }
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'center'
                }
            };
            
            if(document.querySelector("#chart-jurusan-<?=$jur?>")) {
                var chart_<?=$jur?> = new ApexCharts(
                    document.querySelector("#chart-jurusan-<?=$jur?>"),
                    options_<?=$jur?>
                );
                chart_<?=$jur?>.render();
            }
            <?php endforeach; ?>
        });
    }, 500);
});
</script>