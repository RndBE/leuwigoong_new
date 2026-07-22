
<!DOCTYPE html>
<html>
	<head>
		<title>DI Leuwigoong</title>
		<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
		<link rel="icon" href="<?php echo base_url()?>image/logopu 4.png">
		<link href="https://stesy.beacontelemetry.com/assets/code/tabler.min.css" rel="stylesheet"/>
		<link href="https://stesy.beacontelemetry.com/assets/code/tabler-flags.min.css" rel="stylesheet"/>
		<link href="https://stesy.beacontelemetry.com/assets/code/tabler-payments.min.css" rel="stylesheet"/>
		<link href="https://stesy.beacontelemetry.com/assets/code/tabler-vendors.min.css" rel="stylesheet"/>
		<link href="https://stesy.beacontelemetry.com/assets/code/demo.min.css" rel="stylesheet"/>
		<script src="https://stesy.beacontelemetry.com/assets/code/tom-select.complete.min.js" defer></script>
		<script src="https://stesy.beacontelemetry.com/assets/code/tabler.min.js" defer></script>
		<script src="https://stesy.beacontelemetry.com/assets/code/demo.min.js" defer></script>
		<script src="<?php echo base_url();?>code/highcharts.js"></script>
		<script src="<?php echo base_url();?>code/highcharts-more.js"></script>
		<script src="<?php echo base_url();?>code/modules/series-label.js"></script>
		<script src="<?php echo base_url();?>code/modules/exporting.js"></script>
		<script src="<?php echo base_url();?>code/modules/export-data.js"></script>
		<script src="<?php echo base_url();?>code/js/themes/grid.js"></script>
		<script
				src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA0za7gSm6K-8eFKK-np3jhyyW5IMRVSb8"
				async
				defer
				></script>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
		<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/js/all.min.js" integrity="sha512-6sSYJqDreZRZGkJ3b+YfdhB3MzmuP9R7X1QZ6g5aIXhRvR1Y/N/P47jmnkENm7YL3oqsmI6AK+V6AD99uWDnIw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
		<style>
			.gm-style-iw {
				width:420px;
				max-width: 550px; 
				min-height: 150px;
			}
			.gm-style-iw-chr{
				position:absolute;
				right:0px
			}
			*::-webkit-scrollbar {
				display: none;
			}
			#map {
				height: 100%;
				width: 100%;
			}
			html, body {
				height: 100%;
				margin: 0;
				padding: 0;
			}
			#tes{
				border:2px solid #FFD61580;
				background-color:#30348180;
				justify-content:space-between;
				display:flex;
				align-items:center;
				padding:0px 20px;
				width:calc(100% - 40px);
				height:75px;
				box-sizing: border-box;
				position:absolute;

				border-radius:5px;
				margin-top:20px
			}

			#left_map{
				margin-top:110px;
				max-height:80vh;
				overflow-y:scroll;
				scrollbar-width: none;
				overflow-x: hidden;
				border:2px solid #FFD61580;
				position:absolute;
				margin-left:20px;
				border-radius:5px;
				background-color:#30348140;
			}

			#right_map{
				margin-top:110px;
				height:80vh;
				border:2px solid #FFD61580;
				position:absolute;
				margin-right:20px;
				border-radius:5px;
				background-color:#30348140;
				display: flex;
				flex-direction: column;
			}
			#loader {
				position: absolute;
				inset: 0;
				z-index: 3;
				display: flex;
				align-items: center;
				justify-content: center;
				margin: 0 !important;
				padding: 0 !important;
				pointer-events: none;
				background: linear-gradient(180deg, rgba(48, 52, 129, 0.18), rgba(48, 52, 129, 0.52));
			}
			#loader i {
				font-size: 28px !important;
			}
			#analisa-right {
				transition: opacity 180ms ease, filter 180ms ease;
			}
			#right_map.detail-loading #analisa-right {
				opacity: 0.56;
				filter: blur(0.3px);
			}
			#pilih_kat{
				font-size:14px;
				font-weight:bold;
				color:white;
				background-color:#30348180;
				border:2px solid #FFD61580;
				border-radius:5px;
				padding:10px 10px;
			}
			@keyframes pulseBorder {
				0% {
					box-shadow: 0 0 0px rgba(255, 214, 21, 0.8);
					border-color: rgba(255, 214, 21, 0.8);
				}
				50% {
					box-shadow: 0 0 10px rgba(255, 214, 21, 1);
					border-color: rgba(255, 214, 21, 1);
				}
				100% {
					box-shadow: 0 0 0px rgba(255, 214, 21, 0.8);
					border-color: rgba(255, 214, 21, 0.8);
				}
			}

			.border-pulse {
				animation: pulseBorder 2s cubic-bezier(0.4, 0, 0.2, 1); /* Pulse for 1 second */
			}
			@keyframes mobilePanelIn {
				from {
					opacity: 0;
					transform: translateY(10px);
				}
				to {
					opacity: 1;
					transform: translateY(0);
				}
			}

			.map-info-card {
				width: 360px;
				color: #1f2937;
				font-family: inherit;
			}

			.map-info-title {
				padding: 12px 42px 10px 14px;
				font-size: 16px;
				font-weight: 800;
				line-height: 1.2;
			}

			.map-info-table {
				width: calc(100% - 28px);
				margin: 0 14px;
				border-collapse: collapse;
				border: 1px solid #d8dee8;
				font-size: 12px;
			}

			.map-info-table td {
				padding: 9px 12px;
				border: 1px solid #d8dee8;
				color: #5f6b7a;
			}

			.map-info-table td:first-child {
				width: 48%;
			}

			.map-info-actions {
				display: flex;
				justify-content: center;
				gap: 24px;
				padding: 12px 10px 14px;
				font-size: 13px;
				font-weight: 800;
			}

			.map-info-actions a {
				display: inline-flex;
				align-items: center;
				gap: 5px;
				color: #0069bd;
				text-decoration: none;
				white-space: nowrap;
			}

			#tes .brand-mark {
				display: none;
			}

			#tes .peta-map-brand {
				display: flex;
				align-items: center;
				min-width: 0;
				gap: 12px;
			}

			#tes .peta-map-brand img {
				width: auto;
				max-width: 42vw;
				height: 40px;
				object-fit: contain;
				object-position: left center;
			}

			#tes .peta-map-actions {
				display: flex;
				align-items: center;
				min-width: 0;
			}

			#tes .weather-chip-location {
				display: flex;
				align-items: center;
				gap: 0;
				min-height: 52px;
				margin-right: 16px;
				padding: 7px 8px;
				color: #fff;
				border: 1px solid #fff;
				border-radius: 5px;
				background: transparent;
			}

			#tes .weather-chip-location img {
				width: auto;
				height: 35px;
				object-fit: contain;
			}

			#tes .weather-temp-location {
				display: flex;
				align-items: flex-start;
				margin-left: 8px;
				font-size: 26px;
				font-weight: 700;
				line-height: 1;
			}

			#tes .weather-temp-location span {
				font-size: 12px;
				font-weight: 700;
				line-height: 1;
				padding-top: 2px !important;
			}

			#tes .weather-temp-location h1 {
				margin: 0 !important;
				font-size: inherit;
				font-weight: inherit;
				line-height: inherit;
			}

			#tes .weather-desc-location {
				margin-left: 14px;
				font-size: 12px;
				font-weight: 700;
				line-height: 1.1;
				white-space: nowrap;
			}

			#tes .weather-desc-location h5 {
				margin: 0 !important;
				font-size: inherit;
				font-weight: inherit;
				line-height: inherit;
			}

			#tes .nav-action-location {
				position: relative;
				display: inline-flex;
				align-items: center;
				gap: 8px;
				margin-right: 16px;
				padding: 7px 0;
				color: #fff;
				background: transparent;
				border: 0;
				font-size: 16px;
				font-weight: 700;
				text-decoration: none;
				white-space: nowrap;
			}

			#tes .nav-action-location.active::after {
				position: absolute;
				left: 50%;
				bottom: 1px;
				width: 40px;
				height: 2px;
				background: #FFD615;
				content: "";
				transform: translateX(-50%);
			}

			#tes .nav-action-location svg {
				width: 22px;
				height: 22px;
				flex: 0 0 auto;
				margin-right: 0 !important;
			}

			#tes .download-nav-location {
				margin-right: 16px;
			}

			#tes .download-nav-location .nav-action-location {
				margin-right: 0;
			}

			.mobile-map-actions {
				display: none;
			}

			@media (max-width: 991.98px) {
				:root {
					--mobile-edge: clamp(8px, 2.8vw, 14px);
					--mobile-edge-double: calc(var(--mobile-edge) + var(--mobile-edge));
					--mobile-actions-height: clamp(38px, 11vw, 44px);
					--mobile-actions-bottom: max(10px, env(safe-area-inset-bottom, 0px));
					--mobile-panel-bottom: calc(var(--mobile-actions-bottom) + var(--mobile-actions-height) + 8px);
					--mobile-top-clear: 84px;
					--mobile-detail-height: min(50dvh, 390px);
					--mobile-logger-height: min(58dvh, 430px);
				}

				html,
				body {
					height: 100dvh;
					overflow: hidden;
				}

				#map {
					height: 100dvh;
				}

				.gm-style-iw {
					width: auto !important;
					max-width: calc(100vw - 32px) !important;
					min-width: 0 !important;
					min-height: 0;
				}

				.gm-style .gm-style-iw-c {
					padding: 0 !important;
					border-radius: 8px !important;
					box-shadow: 0 12px 28px rgba(22, 34, 59, 0.22) !important;
					max-width: calc(100vw - var(--mobile-edge-double) - 34px) !important;
				}

				.gm-style .gm-style-iw-tc {
					filter: drop-shadow(0 3px 4px rgba(22, 34, 59, 0.16));
				}

				.gm-style-iw-d {
					max-height: none !important;
					overflow: hidden !important;
				}

				.map-info-card {
					width: min(clamp(210px, 68vw, 252px), calc(100vw - var(--mobile-edge-double) - 46px));
				}

				.map-info-title {
					padding: 10px 34px 8px 12px;
					font-size: 12px;
				}

				.map-info-table {
					width: calc(100% - 22px);
					margin: 0 11px;
					font-size: clamp(9px, 2.7vw, 10.5px);
				}

				.map-info-table td {
					padding: clamp(6px, 1.9vw, 8px) clamp(7px, 2.2vw, 9px);
				}

				.map-info-actions {
					gap: clamp(10px, 4vw, 16px);
					padding: 10px 8px 12px;
					font-size: clamp(9.5px, 2.8vw, 10.5px);
				}

				.map-info-actions svg {
					width: 14px;
					height: 14px;
				}

				#tes {
					width: calc(100vw - 20px);
					height: 64px;
					margin-top: 10px;
					padding: 0 10px;
					gap: 7px;
				}

				#tes .peta-map-brand {
					flex: 0 0 auto;
					min-width: 0;
				}

				#tes .brand-full {
					display: none;
				}

				#tes .peta-map-brand .brand-mark {
					display: block;
					width: 28px;
					height: 28px;
					max-width: none;
					object-fit: contain;
					flex: 0 0 auto;
				}

				#tes .peta-map-actions {
					flex: 0 0 auto;
					min-width: 0;
				}

				#tes .map-nav-item {
					display: contents;
				}

				#tes .weather-chip-location {
					display: flex;
					min-height: 28px;
					margin-right: 6px !important;
					padding: 2px 5px !important;
				}

				#tes .weather-chip-location img {
					height: 20px !important;
				}

				#tes .weather-temp-location {
					margin-left: 4px;
					font-size: 16px;
				}

				#tes .weather-temp-location span {
					font-size: 9px;
					padding-top: 1px;
				}

				#tes .weather-desc-location {
					margin-left: 6px !important;
					font-size: 8px;
					line-height: 1.08;
				}

				#tes .nav-action-location {
					width: 34px;
					height: 34px;
					justify-content: center;
					margin-right: 5px !important;
					padding: 0 !important;
					font-size: 0 !important;
					gap: 0;
				}

				#tes .nav-action-location .nav-link-icon {
					display: flex !important;
					align-items: center;
					justify-content: center;
					width: 21px;
					height: 21px;
					margin: 0 !important;
					padding: 0 !important;
				}

				#tes .nav-action-location svg {
					width: 21px;
					height: 21px;
					margin: 0 !important;
				}

				#tes .nav-action-location.active::after {
					left: 50%;
					bottom: -5px;
					width: 24px;
					transform: translateX(-50%);
				}

				#tes .download-nav-location {
					margin-right: 5px !important;
				}

				#tes .dropdown-toggle::after {
					display: none;
				}

				#left_map,
				#right_map {
					display: none !important;
					position: fixed !important;
					left: var(--mobile-edge) !important;
					right: var(--mobile-edge) !important;
					bottom: var(--mobile-panel-bottom) !important;
					width: auto !important;
					max-width: none !important;
					margin: 0 !important;
					z-index: 1000;
					border: 2px solid #FFD61580;
					border-radius: 6px;
					background-color: #303481cc;
					backdrop-filter: blur(5px);
				}

				#left_map.mobile-open,
				#right_map.mobile-open {
					display: block !important;
					animation: mobilePanelIn 160ms ease-out;
				}

				#left_map {
					max-height: min(var(--mobile-logger-height), calc(100dvh - var(--mobile-panel-bottom) - var(--mobile-top-clear)));
					overflow-y: auto;
					padding-bottom: 10px;
				}

				#right_map {
					height: min(var(--mobile-detail-height), calc(100dvh - var(--mobile-panel-bottom) - var(--mobile-top-clear)));
					overflow: hidden;
				}

				#right_map #chart_analisa {
					min-height: clamp(92px, 18dvh, 120px);
				}

				#right_map table {
					font-size: 11px;
				}

				.mobile-map-actions {
					position: fixed;
					left: var(--mobile-edge);
					right: var(--mobile-edge);
					bottom: var(--mobile-actions-bottom);
					z-index: 1001;
					display: grid;
					grid-template-columns: 1fr 1fr;
					gap: 8px;
					pointer-events: auto;
				}

				.mobile-map-action {
					height: var(--mobile-actions-height);
					color: #fff;
					background: #303481cc;
					border: 2px solid #FFD61580;
					border-radius: 5px;
					font-size: clamp(12px, 3.4vw, 13px);
					font-weight: 800;
				}

				.mobile-map-action.active {
					color: #1f2937;
					background: #FFD615;
				}

				#ipcam_view .modal-dialog {
					margin: 10px;
				}

				#ipcam_body {
					height: 60dvh;
				}
			}

			@media (max-width: 380px), (max-width: 991.98px) and (max-height: 680px) {
				:root {
					--mobile-top-clear: 78px;
				}

				.map-info-title {
					padding-top: 9px;
					padding-bottom: 7px;
					font-size: 11px;
				}

				.map-info-actions {
					padding-top: 8px;
					padding-bottom: 10px;
				}

				#right_map .px-3.pt-3.pb-3 {
					padding-top: 10px !important;
					padding-bottom: 10px !important;
				}

				#right_map h3 {
					font-size: 13px;
				}

				#right_map h4 {
					font-size: 11px;
				}

			}
		</style>
	</head>
	<body>
		<div id="map"></div>
		<div class="container-fluid">
			<div class="row">
				<div class="col-xl-3 col-xxl-2 pb-3 pt-0 d-none d-lg-block" id="left_map">
					<div class="mb-3 px-3" style="background-color:#30348180;border-bottom:2px solid #FFD61580;">
						<h3 class="text-white fw-bold py-2 mb-0">List Logger</h3>
					</div>
					<div class="row gy-2 px-2">
						<?php foreach($data_konten as $key=>$vl) { ?> 
						<?php foreach($vl['logger'] as $k=> $v){ ?>

						<div class="col-12">
							<div class="card text-white" style="background-color:#30348180;border:2px solid #FFD61580;" id="sc_<?= $v['id_logger'] ?>">
								<div class="card-header px-3 py-2 d-flex justify-content-between " style="border-bottom:2px solid grey;">
									<div class="d-flex align-items-center"><div class="me-2" style="width:8px;height:8px;border-radius:50%;background-color:<?= $v['color'] ?>;"></div><p class="mb-0 fw-bold"><?= $v['status_logger'] ?></p></div>
									<p class="mb-0"><?= $v['waktu']?></p>
								</div>
								<div class="card-body px-3 py-2">
									<div class="d-flex justify-content-between align-items-center">
										<p class="fw-bold mb-0 h4"><?= $v['nama_lokasi'] ?></p>
										<div class="badge badge-outline text-white h-100 h6 mb-0 fw-bold">ID : <?= $v['id_logger'] ?></div>
									</div>


									<?php if($vl['controller'] == 'awgc'){ ?>
									<div class="row justify-content-center mb-2 gy-2 mt-2 ">
										<?php foreach($v['param'] as $y=>$s){ ?> 
										<?php if($s['parameter_utama'] == '1'){ ?>
										<div class="col-6 text-center">
											<h6 class="mb-0 fw-bold h3"><?= $s['nilai']?> <?= $s['satuan'] ?></h6>
											<p class="mb-0 h5 fw-normal"><a href="<?= $s['link'] ?>"><?= str_replace('_',' ',$s['nama_parameter']) ?></a></p>
										</div>
										<?php } ?>

										<?php } ?>
										<?php foreach($v['sensor'] as $y=>$s){ ?> 
										
										<div class="col-6 text-center">
											<h6 class="mb-0 fw-bold h3"><?= $s['elevasi']?> cm</h6>
											<p class="mb-0 h5 fw-normal">Elevasi <?= $s['nama_pintu'] ?></p>
										</div>

										<?php } ?>




									</div>
								
									<div class="rounded py-0 mt-2" style="border:2px solid #FFD6154F">
										<div class="row gx-0 justify-content-center">
											<?php foreach($v['param'] as $y=>$s){ ?> 
											<?php if($s['parameter_utama'] == '0'){ ?>
											<div class="col-4">
												<div class="<?= ($s['nama_parameter'] != 'Temperature_Logger') ? 'border-end':'' ?> d-flex justify-content-center align-items-center w-100 py-1">
													<img  src="<?= base_url() ?>image/<?= $s['icon_sensor']?>.svg" style="stroke:blue;height:16px" class=" text-white me-2 mb-0"/>
													<span class="fw-bold mb-0"><?= $s['nilai'] ?> <?= $s['satuan'] ?></span>
												</div>
											</div>
											<?php } ?>

											<?php } ?>

										</div>
									</div>
									<?php }else{ ?>
									<div class="row justify-content-center mb-2 gy-2 mt-2 ">
										<?php foreach($v['param'] as $y=>$s){ ?> 
										<?php if($s['parameter_utama'] == '1'){ ?>
										<div class="col-6 text-center">
											<h6 class="mb-0 fw-bold h3"><?= $s['nilai']?> <?= $s['satuan'] ?></h6>
											<p class="mb-0 h5 fw-normal"><a href="<?= $s['link'] ?>"><?= str_replace('_',' ',$s['nama_parameter']) ?></a></p>
										</div>
										<?php } ?>

										<?php } ?>

									</div>
									<?php if($vl['id_katlogger'] == '3') { ?>
										<?php 
	$link = $v['ipcam']; 
										?> 
									<img class="img-fluid rounded w-100" src="<?= $link ?>"/>
									<?php } ?>
									<?php 
												$param_bt = false;
												foreach($v['param'] as $y=>$s){  
													if($s['parameter_utama'] == '0'){ 
														$param_bt = true;
													}  
												} ?>
									<?php if($param_bt) { ?>
									<div class="rounded py-0 mt-3" style="border:2px solid #FFD6154F">
										<div class="row gx-0 justify-content-center">
											<?php foreach($v['param'] as $y=>$s){ ?> 
											<?php if($s['parameter_utama'] == '0'){ ?>
											<div class="col-4">
												<div class="<?= ($s['nama_parameter'] != 'Temperature_Logger') ? 'border-end':'' ?> d-flex justify-content-center align-items-center w-100 py-1 text-white">
													<img  src="<?= base_url() ?>image/<?= $s['icon_sensor']?>.svg" style="stroke:blue;height:16px" class=" text-white me-2 mb-0"/>
													<span class="fw-bold mb-0"><?= $s['nilai'] ?> <?= $s['satuan'] ?></span>
												</div>
											</div>
											<?php } ?>

											<?php } ?>

										</div>
									</div>
									<?php } ?>

									<?php } ?>


								</div>
							</div>
						</div>
						<?php } ?>
						<?php } ?>
					</div>


				</div>
				<div class="col-xl-3 col-xxl-2  d-none d-lg-block" id="right_map">
					<div class="text-white " style="flex-shrink: 0;">
						<div class="px-3 pt-3 pb-3 " style="background-color:#30348180;border-bottom:2px solid #FFD61580;">
							<h3 class="mb-0 fw-bold" id="nama_lokasi"><?= $data_konten[0]['logger'][0]['nama_lokasi'] ?></h3>
							<h4 class="fw-normal mb-0">Rerata <span id="nama_pr"><?= str_replace('_',' ',$data_konten[0]['logger'][0]['param'][0]['nama_parameter']) ?></span> pada <?= date('Y-m-d') ?></h4>
						</div>
						<div class="px-3 pb-2 text-white">
							<select class="form-select mt-2 d-none" id="select_station" style="padding:5px 15px;background-color:#30348180;color:white;font-weight:700;border:2px solid #FFD61580">
								<option selected disabled>Pilih Sensor</option>
							</select>
							<select class="form-select mt-2" id="pilih_param" style="padding:5px 15px;background-color:#30348180;color:white;font-weight:700;border:2px solid #FFD61580">
								<option selected disabled>Pilih Parameter</option>
								<?php foreach($param_first as $pr=>$pf) { ?>
								<option value="<?= $pf['id_param'] ?>" <?= ($pf['id_param'] == $data_konten[0]['logger'][0]['param'][0]['id_param']) ? 'selected' : '' ?>><?= str_replace('_',' ',$pf['nama_parameter']) ?></option>
								<?php } ?>
							</select>
						</div>


						<div id="chart_analisa" class=" w-100 pe-0 mt-3"></div>
					</div>
					<div id="loader" class="w-100 pt-5 mt-5 d-none" ><i  class="w-100 fas fa-sync fa-spin text-white fa-4x "></i></div>
					<div class="" id="analisa-right"  style="background-color:#30348180;flex-grow: 1;overflow-y: auto;scrollbar-width: none;">
						<div class="mt-2 mb-0 text-white">
							<table class="table table-bordered border-white mb-0" style="border-collapse: collapse;">
								<thead >
									<tr >
										<th class="bg-transparent text-white">Waktu</th>
										<th  class="bg-transparent text-white">Nilai</th>
									</tr>
								</thead>
								<tbody id="body_data" class="fw-bold">
									<?php foreach(array_reverse($gabung) as $k=>$da) { ?>
									<tr >
										<td><?= $da['waktu'] ?></td>
										<td><?= $da['data'] ?></td>
									</tr>
									<?php } ?>

								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div style="" id="tes">
			<div class="peta-map-brand">
				<img class="brand-full" src="<?= base_url()?>image/peta_lokasi.svg" height="40px"/>
				<img class="brand-mark" src="<?= base_url()?>image/logopu 4.png" alt="DI Leuwigoong"/>
			</div>
			<div class="peta-map-actions">
				<div class="weather-chip-location">
					<img src="<?= $cuaca->image ?>" height="35px" />
					<div class="weather-temp-location">
						<h1 class="mb-0 ms-2"><?= $cuaca->t ?></h1><span class="pt-1">°C</span>
					</div>
					<div class="weather-desc-location">
						<h5 class="mb-0"><?= $cuaca->weather_desc ?></h5>
						<h5 class="mb-0">Angin : <?= $cuaca->ws ?> km/h</h5>
					</div>

				</div>
				<div class="map-nav-item d-flex flex-column align-items-center">
					<a class="nav-action-location" href="<?= base_url() ?>beranda">
						<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 16 16" class="me-2"><path fill="currentColor" d="M6.906.664a1.749 1.749 0 0 1 2.187 0l5.25 4.2c.415.332.657.835.657 1.367v7.019A1.75 1.75 0 0 1 13.25 15h-3.5a.75.75 0 0 1-.75-.75V9H7v5.25a.75.75 0 0 1-.75.75h-3.5A1.75 1.75 0 0 1 1 13.25V6.23c0-.531.242-1.034.657-1.366l5.25-4.2Zm1.25 1.171a.25.25 0 0 0-.312 0l-5.25 4.2a.25.25 0 0 0-.094.196v7.019c0 .138.112.25.25.25H5.5V8.25a.75.75 0 0 1 .75-.75h3.5a.75.75 0 0 1 .75.75v5.25h2.75a.25.25 0 0 0 .25-.25V6.23a.25.25 0 0 0-.094-.195Z"/></svg>
						<span class="d-none d-lg-inline-block">Dashboard</span>
					</a>
				</div>
				<div class="map-nav-item d-flex flex-column align-items-center">
					<button class="nav-action-location active">
						<span class="nav-link-icon d-lg-inline-block text-white">
							<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-map"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 7l6 -3l6 3l6 -3v13l-6 3l-6 -3l-6 3v-13" /><path d="M9 4v13" /><path d="M15 7v13" /></svg>
						</span>
					<span class="d-none d-lg-inline-block">	Peta Lokasi</span>
					</button>
				</div>
				<div class="map-nav-item d-flex flex-column align-items-center">
					<a class="nav-action-location" href="<?= base_url('peta3d') ?>">
						<span class="nav-link-icon d-lg-inline-block text-white">
							<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" class="me-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 2l9 5l-9 5l-9 -5l9 -5z"/><path d="m3 12l9 5l9 -5"/><path d="m3 17l9 5l9 -5"/></svg>
						</span>
						<span class="d-none d-lg-inline-block">Peta 3D</span>
					</a>
				</div>
				<div class="dropdown px-0 download-nav-location">
					<button type="button" class="nav-action-location dropdown-toggle" data-bs-toggle="dropdown">
						<svg xmlns="http://www.w3.org/2000/svg" width="22" class="me-2" height="22" viewBox="0 0 26 26"><g fill="none"><path d="M24 0v24H0V0h24ZM12.593 23.258l-.011.002l-.071.035l-.02.004l-.014-.004l-.071-.035c-.01-.004-.019-.001-.024.005l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427c-.002-.01-.009-.017-.017-.018Zm.265-.113l-.013.002l-.185.093l-.01.01l-.003.011l.018.43l.005.012l.008.007l.201.093c.012.004.023 0 .029-.008l.004-.014l-.034-.614c-.003-.012-.01-.02-.02-.022Zm-.715.002a.023.023 0 0 0-.027.006l-.006.014l-.034.614c0 .012.007.02.017.024l.015-.002l.201-.093l.01-.008l.004-.011l.017-.43l-.003-.012l-.01-.01l-.184-.092Z"/><path fill="currentColor" d="M20 14.5a1.5 1.5 0 0 1 1.5 1.5v4a2.5 2.5 0 0 1-2.5 2.5H5A2.5 2.5 0 0 1 2.5 20v-4a1.5 1.5 0 0 1 3 0v3.5h13V16a1.5 1.5 0 0 1 1.5-1.5Zm-8-13A1.5 1.5 0 0 1 13.5 3v9.036l1.682-1.682a1.5 1.5 0 0 1 2.121 2.12l-4.066 4.067a1.75 1.75 0 0 1-2.474 0l-4.066-4.066a1.5 1.5 0 0 1 2.121-2.121l1.682 1.682V3A1.5 1.5 0 0 1 12 1.5Z"/></g></svg>
						<h3 class="mb-0 fw-bold d-none d-lg-inline-block">Unduh</h3>
					</button>
					<div class="dropdown-menu fw-bold border-white">
						
						<a class="dropdown-item" href="<?= base_url() ?>" target="_blank">
							Android App
						</a>
					</div>
				</div>
				<a class="nav-action-location" href="<?= base_url() ?>login/logout">
					<svg xmlns="http://www.w3.org/2000/svg" class="me-2" width="22" height="22" viewBox="0 0 24 24"><g fill="none"><path d="M24 0v24H0V0h24ZM12.593 23.258l-.011.002l-.071.035l-.02.004l-.014-.004l-.071-.035c-.01-.004-.019-.001-.024.005l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427c-.002-.01-.009-.017-.017-.018Zm.265-.113l-.013.002l-.185.093l-.01.01l-.003.011l.018.43l.005.012l.008.007l.201.093c.012.004.023 0 .029-.008l.004-.014l-.034-.614c-.003-.012-.01-.02-.02-.022Zm-.715.002a.023.023 0 0 0-.027.006l-.006.014l-.034.614c0 .012.007.02.017.024l.015-.002l.201-.093l.01-.008l.004-.011l.017-.43l-.003-.012l-.01-.01l-.184-.092Z"/><path fill="currentColor" d="M12 2.5a1.5 1.5 0 0 1 0 3H7a.5.5 0 0 0-.5.5v12a.5.5 0 0 0 .5.5h4.5a1.5 1.5 0 0 1 0 3H7A3.5 3.5 0 0 1 3.5 18V6A3.5 3.5 0 0 1 7 2.5Zm6.06 5.61l2.829 2.83a1.5 1.5 0 0 1 0 2.12l-2.828 2.83a1.5 1.5 0 1 1-2.122-2.122l.268-.268H12a1.5 1.5 0 0 1 0-3h4.207l-.268-.268a1.5 1.5 0 1 1 2.122-2.121Z"/></g></svg>
					<span class="d-none d-lg-inline-block">	Keluar</span>
				</a>
			</div>
		</div>
		<div class="mobile-map-actions">
			<button class="mobile-map-action" type="button" data-panel="left_map">Logger</button>
			<button class="mobile-map-action" type="button" data-panel="right_map">Detail</button>
		</div>
		<script src="https://sepakusemoi.monitoring4system.com/assets/js/apexcharts.min.js?1692870487" defer></script>
		<script src="https://code.jquery.com/jquery-3.6.0.js"></script>

		<script>
			function open_cam (name) {
				var splitArray = name.split("_");
				$('#ipcam_title').text(splitArray[0]);
				$('#ipcam_body').attr('src', splitArray[1]);
				$('#ipcam_view').modal('show');
			}
				
			$(document).ready(function() {
				var center_map = "<?= $this->session->userdata('center_map')?>";
				let [lat, lon] = center_map.split(",").map(x => parseFloat(x.trim()))
				var logger_selected = '<?= $data_konten[0]['logger'][0]['id_logger'] ?>';
				var sensor_avw = <?= json_encode($sensor_avw) ?>;
				var avw_selected = '';
				var sensor_selected = '';
				let avw_markers = [];
				let mapInstance = null;
				let detailRequest = null;
				let detailRequestSeq = 0;
				const detailCache = {};
				const location_new = <?php echo json_encode($marker)?>;
				function isMobileMapView() {
					return window.matchMedia('(max-width: 991.98px)').matches;
				}

				function mobileViewport() {
					const viewport = window.visualViewport;
					return {
						width: viewport && viewport.width ? viewport.width : window.innerWidth,
						height: viewport && viewport.height ? viewport.height : window.innerHeight
					};
				}

				function clampValue(value, min, max) {
					return Math.min(Math.max(value, min), max);
				}

				function syncMobileLayout() {
					if (!isMobileMapView()) {
						return;
					}
					const viewport = mobileViewport();
					const compactHeight = viewport.height < 680;
					const detailMin = compactHeight ? 220 : 250;
					const detailMax = compactHeight ? 330 : 390;
					const loggerMin = compactHeight ? 260 : 310;
					const loggerMax = compactHeight ? 360 : 430;
					const detailHeight = Math.round(clampValue(viewport.height * 0.46, detailMin, detailMax));
					const loggerHeight = Math.round(clampValue(viewport.height * 0.56, loggerMin, loggerMax));
					const toolbar = document.getElementById('tes');
					const toolbarBottom = toolbar ? Math.ceil(toolbar.getBoundingClientRect().bottom + 8) : 84;

					document.documentElement.style.setProperty('--mobile-detail-height', detailHeight + 'px');
					document.documentElement.style.setProperty('--mobile-logger-height', loggerHeight + 'px');
					document.documentElement.style.setProperty('--mobile-top-clear', toolbarBottom + 'px');
				}

				function removeMapControl(position, element) {
					if (!mapInstance || !element) {
						return;
					}
					const controls = mapInstance.controls[position];
					for (let index = controls.getLength() - 1; index >= 0; index--) {
						if (controls.getAt(index) === element) {
							controls.removeAt(index);
						}
					}
				}

				function addMapControl(position, element) {
					if (!mapInstance || !element) {
						return;
					}
					const controls = mapInstance.controls[position];
					for (let index = 0; index < controls.getLength(); index++) {
						if (controls.getAt(index) === element) {
							return;
						}
					}
					controls.push(element);
				}

				function mountResponsivePanels() {
					const leftElement = document.getElementById('left_map');
					const rightElement = document.getElementById('right_map');
					if (!leftElement || !rightElement) {
						return;
					}
					syncMobileLayout();
					const canUseMapControls = mapInstance && typeof google !== 'undefined' && google.maps;

					if (isMobileMapView()) {
						if (canUseMapControls) {
							removeMapControl(google.maps.ControlPosition.LEFT_TOP, leftElement);
							removeMapControl(google.maps.ControlPosition.RIGHT_TOP, rightElement);
						}
						document.body.appendChild(leftElement);
						document.body.appendChild(rightElement);
						return;
					}

					if (!canUseMapControls) {
						return;
					}
					addMapControl(google.maps.ControlPosition.LEFT_TOP, leftElement);
					addMapControl(google.maps.ControlPosition.RIGHT_TOP, rightElement);
				}

				function toggleMobilePanel(panelId, forceOpen) {
					if (!isMobileMapView()) {
						return;
					}
					syncMobileLayout();
					const $panel = $('#' + panelId);
					const willOpen = typeof forceOpen === 'boolean' ? forceOpen : !$panel.hasClass('mobile-open');
					if (willOpen && $panel.hasClass('mobile-open')) {
						$('.mobile-map-action').removeClass('active');
						$('.mobile-map-action[data-panel="' + panelId + '"]').addClass('active');
						syncMobileLayout();
						return;
					}
					$('#left_map, #right_map').removeClass('mobile-open');
					$('.mobile-map-action').removeClass('active');
					if (willOpen) {
						$panel.addClass('mobile-open');
						$('.mobile-map-action[data-panel="' + panelId + '"]').addClass('active');
					}
					syncMobileLayout();
				}

				function setDetailLoading(isLoading) {
					$('#analisa-right').removeClass('d-none');
					if (isLoading) {
						$('#right_map').addClass('detail-loading');
						$('#loader').removeClass('d-none');
						return;
					}
					$('#right_map').removeClass('detail-loading');
					$('#loader').addClass('d-none');
				}

				function fetchDetailData(cacheKey, url, onSuccess) {
					detailRequestSeq += 1;
					const requestSeq = detailRequestSeq;
					if (detailRequest && detailRequest.readyState !== 4) {
						detailRequest.abort();
					}

					if (cacheKey && detailCache[cacheKey]) {
						onSuccess(detailCache[cacheKey]);
						setDetailLoading(false);
						return;
					}

					setDetailLoading(true);
					detailRequest = $.ajax({
						url: url,
						method: 'get',
						success:function(data){
							if (requestSeq !== detailRequestSeq) {
								return;
							}
							const obj = JSON.parse(data);
							if (cacheKey) {
								detailCache[cacheKey] = obj;
							}
							onSuccess(obj);
							setDetailLoading(false);
						},
						error: function(jqXHR, textStatus) {
							if (textStatus === 'abort' || requestSeq !== detailRequestSeq) {
								return;
							}
							setDetailLoading(false);
							alert('AJAX request failed: ' + textStatus);
						}
					});
				}

				function escapeInfoValue(value) {
					return String(value ?? '').replace(/[&<>"']/g, function(char) {
						return {
							'&': '&amp;',
							'<': '&lt;',
							'>': '&gt;',
							'"': '&quot;',
							"'": '&#039;'
						}[char];
					});
				}

				function buildInfoWindowContent(location) {
					const latitude = escapeInfoValue(location['latitude']);
					const longitude = escapeInfoValue(location['longitude']);
					const namaLokasi = escapeInfoValue(location['nama_lokasi']);
					const koneksi = escapeInfoValue(location['koneksi']);
					const statusSd = escapeInfoValue(location['status_sd']);
					const analisaLink = escapeInfoValue(location['link']);
					const mapsLink = 'https://maps.google.com/?q=' + encodeURIComponent(location['latitude'] + ',' + location['longitude']);

					return ''
						+ '<div class="map-info-card">'
						+ '<div class="map-info-title">' + namaLokasi + '</div>'
						+ '<table class="map-info-table"><tbody>'
						+ '<tr><td>Latitude</td><td>' + latitude + '</td></tr>'
						+ '<tr><td>Longitude</td><td>' + longitude + '</td></tr>'
						+ '<tr><td>Status Koneksi</td><td>' + koneksi + '</td></tr>'
						+ '<tr><td>Status SD Card</td><td>' + statusSd + '</td></tr>'
						+ '</tbody></table>'
						+ '<div class="map-info-actions">'
						+ '<a href="' + mapsLink + '" target="_blank" rel="noopener">'
						+ '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 14l11 -11"/><path d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5"/></svg>'
						+ 'Menuju Lokasi</a>'
						+ '<a href="' + analisaLink + '">'
						+ '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M3 3v18h18"/><path d="M7 9a2 2 0 1 0 4 0a2 2 0 1 0-4 0m10-2a2 2 0 1 0 4 0a2 2 0 1 0-4 0m-5 8a2 2 0 1 0 4 0a2 2 0 1 0-4 0m-1.84-4.38l2.34 2.88m2.588-.172l2.837-4.586"/></g></svg>'
						+ 'Analisa Data</a>'
						+ '</div></div>';
				}

				function keepMobileInfoWindowVisible(map) {
					if (!isMobileMapView()) {
						return;
					}
					syncMobileLayout();

					setTimeout(function() {
						const infoWindow = document.querySelector('.gm-style-iw-c');
						if (!infoWindow) {
							return;
						}

						const viewport = mobileViewport();
						const infoRect = infoWindow.getBoundingClientRect();
						const toolbar = document.getElementById('tes');
						const activePanel = document.querySelector('#left_map.mobile-open, #right_map.mobile-open');
						const toolbarBottom = toolbar ? toolbar.getBoundingClientRect().bottom : 0;
						const panelTop = activePanel ? activePanel.getBoundingClientRect().top : viewport.height - 12;
						const safeTop = Math.max(toolbarBottom + 10, 78);
						const safeBottom = Math.max(safeTop + 150, panelTop - 12);
						const safeLeft = 10;
						const safeRight = viewport.width - 10;
						let panX = 0;
						let panY = 0;

						if (infoRect.left < safeLeft) {
							panX = infoRect.left - safeLeft;
						} else if (infoRect.right > safeRight) {
							panX = infoRect.right - safeRight;
						}

						if (infoRect.top < safeTop) {
							panY = infoRect.top - safeTop;
						} else if (infoRect.bottom > safeBottom) {
							panY = infoRect.bottom - safeBottom;
						}

						if (Math.abs(panX) > 1 || Math.abs(panY) > 1) {
							map.panBy(Math.round(panX), Math.round(panY));
						}
					}, 120);
				}

				function initMap() {
					const mobileMap = isMobileMapView();
					const map = new google.maps.Map(document.getElementById("map"), {
						center: {
							lat: lat,
							lng:  lon, 
						},
						zoom: mobileMap ? 13 : 14,
						tilt: mobileMap ? 0 : 100,
						mapId: "90f87356969d889c",
						disableDefaultUI: true,
						mapTypeId: 'satellite',
						gestureHandling: 'greedy',
						clickableIcons: false
					});
					mapInstance = map;
					const new_element = document.getElementById('tes');
					map.controls[google.maps.ControlPosition.TOP_CENTER].push(new_element);
					mountResponsivePanels();
					var currentInfoWindow = null;
					location_new.forEach(function(location) {
						var marker = new google.maps.Marker({
							position: { lat: parseFloat(location['latitude']), lng: parseFloat(location['longitude']) },
							map: map,
							
							icon: {
								url: location['icon'], 
								scaledSize: new google.maps.Size(25, 35),
								labelOrigin: new google.maps.Point(10, -10)  
							},
						});
						var infoWindow = new google.maps.InfoWindow({
							content: buildInfoWindowContent(location),
							maxWidth: 380
						});
						
						marker.addListener('click', function() {
							if (currentInfoWindow) {
								currentInfoWindow.close();
							}
							map.panTo(marker.getPosition());

							setTimeout(function () {
								map.setTilt(isMobileMapView() ? 0 : 60); 
								map.setHeading(map.getHeading()); 
							}, 200); 

							infoWindow.open(map, marker);
							toggleMobilePanel('right_map', true);
							keepMobileInfoWindowVisible(map);
							$('#select_station').addClass('d-none');

							logger_selected = location['id_logger'];
							scrollToElement(location['id_logger']);
							$('#nama_lokasi').text(location['nama_lokasi']);
							const detailUrl = '<?php echo base_url(); ?>analisa/temp_ajax?id_param='+ location['id_param']+'&id_logger='+location['id_logger'];
							const detailKey = 'marker:' + location['id_param'] + ':' + location['id_logger'];
							fetchDetailData(detailKey, detailUrl, function(obj) {
									update_chart(obj);
									$('#pilih_param').empty();
									$('#select_station').empty();
									$.each(obj['list_param'], function(index, option) {
										let isSelected = option['id_param'] === obj['selected']; 
										$('#pilih_param').append($('<option>', {
											value:  option['id_param'],
											text: option['nama_parameter'].replaceAll('_',' '),
											selected: isSelected
										}));
									});
							});
							currentInfoWindow = infoWindow;
						});
					});
				}
				$('#select_station').change(function() {
					var selectedValue = $(this).val();
					avw_selected = selectedValue;
					$('#pilih_param').empty();
					fetchDetailData('avw:' + selectedValue, '<?php echo base_url(); ?>analisa/temp_avw?id_avw='+ selectedValue, function(obj) {
							update_chart(obj);
							$.each(obj['list_param'], function(index, option) {
								let isSelected = index === 0; 
								$('#pilih_param').append($('<option>', {
									value: option['id'],
									text: option['nama_parameter'].replaceAll('_',' '),
									selected: isSelected
								}));
							});
					});
				});

				$('#pilih_param').change(function() {
					var selectedValue = $(this).val();
					sensor_selected = selectedValue;
					var url_avw = '<?php echo base_url(); ?>analisa/temp_ajax2?id_param='+ selectedValue;
					fetchDetailData('param:' + selectedValue, url_avw, function(obj) {
							update_chart(obj);
					});
				});

				$('.mobile-map-action').on('click', function() {
					toggleMobilePanel($(this).data('panel'));
				});

				$(window).on('resize orientationchange', function() {
					if (!isMobileMapView()) {
						$('#left_map, #right_map').removeClass('mobile-open');
						$('.mobile-map-action').removeClass('active');
					}
					mountResponsivePanels();
					if (mapInstance) {
						setTimeout(function() {
							keepMobileInfoWindowVisible(mapInstance);
						}, 180);
					}
				});

				if (window.visualViewport) {
					window.visualViewport.addEventListener('resize', function() {
						syncMobileLayout();
						if (mapInstance) {
							setTimeout(function() {
								keepMobileInfoWindowVisible(mapInstance);
							}, 180);
						}
					});
				}

				var options = {
					chart: {
						type: "area",
						fontFamily: 'inherit',
						height: 120.0,
						sparkline: {
							enabled: true
						},
						animations: {
							enabled: false
						},
					},
					dataLabels: {
						enabled: false,
					},
					fill: {
						opacity: .50,
						type: 'solid'
					},
					stroke: {
						width: 3,
						lineCap: "round",
						curve: "smooth",
					},
					series: [
						{
							name: "<?= str_replace('_',' ',$data_konten[0]['logger'][0]['param'][0]['nama_parameter']) ?>",
							data: <?php echo json_encode($data_analisa) ?>
						}
							],
							tooltip: {
							theme: 'dark',
							x: {
							format: 'yyyy-MM-dd HH:mm:ss' // Full date and time format
						}
						},
						grid: {
						strokeDashArray: 4,
						},
					xaxis: {
						labels: {
							padding: 0,
						},
							tooltip: {
								enabled: false
							},
								axisBorder: {
									show: false,
								},
									type: 'datetime',
					},
						yaxis: {
							labels: {
								padding: 4
							},
						},
							labels: <?php echo json_encode($waktu_analisa) ?>,
								colors: ["#6495ED"],
									legend: {
										show: false,
									},
			};
							  var chart = new ApexCharts(document.querySelector("#chart_analisa"), options);
			chart.render();		
			function update_chart(data) {
				$('#nama_lokasi').text(data['nama_lokasi']);
				$('#nama_pr').text(data['nama_parameter'].replaceAll('_',' '));
				chart.updateOptions({
					series: [{
						name: data['nama_parameter'].replaceAll('_',' '),
						data: data['data_analisa']
					}],
					labels: data['tanggal_analisa']
				});
				$('#body_data').empty();
				var new_data = [];
				if(data['data_analisa'].length == 0){
					$('#body_data').append(
						'<tr><td colspan="2" class="text-center h3" >Tidak Ada Data</td></tr>'
					);
				}
				$.each(data['data_gabung'], function(index, value) {
					$('#body_data').append(
						'<tr><td>' + value['waktu'] + '</td><td>' + value['data'] + '</td></tr>'
					);
				});
			}


			if (typeof google !== 'undefined' && google.maps) {
				initMap();
			} else {
				window.onload = initMap;
			}
			function scrollToElement(elementId) {
				const $element = $('#sc_' + elementId);
				if ($element.length) {
					$('#left_map').animate({
						scrollTop: $element.offset().top - $('#left_map').offset().top + $('#left_map').scrollTop()- 20
					}, 600);  // 600ms smooth scrolling
				}
				$element.addClass('border-pulse');

				setTimeout(function() {
					$element.removeClass('border-pulse');
				}, 2000); 
			}
			});
			// @formatter:on
		</script>
	</body>

	<div class="modal fade" id="ipcam_view" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-xl">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="ipcam_title">Modal title</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body p-1">
					<iframe id="ipcam_body" src="" width="100%" height="500" allow="fullscreen"></iframe>

				</div>
				<div class="modal-footer py-1">
					<button type="button" class="btn btn-secondary btm-sm px-2" data-bs-dismiss="modal">Tutup</button>
				</div>
			</div>
		</div>
	</div>
</html>
