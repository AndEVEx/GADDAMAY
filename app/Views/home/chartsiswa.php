<?php
$db = \Config\Database::connect();
$thn = date('Y'); 
$bln = date('m'); 

$id_ptk = session()->get('id_user');
?>

<script>
'use strict';
$(document).ready(function() {
    setTimeout(function() {
        
        $(function() {
            var options = {
                chart: {
                    height: 350,
                    type: 'bar',
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded'
                    },
                },
                dataLabels: {
                    enabled: false
                },
                colors: ["#0e9e4a", "#4680ff", "#ff5252"],
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                series: [{
                    name: 'Hadir',
                    data: [<?=jummasukblnadmin(1);?>, <?=jummasukblnadmin(2);?>, <?=jummasukblnadmin(3);?>, <?=jummasukblnadmin(4);?>, <?=jummasukblnadmin(5);?>, <?=jummasukblnadmin(6);?>, <?=jummasukblnadmin(7);?>, <?=jummasukblnadmin(8);?>, <?=jummasukblnadmin(9);?>, <?=jummasukblnadmin(10);?>, <?=jummasukblnadmin(11);?>, <?=jummasukblnadmin(12);?>]
                }, { 
                    name: 'Terlambat',
                    data: [<?=jumterlambatblnadmin(1);?>, <?=jumterlambatblnadmin(2);?>, <?=jumterlambatblnadmin(3);?>, <?=jumterlambatblnadmin(4);?>, <?=jumterlambatblnadmin(5);?>, <?=jumterlambatblnadmin(6);?>, <?=jumterlambatblnadmin(7);?>, <?=jumterlambatblnadmin(8);?>, <?=jumterlambatblnadmin(9);?>, <?=jumterlambatblnadmin(10);?>, <?=jumterlambatblnadmin(11);?>, <?=jumterlambatblnadmin(12);?>]
                }, {
                    name: 'Tidak Hadir',
                    data: [<?=jumalphablnadmin(1);?>, <?=jumalphablnadmin(2);?>, <?=jumalphablnadmin(3);?>, <?=jumalphablnadmin(4);?>, <?=jumalphablnadmin(5);?>, <?=jumalphablnadmin(6);?>, <?=jumalphablnadmin(7);?>, <?=jumalphablnadmin(8);?>, <?=jumalphablnadmin(9);?>, <?=jumalphablnadmin(10);?>, <?=jumalphablnadmin(11);?>, <?=jumalphablnadmin(12);?>]
                }],
                xaxis: {
                    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags','Sept','Okt','Nov','Des'],
                },
                yaxis: {
                    title: {
                        text: 'Jumlah'
                    }
                },
                fill: {
                    opacity: 1

                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val
                        }
                    }
                }
            }
            var chart = new ApexCharts(
                document.querySelector("#bar-chart-1"),
                options
            );
            chart.render();
        });

    }, 700);
});
</script>