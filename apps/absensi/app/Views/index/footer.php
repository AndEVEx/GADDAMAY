<!-- Footer Copyright -->
<footer class="text-center py-3 mt-4"
    style="background: #f1f1f1; border-top: 1px solid #ddd; color: #666; font-size: 13px;">
    &copy; <?= date('Y') ?> Develop by <strong>SMKN 2 INDRAMAYU</strong>
</footer>

<!-- Required Js -->
<script src="<?= base_url() ?>template/assets/js/vendor-all.min.js"></script>
<script src="<?= base_url() ?>template/assets/js/plugins/bootstrap.min.js"></script>
<script src="<?= base_url() ?>template/assets/js/ripple.js"></script>
<script src="<?= base_url() ?>template/assets/js/pcoded.min.js"></script>
<script src="<?= base_url() ?>template/assets/js/plugins/apexcharts.min.js"></script>

<script src="<?= base_url() ?>template/assets/js/webcamjs/webcam.min.js"></script>

<!-- prism Js -->
<script src="<?= base_url() ?>template/assets/js/plugins/prism.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap4.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.2/js/dataTables.buttons.js"></script>

<script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.bootstrap4.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.colVis.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php if (session()->getFlashdata('success')): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Sukses!',
            text: '<?= session()->getFlashdata('success') ?>',
            showConfirmButton: false,
            timer: 2000
        });
    </script>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: '<?= session()->getFlashdata('error') ?>',
        });
    </script>
<?php endif; ?>
<script>
    if ($.fn.DataTable && !$.fn.dataTable.isDataTable('#example1')) {
        new DataTable('#example1', {
            pageLength: -1,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'Semua']]
        });
    }
    if ($.fn.DataTable && !$.fn.dataTable.isDataTable('#example')) {
        $('#example').DataTable({
            pageLength: -1,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'Semua']],
            buttons: ['copy', 'csv', 'excel']
        });
    }
    if ($('#single-select-field').length) {
        $('#single-select-field').select2({
            theme: "bootstrap-5",
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            placeholder: $(this).data('placeholder'),
        });
    }
</script>
<script>
    if ($.fn.DataTable && !$.fn.dataTable.isDataTable('#example2')) {
        new DataTable('#example2', {
            orderCellsTop: true,
            pageLength: -1,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'Semua']],
            layout: {
                topStart: {
                    buttons: ['copy', 'excel', 'pdf', 'colvis']
                }
            }
        });
    }
</script>

<script>
    $(document).ready(function() {
        setTimeout(function() {
            $('.loader-bg').fadeOut('slow', function() {
                $(this).remove();
            });
        }, 200);
    });
</script>
</body>

</html>