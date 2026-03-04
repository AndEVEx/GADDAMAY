    <!-- Required Js -->
    <script src="<?=base_url()?>/template/assets/js/vendor-all.min.js"></script>
    <script src="<?=base_url()?>/template/assets/js/plugins/bootstrap.min.js"></script>
    <script src="<?=base_url()?>/template/assets/js/ripple.js"></script>
    <script src="<?=base_url()?>/template/assets/js/pcoded.min.js"></script>
    <script src="<?=base_url()?>/template/assets/js/plugins/apexcharts.min.js"></script>

    <script src="<?=base_url()?>/template/assets/js/webcamjs/webcam.min.js"></script>

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

<?php 
if ($getAgent->isBrowser()) {
?>
<!-- SCRIPT ABSENSI DENGAN WEBCAME VIA PC -->
<script type="text/javascript">
    // start webcame
    Webcam.set({
        width: 390,height: 260,
        image_format: 'jpeg',
        jpeg_quality:80,
    });

    var cameras = new Array(); //create empty array to later insert available devices
    navigator.mediaDevices.enumerateDevices() // get the available devices found in the machine
    .then(function(devices) {
        devices.forEach(function(device) {
        var i = 0;
            if(device.kind=== "videoinput"){ //filter video devices only
                cameras[i]= device.deviceId; // save the camera id's in the camera array
                i++;
            }
        });
    })

    // Set Camera Depan =========
    Webcam.set('constraints',{
        width: 390,
        height: 260,
        image_format: 'jpeg',
        jpeg_quality:80,
        sourceId: cameras[0]
    });

    Webcam.attach('.webcam-capture');
    // preload shutter audio clip
    var shutter = new Audio();
    //shutter.autoplay = true;
    shutter.src = navigator.userAgent.match(/Firefox/) ? '<?=base_url()?>/template/assets/js/webcamjs/shutter.ogg' : '<?=base_url()?>/template/assets/js/webcamjs/shutter.mp3';
    function captureimage() {
    var latitude = $('.latitude').html();
        shift_id = $('.selected').html();
        // play sound effect
        shutter.play();
        // take snapshot and get image data
        Webcam.snap( function(data_uri) {
            document.form1.webcam.value = data_uri;
        } );
    }

    function take_snapshot() {
    // play sound effect
    shutter.play();
 
    //  snapshot dan mendapatkan data gambar
    Webcam.snap( function(data_uri) {
        $(".image-tag").val(data_uri);
       // display results in page
       document.getElementById('results').innerHTML = 
           '<img src="'+data_uri+'"/>';
     } );
     
     $("#captured_image_data").val(data_uri);
     Webcam.reset();
 }
</script>

<script>
		if(geo_position_js.init()){
			geo_position_js.getCurrentPosition(success_callback,error_callback,{enableHighAccuracy:true});
		}
		else{
			alert("Functionality not available");
		}

		function success_callback(p)
		{
            
			lat=''+p.coords.latitude.toFixed(5)+','+p.coords.longitude.toFixed(5);
            
            document.form1.lat.value = lat;
		}
		
		function error_callback(p)
		{
			alert('error='+p.message);
		}		
</script>

<?php
} elseif ($getAgent->isMobile()) {
?>
<!-- SCRIPT ABSENSI DENGAN WEBCAME VIA MOBILE -->
<script type="text/javascript">
    var result;
    $(document).ready(function getLocation() {
        result = document.getElementById("latitude");
       // 
        if(navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(successCallback, errorCallback);
        } else {
            swal({title: 'Oops!', text:'Maaf, browser Anda tidak mendukung geolokasi HTML5.', icon: 'error', timer: 3000,});
        }
    });
    
    // Define callback function for successful attempt
    function successCallback(position) {
       result.innerHTML =""+ position.coords.latitude + ","+position.coords.longitude + "";
    }

    // Define callback function for failed attempt
    function errorCallback(error) {
        if(error.code == 1) {
            swal({title: 'Oops!', text:'Anda telah memutuskan untuk tidak membagikan posisi Anda, tetapi tidak apa-apa. Kami tidak akan meminta Anda lagi.', icon: 'error', timer: 3000,});
        } else if(error.code == 2) {
            swal({title: 'Oops!', text:'Jaringan tidak aktif atau layanan penentuan posisi tidak dapat dijangkau.', icon: 'error', timer: 3000,});
        } else {
            swal({title: 'Oops!', text:'Waktu percobaan habis sebelum bisa mendapatkan data lokasi.', icon: 'error', timer: 3000,});
        }
    }

    $(document).on('change','#webcam',function(){
        var file_data = $('#webcam').prop('files')[0];  
        var image_name = file_data.name;
        var image_extension = image_name.split('.').pop().toLowerCase();
        var latitude = $('.latitude').html();
        $('.progress').show();

        if(jQuery.inArray(image_extension,['gif','jpg','jpeg']) == -1){
          swal({title: 'Oops!', text: 'File yang di unggah tidak sesuai dengan format, File harus jpg, jpeg, gif.!', icon: 'error', timer: 2000,});
        }

        var form_data = new FormData();
        form_data.append("webcam",file_data);
        $.ajax({
            xhr : function() {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener('progress', function(e){
                    if(e.lengthComputable){
                        console.log('Bytes Loaded : ' + e.loaded);
                        console.log('Total Size : ' + e.total);
                        console.log('Persen : ' + (e.loaded / e.total));
                        var percent = Math.round((e.loaded / e.total) * 100);
                        $('#progressBar').attr('aria-valuenow', percent).css('width', percent + '%').text(percent + '%');
                    }
                });
                return xhr;
            },
          url:'./sw-proses?action=absent&latitude='+latitude+'',
          method:'POST',
          data:form_data,
          contentType:false,
          cache:false,
          processData:false,
          success:function(data){
                var results = data.split("/");
                $results = results[0];
                $results2 = results[1];
                if($results =='success'){
                    swal({title: 'Berhasil!', text:$results2, icon: 'success', timer: 3500,});
                    setTimeout("location.href = './';",3600);
                    $('.progress').delay(500).fadeOut('slow');
                } else {
                    swal({title: 'Oops!', text: data, icon: 'error', timer: 3500,});
                    $('.progress').delay(500).fadeOut('slow');
                }
          }
        });
});
</script>
<?php
} else {
    $currentAgent = 'Unidentified User Agent';
}
?>
    

</body>

</html>
