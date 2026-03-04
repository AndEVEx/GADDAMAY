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
  body {
   background: #282828;
   font-family: tahoma;
   color: #fff;
}
h1 {
   margin: 20px 10%;
   font-size: 60px;
   letter-spacing: 10px;
}
.jam-digital {
   width: 100%;
   margin: 10% 30%;
}
#jam span {
   float: left;
   text-align: center;
   font-size: 40px;
   margin: 0 2.5%;
   padding: 20px;
   width: 20%;
   border-radius: 10px;
   box-sizing: border-box;
}
#jam span:nth-child(1) {
   background: #a70c0c;
}
#jam span:nth-child(2) {
   background: #f6ab29;
}
#jam span:nth-child(3) {
   background: #298f19;
}
#jam::after {
   content: "";
   display: block;
   clear: both;
}
#unit span {
   float: left;
   width: 20%;
   margin-top: 30px;
   text-align: center;
   text-transform: uppercase;
   letter-spacing: 2px;
   font-size: 18px;
   text-shadow: 1px 1px 1px rgba(10, 10, 10, 0.7)
}
span.turn {
   animation: turn 0.7s ease;
}
@keyframes turn {
   0% {transform: rotateX(0deg)}
   100% {transform: rotateX(360deg)}
}
@media screen and (max-width: 980px){
   #jam span {
      float: left;
      text-align: center;
      font-size: 50px;
      margin: 0 1.5%;
      padding: 20px;
      width: 20%;
   }
   h1 {
      margin: 20px 5%;
   }
   .jam-digital {
      width: 100%;
      margin: 10% 20%;
   }
   #unit span {
      width: 23%;
   }
}
</style>

</head>

<body>