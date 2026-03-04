<?php
$db = \Config\Database::connect();
$thn = date('Y'); 
$bln = date('m'); 

$id_sis = session()->get('id_user');
?>

<script>
'use strict';
$(document).ready(function() {
    setTimeout(function() {
        
        $(function() {
            var options = {
                chart: {
                    height: 320,
                    type: 'donut',
                },
                series: [<?= jummasukbln($id_sis,date('m'))?>, <?= jumterlambatbln($id_sis,date('m'))?>, <?= jumsakitbln($id_sis,date('m'))?>, <?= jumizinbln($id_sis,date('m'))?>, <?= jumalphatgl($id_sis)?>],
                labels: ['Masuk', 'Terlambat', 'Sakit', 'Izin', 'Alpha'],
                colors: ["#0e9e4a", "#ffba57", "#00acc1", "#4680ff", "#ff5252"],
                legend: {
                    show: true,
                    position: 'bottom',
                },
                plotOptions: {
                    pie: {
                        donut: {
                            labels: {
                                show: true,
                                name: {
                                    show: true
                                },
                                value: {
                                    show: true
                                }
                            }
                        }
                    }
                },
                dataLabels: {
                    enabled: true,
                    dropShadow: {
                        enabled: false,
                    }
                },
                responsive: [{
                    breakpoint: 480,
                    options: {          
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            }
            var chart = new ApexCharts(
                document.querySelector("#pie-chart-2"),
                options
            );
            chart.render();
        });
        
    }, 700);
});
</script>