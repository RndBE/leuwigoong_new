<style>
	.hide-scrollbar::-webkit-scrollbar {
		display: none;
	}
</style>
<div class="container-xl">
	<!-- Page title -->
	<div class="page-header d-print-none">
		<div class="row g-2 align-items-center">
			<div class="col">
				<h2 class="page-title">
					<?php echo ucfirst($this->uri->segment(1)) ?>
				</h2>
			</div>
		</div>
	</div>
</div>
<div class="page-body">
	<!-- Konten-->
	<div class="container-xl">

		<div class="row row-cards">
			<?php foreach ($data_konten as $key => $kt) { ?>
			<div class="col-12">
				<div class="row row-cards">
					<?php foreach ($kt->logger as $log) { ?>
					<div class="col-md-12 col-lg-12">
						<div class="card">
							<div class="card-status-top bg-<?= $log->color ?>"></div>
							<div class="ribbon bg-<?= $log->color ?>"><span id="waktu_logger"> <?= $log->waktu ?></span>

								<div class="card-actions">
									<div class="dropdown">
										<a href="#" class="btn-icon" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-list" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="white" fill="none" stroke-linecap="round" stroke-linejoin="round">
												<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
												<line x1="9" y1="6" x2="20" y2="6"></line>
												<line x1="9" y1="12" x2="20" y2="12"></line>
												<line x1="9" y1="18" x2="20" y2="18"></line>
												<line x1="5" y1="6" x2="5" y2="6.01"></line>
												<line x1="5" y1="12" x2="5" y2="12.01"></line>
												<line x1="5" y1="18" x2="5" y2="18.01"></line>
											</svg>

										</a>
										<div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
											<div class="dropdown-item">
												<strong><a href="#" class="text-reset">Id Logger</a></strong>
												<label class="form-check m-0 ms-auto">
													<?= $log->id_logger ?>
												</label>
											</div>

											<div class="dropdown-item">
												<strong><a href="#" class="text-reset">Status Logger</a></strong>
												<label class="form-check m-0 ms-auto">
													<?= $log->status_logger ?>
												</label>
											</div>

											<div class="dropdown-item">
												<strong><a href="#" class="text-reset">Status SDCard</a></strong>
												<label class="form-check m-0 ms-auto">
													<?= $log->sdcard ?>
												</label>
											</div>

										</div>
									</div>
								</div>
							</div>
							<div class="card-header pb-3">
								<h3 class="card-title fw-bold mb-0"><?= $log->nama_lokasi ?></h3>
							</div>
							<div class="card-body ">
								<div class="row">
									<div class="col-md-4 col-lg-2 mb-3 mb-lg-0 ">
										<div class="card h-100 ">
											<div class="card-body d-flex align-items-center justify-content-center ">
												<a class="btn btn-primary" href="<?= base_url() ?>awgc/kontrol_pintu?idlogger=<?= $log->id_logger ?>">
													<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-settings" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
														<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
														<path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z"></path>
														<path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"></path>
													</svg> Kontrol Pintu</a>
											</div>
										</div>
									</div>
									<?php foreach ($log->param as $pr_log) { ?>
									<div class="col-md-4 col-lg-2 mb-3 mb-lg-0">
										<div class="card w-100 h-100">
											<div class="card-body d-flex flex-column justify-content-center align-items-center">
												<a class="text-center" href="<?= $pr_log['link'] ?>">
													<h3 class="mb-0 fw-normal"><?= str_replace('_', ' ', $pr_log['nama_parameter']) ?></h3>
												</a>
												<h2 class="mb-1" id="<?= $pr_log['nama_parameter']?>"><?= number_format($pr_log['nilai'], 2) . ' ' . $pr_log['satuan'] ?></h2>

											</div>
										</div>
									</div>
									<?php } ?>

								</div>
								<div class="row mt-0 mt-lg-3 gx-4">
									<?php foreach ($log->data_pintu as $key => $x) { ?>
									<div class="col-md-6 col-lg-4 col-xl-3	 mb-3">
										<div class="card">
											<div class="card-header py-2 d-flex justify-content-center">
												<h3 class="card-title mb-0 fw-bold"><?= $x['nama_pintu']  ?></h3>
											</div>
											<div class="card-body px-0 px-md-2 px-lg-3	">
												<div class="row">
													<div class="col-lg-12">
														<div class="card h-100">
															<div class="card-header d-flex justify-content-center py-2">
																<b>Elevasi Pintu</b>
															</div>
															<style>
																.left {
																	background-color: #0112AA;
																	position: absolute;
																	width: 30px;
																	left: 0px;
																	height: 150px;
																	bottom: 0px;
																	border-left: 2px solid black;
																	border-right: 2px solid black;
																}

																.middle {
																	position: absolute;

																	width: calc(100% - 60px);
																	left: 30px;
																	background-color: #B8A476;
																	height: 60%;
																	border-top: 2px solid black;
																	border-bottom: 2px solid black;
																}

																.right {
																	background-color: #0112AA;
																	position: absolute;
																	right: 0px;
																	bottom: 0px;
																	width: 30px;
																	height: 150px;
																	border-left: 2px solid black;
																	border-right: 2px solid black;
																}

																.top {
																	position: relative;
																	height: 100px;
																	width: calc(100%);
																	left: 0px;
																}

																.top .up {
																	position: absolute;
																	height: 20px;
																	width: 100%;
																	bottom: 0px;
																	background-color: #0112AA;
																}

																.top .act-left {
																	bottom: 20px;
																	position: absolute;
																	width: 25px;
																	left: 5px;
																}

																.top .act-right {
																	bottom: 20px;
																	position: absolute;
																	width: 25px;
																	transform: scaleX(-1);
																	right: 5px;
																}

																.top .up2 .mid {
																	bottom: 25px;
																	position: absolute;
																	width: calc(100% - 60px);
																	height: 8px;
																	background-color: #BAB4C5;
																	transform: scaleX(-1);
																	left: 50%;
																	transform: translateX(-50%);
																}

																.top .up2 .mid {
																	bottom: 25px;
																	position: absolute;
																	width: calc(100% - 60px);
																	height: 8px;
																	background-color: #BAB4C5;
																	transform: scaleX(-1);
																	left: 50%;
																	transform: translateX(-50%);
																}

																.top .up2 .mid h3 {
																	bottom: 30px;
																	position: absolute;
																	left: 50%;
																	white-space: nowrap;
																	transform: translateX(-50%);
																}
															</style>
															<div class="card-body ">
																<div class="px-xxl-4 px-xl-3" style="position: relative;">
																	<div class="top">

																		<div class="up"></div>
																		<div class="up2">
																			<img class="act-left" src="<?= base_url() ?>image/actuator.svg" alt="">
																			<div class="mid"></div>
																			<img class="act-right" src="<?= base_url() ?>image/actuator.svg" alt="">
																		</div>
																	</div>
																	<div class="cont" style="position: relative;height: 150px;">
																		<div class="left">

																		</div>
																		<div id="elev-<?= $x['id_pintu'] ?>" class="middle d-flex align-items-center justify-content-center" style="top: calc(100% - <?= $x['elevasi'] ?>%);transform: translateY(calc(0% - calc(100% - <?= $x['elevasi'] ?>%)));">
																			<h1 id="elevt-<?= $x['id_pintu'] ?>" class="mb-0"><?= $x['elevasi'] ?> %</h1>
																		</div>
																		<div class=" right">
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</div>
													<div class="col-lg-12 mt-3">
														<div class="d-flex justify-content-center">
															<div class="me-4">
																<div class="bg-dark px-1 pt-1">
																	<div class="bg-light py-0 text-center fw-bold">R</div>
																</div>
																<div class="bg-dark p-2" style="border-bottom-left-radius: 50%;border-bottom-right-radius: 50%;">
																	<div id="r-<?= $x['id_pintu'] ?>" class="rounded-circle bg-<?= ($x['r'] == '1') ? 'danger' : 'secondary' ?> d-flex align-items-center justify-content-center fw-bold text-white" style="height: 50px; width:50px"><?= ($x['r'] == '1') ? 'ON' : 'OFF' ?></div>
																</div>
															</div>
															<div class="me-4">
																<div class="bg-dark px-1 pt-1">
																	<div class="bg-light py-0 text-center fw-bold">S</div>
																</div>
																<div class="bg-dark p-2" style="border-bottom-left-radius: 50%;border-bottom-right-radius: 50%;">
																	<div id="s-<?= $x['id_pintu'] ?>" class="rounded-circle bg-<?= ($x['s'] == '1') ? 'yellow' : 'secondary' ?> d-flex align-items-center justify-content-center fw-bold text-white" style="height: 50px; width:50px"><?= ($x['s'] == '1') ? 'ON' : 'OFF' ?></div>
																</div>
															</div>
															<div class="">
																<div class="bg-dark px-1 pt-1">
																	<div class="bg-light py-0 text-center fw-bold">T</div>
																</div>
																<div class="bg-dark p-2" style="border-bottom-left-radius: 50%;border-bottom-right-radius: 50%;">
																	<div id="t-<?= $x['id_pintu'] ?>" class="rounded-circle bg-<?= ($x['t'] == '1') ? 'success' : 'secondary' ?> d-flex align-items-center justify-content-center fw-bold text-white" style="height: 50px; width:50px"><?= ($x['t'] == '1') ? 'ON' : 'OFF' ?></div>
																</div>
															</div>

														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<?php } ?>
								</div>
							</div>
						</div>
					</div>
					<?php } ?>
				</div>
			</div>

			<?php } ?>

		</div>
	</div>
	<!-- end Konten-->
</div>
<script type="text/javascript">
	'use strict';
	(function() {
		window.AutoScroll = function(el, options) {
			// In case they forgot 'new'
			if (!(this instanceof AutoScroll)) {
				return new AutoScroll(el, options);
			}

			this.el = el;
			this.speed = null;
			this.isBeingThrown = false;
			this.isMouseOver = false;
			this.isRunning = false;
			this.thrownInterval = null;
			this.timeout = null;
			this.previousScrollTop = null;

			var defaults = {
				speed: 0,
				pauseBottom: 500,
				pauseStart: 500,
				requestAnimationFrame: true,
				timeoutRate: 10
			};

			if (options && typeof options === 'object') {
				this.options = extendDefaults(defaults, options);
			} else {
				this.options = defaults;
			}

			_init.call(this);
		}

		AutoScroll.prototype.autoScroll = function() {
			if (this.isRunning && !this.isBeingThrown && !this.isMouseOver) {
				if (this.el.scrollTop < this.el.scrollHeight - this.el.offsetHeight) {
					if (this.options.requestAnimationFrame) {
						this.el.scrollTop += this.speed;
						window.requestAnimationFrame(this.autoScroll.bind(this));
					} else {
						this.el.scrollTop += this.speed;
						if (this.timeout) clearTimeout(this.timeout);
						this.timeout = setTimeout(this.autoScroll.bind(this), this.options.timeoutRate)
					}
				} else {
					this.isRunning = false;
					setTimeout(this.resetScroll.bind(this), this.options.pauseBottom);
				}
			} else {
				return;
			}
		}

		AutoScroll.prototype.startScroll = function() {
			this.isRunning = true;
			this.autoScroll.call(this);
		}

		AutoScroll.prototype.pauseScroll = function() {
			this.isRunning = false;
		}

		AutoScroll.prototype.resetScroll = function() {
			this.el.scrollTop = 0;
			setTimeout(this.startScroll.bind(this), this.options.pauseStart);
		}

		AutoScroll.prototype.mouseEnter = function(e) {
			this.isMouseOver = true;
			this.isRunning = false;
		}

		AutoScroll.prototype.mouseLeave = function(e) {
			this.isMouseOver = false;
			this.isRunning = true;
			this.startScroll.call(this);
		}

		AutoScroll.prototype.mobileTouchStart = function(e) {
			this.isBeingThrown = true;
		}

		AutoScroll.prototype.mobileTouchEnd = function(e) {
			this.thrownInterval = setInterval(this.wasThrown.bind(this), 10);
		}

		AutoScroll.prototype.wasThrown = function() {
			if (this.previousScrollTop !== this.el.scrollTop) this.previousScrollTop = this.el.scrollTop;
			else this.thrownEnd.call(this);
		}

		AutoScroll.prototype.thrownEnd = function() {
			clearInterval(this.thrownInterval);
			this.isBeingThrown = false;
			this.startScroll.call(this);
		}

		// Private Methods
		function _init() {
			this.speed = _setSpeed(this.options.speed);
			_initEvents.call(this);
			setTimeout(this.startScroll.bind(this), this.options.pauseStart);
		}

		function _initEvents() {
			this.el.addEventListener('mouseenter', this.mouseEnter.bind(this));
			this.el.addEventListener('mouseleave', this.mouseLeave.bind(this));
			this.el.addEventListener('touchstart', this.mobileTouchStart.bind(this));
			this.el.addEventListener('touchend', this.mobileTouchEnd.bind(this));
		}

		function _setSpeed(scrollDistance) {
			return Math.ceil(scrollDistance / 60);
		}

		// Utility Methods
		function extendDefaults(source, properties) {
			var property;
			for (property in properties) {
				if (properties.hasOwnProperty(property)) {
					source[property] = properties[property];
				}
			}
			return source;
		}

	})();

	var element2 = document.getElementById('cont');

	var Scroller2 = new AutoScroll(element2, {
		speed: 1
	});
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/paho-mqtt/1.0.1/mqttws31.js" type="text/javascript"></script>

<script type="text/javascript"> 
	var MQTTbroker = 'mqtt.beacontelemetry.com';
	var MQTTport = 8083;
	var MQTTsubTopic = "awgc-10213";
	var dataTopics = new Array();
	var client = new Paho.MQTT.Client(MQTTbroker, MQTTport,
									  "clientid_" + parseInt(Math.random() * 100, 10));
	client.onMessageArrived = onMessageArrived;
	client.onConnectionLost = onConnectionLost;
	console.log(MQTTsubTopic);
	var options = {
		timeout: 3,
		useSSL: true,
		userName : "userlog",
		password : "b34c0n",

		onSuccess: function () {
			console.log("mqtt connected");
			client.subscribe(MQTTsubTopic, {qos: 0});
		},
		onFailure: function (message) {
			console.log(message);
		}
	};
	function onConnectionLost(responseObject) {
	};
	function onMessageArrived(message) {
		var dataLog = message.payloadString;
		$.ajax({
			url: '<?php echo base_url(); ?>beranda/mqtt_send',
			method: 'get',
			success:function(data){
				const obj = JSON.parse(data);
				$('#waktu_logger').text(obj['waktu']);
				obj['param'].forEach(function(item) {
					var id_param = item['nama_parameter'];
					$('#'+id_param).text(item['nilai'] + ' ' + item['satuan']);
				});
				obj['data_pintu'].forEach(function(item){
					var elev = item['elevasi'];
					var s = 100 - elev;
					$("#elev-" + item['id_pintu']).css({
						"top": "calc(100% - " + elev + "%)",
						"transform": "translateY(calc(0% - " + s + "%))"
					});
					$('#elevt-'+item['id_pintu']).text(elev + ' %');
					if(item['r'] == '1'){
						$('#r-'+item['id_pintu']).addClass('bg-danger');	
						$('#r-'+item['id_pintu']).removeClass('bg-secondary');	
						$('#r-'+item['id_pintu']).text('ON');
					}else{
						$('#r-'+item['id_pintu']).addClass('bg-secondary');
						$('#r-'+item['id_pintu']).removeClass('bg-danger');	
						$('#r-'+item['id_pintu']).text('OFF');
					}
					if(item['s'] == '1'){
						$('#s-'+item['id_pintu']).addClass('bg-yellow');	
						$('#s-'+item['id_pintu']).removeClass('bg-secondary');	
						$('#s-'+item['id_pintu']).text('ON');
					}else{
						$('#s-'+item['id_pintu']).addClass('bg-secondary');
						$('#s-'+item['id_pintu']).removeClass('bg-yellow');	
						$('#s-'+item['id_pintu']).text('OFF');
					}
					if(item['t'] == '1'){
						$('#t-'+item['id_pintu']).addClass('bg-success');	
						$('#t-'+item['id_pintu']).removeClass('bg-secondary');	
						$('#t-'+item['id_pintu']).text('ON');
					}else{
						$('#t-'+item['id_pintu']).addClass('bg-secondary');
						$('#t-'+item['id_pintu']).removeClass('bg-success');	
						$('#t-'+item['id_pintu']).text('OFF');
					}
				});
			}
		});
		
	};
	client.connect(options);

</script>