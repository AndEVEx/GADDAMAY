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
						<div class="form-group mb-3">
							<label class="floating-label">Username</label>
							<input type="text" class="form-control" name="email" required>
						</div>
						<div class="form-group mb-4">
							<label class="floating-label">Password</label>
							<input type="password" class="form-control" name="password" required>
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