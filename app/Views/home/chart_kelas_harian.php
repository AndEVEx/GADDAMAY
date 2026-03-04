<?php
/**
 * Chart Absensi Harian per Kelas (Today)
 * Displays a bar chart showing Hadir, Terlambat, Tidak Hadir for each class
 */
$db = \Config\Database::connect();
$tgl = date('Y-m-d');
$rombelList = getAllRombelForChart();

// Build data arrays for the chart
$categories = [];
$dataHadir = [];
$dataTerlambat = [];
$dataTidakHadir = [];

foreach ($rombelList as $rombel) {
    $categories[] = $rombel->nm_rombel;
    $dataHadir[] = jumHadirKelasHariIni($rombel->id_rombel);
    $dataTerlambat[] = jumTerlambatKelasHariIni($rombel->id_rombel);
    $dataTidakHadir[] = jumTidakHadirKelasHariIni($rombel->id_rombel);
}

// Convert to JSON for JavaScript
$categoriesJson = json_encode($categories);
$dataHadirJson = json_encode($dataHadir);
$dataTerlambatJson = json_encode($dataTerlambat);
$dataTidakHadirJson = json_encode($dataTidakHadir);
?>

<script>
'use strict';
$(document).ready(function() {
    setTimeout(function() {
        $(function() {
            var options = {
                chart: {
                    height: 450,
                    type: 'bar',
                    toolbar: {
                        show: true
                    },
                    zoom: {
                        enabled: true
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '70%',
                        endingShape: 'flat',
                        borderRadius: 0
                    },
                },
                dataLabels: {
                    enabled: false
                },
                colors: ["#0e9e4a", "#ffb64d", "#ff5252"],
                stroke: {
                    show: true,
                    width: 1,
                    colors: ['transparent']
                },
                series: [{
                    name: 'Hadir',
                    data: <?=$dataHadirJson?>
                }, {
                    name: 'Terlambat',
                    data: <?=$dataTerlambatJson?>
                }, {
                    name: 'Tidak Hadir',
                    data: <?=$dataTidakHadirJson?>
                }],
                xaxis: {
                    categories: <?=$categoriesJson?>,
                    title: {
                        text: 'Kelas'
                    },
                    labels: {
                        rotate: -45,
                        rotateAlways: true,
                        style: {
                            fontSize: '9px'
                        },
                        trim: true,
                        maxHeight: 80
                    },
                    tickPlacement: 'on'
                },
                yaxis: {
                    title: {
                        text: 'Jumlah Murid'
                    },
                    min: 0,
                    max: 40,
                    tickAmount: 8
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
                },
                responsive: [{
                    breakpoint: 768,
                    options: {
                        chart: {
                            height: 350
                        },
                        xaxis: {
                            labels: {
                                rotate: -90,
                                style: {
                                    fontSize: '8px'
                                }
                            }
                        }
                    }
                }]
            };
            
            var chart = new ApexCharts(
                document.querySelector("#bar-chart-kelas-hari-ini"),
                options
            );
            chart.render();
        });
    }, 800);
});
</script>