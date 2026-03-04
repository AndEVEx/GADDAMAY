  
  <!-- Required Js -->
        <script src="<?=base_url()?>/template/assets/js/vendor-all.min.js"></script>
        <script src="<?=base_url()?>/template/assets/js/plugins/bootstrap.min.js"></script>
        <script src="<?=base_url()?>/template/assets/js/ripple.js"></script>
        <script src="<?=base_url()?>/template/assets/js/pcoded.min.js"></script>


    <!-- prism Js -->
    <script src="<?=base_url()?>/template/assets/js/plugins/prism.js"></script>

    



    <script src="<?=base_url()?>/template/assets/js/horizontal-menu.js"></script>
    <script>
        const hour = document.getElementById("hour");
const minute = document.getElementById("minute");
const seconds = document.getElementById("seconds");

const clock = setInterval(function time() {
  const dateNow = new Date();
  let hr = dateNow.getHours();
  let min = dateNow.getMinutes();
  let sec = dateNow.getSeconds();

  hr = hr.toString().padStart(2, "0");
  min = min.toString().padStart(2, "0");
  sec = sec.toString().padStart(2, "0");

  const timeString = `${hr}:${min}:${sec}`;
  hour.textContent = hr;
  minute.textContent = min;
  seconds.textContent = sec;
}, 1000);
    </script>

    <script src="<?=base_url()?>/template/assets/js/analytics.js"></script>

</body>

</html>
