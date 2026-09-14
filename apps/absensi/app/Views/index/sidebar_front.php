<!DOCTYPE html>
<html lang="en">

<head>
    <title>Absensi Online</title>
    <!-- HTML5 Shim and Respond.js IE11 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 11]>
    	<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    	<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    	<![endif]-->
    <!-- Meta -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="" />
    <meta name="keywords" content="">
    <meta name="author" content="Phoenixcoded" />
    <!-- Favicon icon -->
    <link rel="icon" href="<?=base_url()?>/template/assets/images/favicon.ico" type="image/x-icon">

    <!-- prism css -->
    <link rel="stylesheet" href="<?=base_url()?>/template/assets/css/plugins/prism-coy.css">
    <!-- vendor css -->
    <link rel="stylesheet" href="<?=base_url()?>/template/assets/css/style.css">
    
<style>

.clock {
  width: 24.00em;
  height: 8em;
  position: absolute;
  transform: translate(-50%, -50%);
  top: 50%;
  left: 50%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-weight: 600;
}
.clock div {
  position: relative;
  background-color: #ffffff;
  height: 100%;
  width: 3.2em;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2.5em;
  color: #150c41;
  border-radius: 0.4em;
  box-shadow: 0 1em 2em rgba(0, 0, 0, 0.3);
  letter-spacing: 0.05em;
}
.clock span {
  font-weight: 800;
  font-size: 2.5em;
  color: #ffffff;
}
</style>

</head>

<body>