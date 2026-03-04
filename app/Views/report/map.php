<?php 
$lat = $_GET['lat'];
?>
	<script>
		if(geo_position_js.init()){
			geo_position_js.getCurrentPosition(success_callback,error_callback,{enableHighAccuracy:true});
		}
		else{
			alert("Functionality not available");
		}

		function success_callback(p)
		{
			geo_position_js.showMap(<?= $lat ?>);
		}
		
		function error_callback(p)
		{
			alert('error='+p.message);
		}		
	</script>
