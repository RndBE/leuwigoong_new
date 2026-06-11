<html lang="en">

	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
		<meta http-equiv="X-UA-Compatible" content="ie=edge" />
		<title>DI Leuwigoong</title>
		<style>
			@import url('https://rsms.me/inter/inter.css');

			:root {
				--tblr-font-sans-serif: Inter, -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif !important;
			}
		</style>
		<!-- CSS files -->
		<link rel="icon" href="<?php echo base_url() ?>image/logopu 4.png">
		<link href="https://stesy.beacontelemetry.com/assets/code/tabler.min.css" rel="stylesheet"/>
		<link href="https://stesy.beacontelemetry.com/assets/code/tabler-flags.min.css" rel="stylesheet"/>
		<link href="https://stesy.beacontelemetry.com/assets/code/tabler-payments.min.css" rel="stylesheet"/>
		<link href="https://stesy.beacontelemetry.com/assets/code/tabler-vendors.min.css" rel="stylesheet"/>
		<link href="https://stesy.beacontelemetry.com/assets/code/demo.min.css" rel="stylesheet"/>

	</head>

	<body class="border-top-wide border-primary d-flex flex-column">
		<div class="page page-center">
			<div class="container-tight py-4 my-auto">
				<div class="text-center mb-4">
					<a href="#" class="navbar-brand navbar-brand-autodark" style="padding-left:20px;padding-right:20px"><img src="<?php echo base_url() ?>image/login.png" alt="Logo Cimancis"></a>
				</div>

				<?php echo form_open('login/validasi_login', 'id="loginform" autocomplete="off" class="card card-md"') ?>

				<div class="card-body">

					<div class="mb-3">
						<label class="form-label">Nama Pengguna</label>
						<input type="text" class="form-control" placeholder="Username" name="username" value="<?php echo set_value('username') ?>" autocomplete="off">
					</div>
					<div class="mb-2">
						<label class="form-label">
							Kata Sandi
						</label>
						<div class="input-group input-group-flat">
							<input type="password" id="typepass" name="password" class="form-control" placeholder="Kata Sandi" value="<?php echo set_value('password') ?>" autocomplete="off">
							<span class="input-group-text">
								<a href="#" id="btneye" class="link-secondary ps-2" onclick="show()" title="Tampilkan kata sandi">
									<img id="imgeye" src="<?php echo base_url() ?>image/template/eye.svg" height="24" width="24" alt="" /> </a>
							</span>
						</div>
					</div>

					<div class="form-footer">
						<button type="submit" class="btn w-100" style="background:#303481;color:white;">Masuk</button>
					</div>
				</div>

				<?php echo form_close(); ?>
				<?php echo form_error('username'); ?>
				<?php echo form_error('password'); ?>
				<?php echo $this->session->flashdata('message'); ?>
				<?php echo $this->session->flashdata('error'); ?>
			</div>
		</div>
		<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
			<div class="modal-dialog modal-sm modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-body d-flex justify-content-center flex-column align-items-center">

						<svg width="80px" height="80px" class="text-danger" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" class="mb-2"><title>ionicons-v5-r</title><path d="M85.57,446.25H426.43a32,32,0,0,0,28.17-47.17L284.18,82.58c-12.09-22.44-44.27-22.44-56.36,0L57.4,399.08A32,32,0,0,0,85.57,446.25Z" style="fill:none;stroke:#000000;stroke-linecap:round;stroke-linejoin:round;stroke-width:32px"/><path d="M250.26,195.39l5.74,122,5.73-121.95a5.74,5.74,0,0,0-5.79-6h0A5.74,5.74,0,0,0,250.26,195.39Z" style="fill:none;stroke:#000000;stroke-linecap:round;stroke-linejoin:round;stroke-width:32px"/><path d="M256,397.25a20,20,0,1,1,20-20A20,20,0,0,1,256,397.25Z"/></svg>
						<h3 class="mb-0 mt-2">Sistem Sedang Dimatikan</h3>
					</div>
				</div>
			</div>
		</div>

		<!-- Libs JS -->
		<!-- Tabler Core -->
		<script src="https://stesy.beacontelemetry.com/assets/code/tom-select.base.min.js" defer></script>
<script src="https://stesy.beacontelemetry.com/assets/code/tabler.min.js" defer></script>
<script src="https://stesy.beacontelemetry.com/assets/code/demo.min.js" defer></script>
		<script type="text/javascript">
			/*
			document.addEventListener('DOMContentLoaded', function () {
				const modal = new bootstrap.Modal(document.getElementById('staticBackdrop'))
				modal.show()
			})*/
			
			function getLocation() {
				if (navigator.geolocation) {
					navigator.geolocation.getCurrentPosition(showPosition, showError);
				} else {
					document.getElementById("location").innerText = "Geolocation is not supported by this browser.";
				}
			}

			function showPosition(position) {
				const latitude = position.coords.latitude;
				const longitude = position.coords.longitude;
				console.log(latitude);
				// Send data to the server
			}

			function showError(error) {
				switch (error.code) {
					case error.PERMISSION_DENIED:
						document.getElementById("location").innerText = "User denied the request for Geolocation.";
						break;
					case error.POSITION_UNAVAILABLE:
						document.getElementById("location").innerText = "Location information is unavailable.";
						break;
					case error.TIMEOUT:
						document.getElementById("location").innerText = "The request to get user location timed out.";
						break;
					case error.UNKNOWN_ERROR:
						document.getElementById("location").innerText = "An unknown error occurred.";
						break;
				}
			}
			function show() {
				var temp = document.getElementById("typepass");
				var imgeye = document.getElementById("imgeye");
				var btneye = document.getElementById("btneye");
				if (temp.type === "password") {
					temp.type = "text";
					imgeye.src = "<?php echo base_url() ?>image/template/eye-off.svg";
					btneye.title = "Sembunyikan kata sandi";
				} else {
					temp.type = "password";
					imgeye.src = "<?php echo base_url() ?>image/template/eye.svg";
					btneye.title = "Tampilkan kata sandi";
				}
			}
		</script>
	</body>

</html>