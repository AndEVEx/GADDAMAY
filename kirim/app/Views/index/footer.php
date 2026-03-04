    <!-- Required Js -->
    <script src="<?=base_url()?>/template/assets/js/vendor-all.min.js"></script>
    <script src="<?=base_url()?>/template/assets/js/plugins/bootstrap.min.js"></script>
    <script src="<?=base_url()?>/template/assets/js/ripple.js"></script>
    <script src="<?=base_url()?>/template/assets/js/pcoded.min.js"></script>
    <script src="<?=base_url()?>/template/assets/js/plugins/apexcharts.min.js"></script>


    <!-- prism Js -->
    <script src="<?=base_url()?>/template/assets/js/plugins/prism.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        new DataTable('#example1');
        $('#example').DataTable( {
            buttons: [ 'copy', 'csv', 'excel' ]
        } );
        $( '#single-select-field' ).select2( {
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
        } );
    </script>
    
</body>

</html>
