<!-- [ auth-signin ] start -->
<div class="auth-wrapper">
	<div class="auth-content">
		<div class="card">
			<div class="row align-items-center text-center">
				<div class="col-md-12">
					<div class="card-body">
					<?php if(session()->get('error')) : ?>
					<div class="alert alert-danger" role="alert">
						<h4 class="alert-heading">Login Gagal!</h4>
						<p><?=session()->get('error');?>
						</p>
					</div>
					<?php endif; ?>

					<?php if(session()->get('errorpass')) : ?>
					<div class="alert alert-danger" role="alert">
						<h4 class="alert-heading">Login Gagal!</h4>
						<p><?=session()->get('errorpass');?>
						</p>
					</div>
					<?php endif; ?>
						<img src="<?=base_url()?>/image/<?= $getLogo ?>" width="100">
						<br><br>
						<h4 class="mb-3 f-w-400">Signin</h4>
 
					<form class="pt-3" action="<?= base_url('Cpanel/process'); ?>" method="post">
						<div class="form-group mb-3 text-left">
							<label>Username</label>
							<input type="text" class="form-control" name="email" placeholder="NIP / NISN" required>
						</div>
						<div class="form-group mb-4 text-left">
							<label>Password</label>
							<div class="input-group">
								<input type="password" class="form-control" name="password" id="passwordInput" placeholder="Password" required>
								<div class="input-group-append">
									<button class="btn btn-outline-secondary" type="button" id="togglePassword" tabindex="-1" style="border-color: #ced4da;">
										<i class="feather icon-eye" id="eyeIcon"></i>
									</button>
								</div>
							</div>
						</div>
						<div class="form-group mb-4">
							<select class="form-control" name="tapel" required>
								<option value=0>Pilih Tapel</option>
								<?php foreach ($getTapel as $data) { ?>
								<option value="<?=$data['id_tapel'] ?>"><?=$data['nm_tapel'] ?></option>
								<?php } ?>
							</select>
						</div>
						<button class="btn btn-block btn-primary mb-4">Signin</button>
						 
					</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- [ auth-signin ] end -->
<script>
document.getElementById('togglePassword').addEventListener('click', function() {
    var input = document.getElementById('passwordInput');
    var icon = document.getElementById('eyeIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'feather icon-eye-off';
    } else {
        input.type = 'password';
        icon.className = 'feather icon-eye';
    }
});
</script>