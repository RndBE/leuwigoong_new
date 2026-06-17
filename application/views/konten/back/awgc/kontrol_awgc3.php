<script src="<?php echo base_url(); ?>code/highcharts.js"></script>
<script src="<?php echo base_url(); ?>code/highcharts-more.js"></script>
<script src="<?php echo base_url(); ?>code/modules/series-label.js"></script>
<script src="<?php echo base_url(); ?>code/modules/exporting.js"></script>
<script src="<?php echo base_url(); ?>code/modules/export-data.js"></script>
<script src="<?php echo base_url(); ?>code/js/themes/grid.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
	@media only screen and (max-width: 576px) {
		#target {
			display: none;
		}
	}

	.btn-info {
		background-color: #303481;
	}

	.btn-info:hover {
		text-decoration: none;
		background-color: #000342;
		border-color: #000342;
	}

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

	input[type=checkbox] {
		position: relative;
		border: 1px solid #e2e5ea;
		box-shadow: 0px .2px 2px #e2e5ea;
		border-radius: 3px;
		background: none;
		cursor: pointer;
		line-height: 0;
		margin: 0 2px 0 0;
		outline: 0;
		padding: 0 !important;
		vertical-align: text-top;
		height: 18px;
		width: 18px;
		-webkit-appearance: none;
		opacity: 1;
	}

	input[type=checkbox]:hover {
		opacity: 1;
	}

	input[type=checkbox]:checked {
		background-color: #000;
		opacity: 1;
	}

	input[type=checkbox]:before {
		content: '';
		position: absolute;
		right: 50%;
		top: 50%;
		width: 4px;
		height: 10px;
		border: solid #FFF;
		border-width: 0 2px 2px 0;
		margin: -1px 0px 0px -1px;
		transform: rotate(45deg) translate(-50%, -50%);
		z-index: 2;
	}

	.c-box label {
		cursor: pointer
	}

	.circle {
		width: 12px;
		height: 12px;
		border-radius: 50%;
		box-shadow: 0px 0px 1px 1px #0000001a;
	}

	.pulse-brown {
		background: #876a2f;
		animation: pulse-animation-brown 2s infinite;
	}

	@keyframes pulse-animation-brown {
		0% {
			box-shadow: 0 0 0 0px #876a2f;
		}

		100% {
			box-shadow: 0 0 0 15px rgba(0, 0, 0, 0);
		}
	}
</style>
<?php
$perbaikan = $this->db->get_where('t_perbaikan', array('id_logger' => $this->session->userdata('idlogger')))->row();
if ($perbaikan) {
	$stts = '1';
	$status_logger = "Perbaikan";
} else {
	$stts = '0';
}
?>

<div class="container-md">
	<div class="page-header d-print-none">
		<div class="row g-3 align-items-center">
			<div class="col-auto">

				<?php
				echo anchor('beranda', '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-big-left-lines" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
			<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
			<path d="M12 15v3.586a1 1 0 0 1 -1.707 .707l-6.586 -6.586a1 1 0 0 1 0 -1.414l6.586 -6.586a1 1 0 0 1 1.707 .707v3.586h3v6h-3z"></path>
			<path d="M21 15v-6"></path>
			<path d="M18 15v-6"></path>
		</svg>
') ?>

			</div>
			<?php if ($stts == '1') { ?>
			<div class="col-auto ">
				<div class="circle pulse-brown mx-3"></div>
			</div>
			<?php } else { ?>
			<div class="col-auto">
				<span class="status-indicator status-<?php echo $color ?> status-indicator-animated">
					<span class="status-indicator-circle"></span>
					<span class="status-indicator-circle"></span>
					<span class="status-indicator-circle"></span>
				</span>
			</div>
			<?php } ?>

			<div class="col col-md-auto">
				<h2 class="page-title mb-1">
					<?php echo $this->session->userdata('namalokasi') ?> 
				</h2>
				<div class="text-muted">
					<ul class="list-inline list-inline-dots mb-0">
						<?php if ($stts == '1') { ?>
						<li class="list-inline-item"><span style="color:#876a2f"><?php echo $status_logger ?></span></li>
						<?php } else { ?>
						<li class="list-inline-item"><span class="text-<?php echo $color ?>"><?php echo $status_logger ?></span></li>
						<?php } ?>
					</ul>
				</div>
			</div>
			<div class="col-12 col-md mt-4 mt-md-0">

				<div class="row g-3 align-items-center justify-content-end">
					<div class="col-12 col-md-auto <?= ($status_kontrol->status_kontrol == '0') ? 'd-none': '' ?> " id="use_kontrol">
						<a class="btn w-100 border-danger text-danger " role="button">
							<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-alert-square-rounded text-danger" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
								<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
								<path d="M12 3c7.2 0 9 1.8 9 9s-1.8 9 -9 9s-9 -1.8 -9 -9s1.8 -9 9 -9z"></path>
								<path d="M12 8v4"></path>
								<path d="M12 16h.01"></path>
							</svg>
							Kontrol Pintu Otomatis Sedang Digunakan
						</a>
					</div>
					<div class="col-12 col-md-auto">
						<a class="btn w-100" href="<?= base_url() . 'awgc/set_sensordash?tabel=awgc&id_logger=' . $this->session->userdata('idlogger') . '&jenis=logger' ?>">
							<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chart-dots" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
								<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
								<path d="M3 3v18h18"></path>
								<path d="M9 9m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
								<path d="M19 7m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
								<path d="M14 15m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
								<path d="M10.16 10.62l2.34 2.88"></path>
								<path d="M15.088 13.328l2.837 -4.586"></path>
							</svg>
							Analisa
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="page-body">
	<div class="container-xl">
		<div class="row msn_row">
			<div class="col-md-3">
				<div class="card">
					<div class="card-header py-3">
						<h3 class="mb-0">Panel Kontrol</h3>
					</div>
					<div class="card-body">
						<div class="subheader"><label class="form-label">Pilih Pintu</label></div>
						<div class="h3 m-0">
							<select type="text" name="id_pintu[]" class="form-select" multiple placeholder="Pilih Pintu" id="select-pos2" autocomplete="off">	
								<?php foreach ($pintu['list_pintu'] as $mnpos) : ?>
								<option value="<?= 'list-' . $mnpos['id_pintu'].'-'.$mnpos['batas_atas'] ?>" <?= ($mnpos['status_controller'] == '1' and $mnpos['status_rst'] == '1') ? '': 'disabled' ?>><?= str_replace('_', ' ', $mnpos['nama_pintu'])  ?> <?=  ($mnpos['status_controller'] == '1'  and $mnpos['status_rst'] == '1') ? '': '- Off' ?></option>
								<?php endforeach ?>
							</select>
						</div>
						<div id="persentase" style="display: none;">
							<div class="subheader mt-3"><label class="form-label">Set Persentase</label></div>
							<div class="d-flex">
								<button class="btn btn-secondary me-2 px-3 text-center minus mb-0">-</button>
								<input id="elev" class="form-control text-center" value="50" />
								<span class="btn btn-secondary ms-2 px-3 text-center plus ">+</span>
							</div>

						</div>
						<div class="w-100 text-end">
							<button type="button" class="btn btn-primary kontrol mt-3 btn-kontrol" <?= ($status_kontrol->status_kontrol == '1' or $status == 'Off') ? 'disabled': '' ?>>Kontrol</button>
						</div>
					</div>
				</div>
				<div class="card mt-3">
					<div class="card-header py-3 d-flex justify-content-between">
						<h3 class="mb-0">Elevasi Pintu</h3><span class="ms-1 fw-normal temp_waktu"><?= $waktuterakhir ?></span>
					</div>
					<div class="card-body pb-1">
						<div class="row elev_all">
							<?php
	foreach ($pintu['list_pintu'] as $l) { ?>
							<div class="col-lg-6 mb-3">
								<div class="card">
									<div class="card-body py-3 text-center">
										<h4 class="mb-1 fw-normal <?= ($l['status_controller'] == '1' and $l['status_rst'] == '1') ? '':'text-secondary' ?>">
											<?= $l['nama_pintu']  ?><?= ($l['status_controller'] == '1' and $l['status_rst'] == '1') ? '':' - Off'  ?></h4>
										<h2 class="mb-0 fw-bold <?= ($l['status_controller'] == '1' and $l['status_rst'] == '1') ? '':'text-secondary' ?>  "><?= $l['elevasi'] ?> <?=$l['satuan']?></h2>
									</div>
								</div>
							</div>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-9 mt-3 mt-md-0">
				<div class="card">
					<div class="card-header py-3">
						<h3 class="mb-0">Kontrol Pintu</h3>
					</div>
					<div class="card-body">
						<div class="row" id="list_kontrol">
							<?php
							if (!$pintu['list_pintu']) {
								echo '<h4 class="mb-0">Pintu belum dipilih</h4>';
							}
							foreach ($pintu['list_pintu'] as $mnpos) : ?>
							<div class="col-md-6 col-lg-4 col-xl-3 mb-3" id="<?= 'list-' . $mnpos['id_pintu'].'-'.$mnpos['batas_atas']?>" style="display:none ;">
								<div class="card">
									<div class="card-header py-2 d-flex justify-content-between">
										<h3 class="mb-0 fw-bold nmpintu<?= $mnpos['id_pintu'] ?>"><?= $mnpos['nama_pintu'] ?></h3>
										<div class="d-flex">
											<div class="badge bg-<?= ($mnpos['r'] == '0') ? 'secondary' : 'red' ?> text-<?= ($mnpos['r'] == '0') ? 'secondary' : 'red' ?>-fg me-2 badge-pill">R</div>
											<div class="badge bg-<?= ($mnpos['s'] == '0') ? 'secondary' : 'yellow' ?> text-<?= ($mnpos['s'] == '0') ? 'secondary' : 'yellow' ?>-fg me-2 badge-pill">S</div>
											<div class="badge bg-<?= ($mnpos['t'] == '0') ? 'secondary' : 'success' ?> text-<?= ($mnpos['t'] == '0') ? 'secondary' : 'success' ?>-fg  badge-pill">T</div>
										</div>
									</div>
									<div class="card-body">
										<div class="px-xxl-4" style="position: relative;">
											<div class="top">
												<div class="up"></div>
												<div class="up2">
													<img class="act-left" src="<?= base_url() ?>image/actuator.svg" alt="">
													<div class="mid"></div>
													<img class="act-right" src="<?= base_url() ?>image/actuator.svg" alt="">
												</div>
											</div>
											<div class="cont" style="position: relative;height: 150px;">
												<div class="left"></div>
												<?php
$range = $mnpos['batas_atas'] - $mnpos['batas_bawah'];
$raw = ($mnpos['elevasi'] - $mnpos['batas_bawah']) / $range * 100;
$persen = max(0, min(100, $raw));
?>
<div class="middle middle<?= $mnpos['id_pintu'] ?> d-flex align-items-center justify-content-center"
     style="top: calc(100% - <?= $persen ?>%);
            transform: translateY(calc(0% - calc(100% - <?= $persen ?>%)));">
													<h1 class="mb-0 counter<?= $mnpos['id_pintu'] ?>"><?= $mnpos['batas_atas'] ?></h1>
													<h1 class="mb-0 ms-2 satuan<?= $mnpos['id_pintu'] ?>"><?= $mnpos['satuan'] ?></h1>
												</div>
												<div class="right"></div>
											</div>

										</div>
										<div id="set_persen<?= $mnpos['id_pintu'] ?>">
											<div class="subheader mt-3 text-center"><label class="form-label">Set Ketinggian</label></div>
											<input type="text" value="<?= $mnpos['elevasi'] ?>" id="elev-asli<?= $mnpos['id_pintu'] ?>" class="d-none">
											<div class="d-flex">
												<button class="btn btn-secondary me-2 px-3 text-center minus<?= $mnpos['id_pintu'] ?> mb-0">-</button>
												<input id="batas_atas<?= $mnpos['id_pintu'] ?>" class="form-control text-center d-none" value="<?= $mnpos['batas_atas'] ?>" />
												<input id="batas_bawah<?= $mnpos['id_pintu'] ?>" class="form-control text-center d-none" value="<?= $mnpos['batas_bawah'] ?>" />
												<input id="elev<?= $mnpos['id_pintu'] ?>" class="form-control text-center" value="<?= $mnpos['elevasi'] ?>" />
												<span class="btn btn-secondary ms-2 px-3 text-center plus<?= $mnpos['id_pintu'] ?>">+</span>
											</div>
										</div>
									</div>

								</div>
							</div>
							<?php endforeach ?>
						</div>

					</div>
				</div>
				<style>
					.ts-control .item {
						white-space: nowrap;
					}
				</style>
			</div>
		</div>
	</div>
	<div class="modal fade" id="tes" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
		<div class="modal-dialog modal-dialog-centered modal-md" id="modal-kontrol">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Kontrol Pintu</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body py-3">
					<h3 class="mb-0 fw-normal">Daftar Kontrol</h3>

					<table class="table table-bordered mt-2">
						<thead>
							<tr>
								<th>Nama Pintu</th>
								<th>Dari</th>
								<th>Ke</th>

							</tr>
						</thead>
						<tbody class="table-kontrol">
						</tbody>
					</table>
					<div id="kode_cont">
						<h3 class="mb-0 fw-normal mt-3">Kode Akses</h3>
						<input type="text" class="form-control mt-2 mb-1" name="kode_akses" id="kode_akses">
					</div>
					<small class="text-danger d-none" id="kode_salah">*Kode Akses Salah</small>
					<small class="text-danger d-none" id="proses_gagal">*Kontrol Pintu Gagal</small>
					<div id="kontrol_process" class=""></div>
				</div>
				<div class="modal-footer py-1 d-flex justify-content-center">
					<button type="button" id="tutup" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
					<button type="button" id="stop" class="btn btn-danger d-none">STOP</button>
					<button type="button" class="btn btn-primary" id="submit_kontrol"><i id="load1" class="fa-solid fa-spinner fa-spin me-2 d-none"></i>Lanjut</button>
				</div>
			</div>
		</div>
	</div>
	<?php
	$list_select = [];
								 foreach ($pintu['list_pintu'] as $val) {
									 $list_select[] = 'list-' . $val['id_pintu'] .'-'.$val['batas_atas'];
								 }
	?>
</div>
<div class="modal fade" id="informasi" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Informasi Sistem Kontrol AWGC</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary">Save changes</button>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="locationModal" tabindex="-1" aria-labelledby="locationModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-body py-4 d-flex justify-content-center align-items-center flex-column">
				<img src="<?= base_url() ?>image/location_warning.png" class="img-fluid">
				<h3 class="mb-0 text-center mt-3">Izinkan Akses Lokasi untuk melakukan kontrol pintu.</h3>
			</div>
		</div>
	</div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/paho-mqtt/1.0.1/mqttws31.js" type="text/javascript"></script>

<script>
	// @formatter:off
	document.addEventListener("DOMContentLoaded", () => {
		if (!window.TomSelect) return;

		new TomSelect(document.getElementById('select-pos2'), {
			plugins: { remove_button: { title: 'Remove this item' } },
			controlInput: null,
			<?= ($this->session->userdata('idlogger') == '10349')  ? 'maxItems: 1':''  ?>
		});

		new TomSelect(document.getElementById('pilih-parameter'), {
			controlInput: null
		});

		new TomSelect(document.getElementById('pilih-sesi'), {
			controlInput: null
		});

		new TomSelect(document.getElementById('pilih-durasi'), {});

		new TomSelect(document.getElementById('select-state'), {});
	});

	$(document).ready(function() {

		$.fn.inputFilter = function(callback, errMsg) {
			return this.on("input keydown keyup mousedown mouseup select contextmenu drop focusout", function(e) {
				if (callback(this.value)) {
					// Accepted value
					if (["keydown", "mousedown", "focusout"].indexOf(e.type) >= 0) {
						$(this).removeClass("input-error");
						this.setCustomValidity("");
					}
					this.oldValue = this.value;
					this.oldSelectionStart = this.selectionStart;
					this.oldSelectionEnd = this.selectionEnd;
				} else if (this.hasOwnProperty("oldValue")) {
					// Rejected value - restore the previous one
					$(this).addClass("input-error");
					this.setCustomValidity(errMsg);
					this.reportValidity();
					this.value = this.oldValue;
					this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
				} else {
					// Rejected value - nothing to restore
					this.value = "";
				}
			});
		};
		var total_pos = <?php echo json_encode($list_select) ?>;
		var list_check = [];
		var selectedValues;
		$('#select-pos2').change(function() {
			selectedValues = $(this).val();
			list_check = $(this).val();

			if (selectedValues.length > 1) {
				$(".c-box").toggle(true);
				if (!$('#elev-sama').is(':checked')) {
					set_before();
				}
			} else {
				$("#persentase").toggle(false);
				$("#elev-sama").prop('checked', false);
				$(".c-box").toggle(false);
				set_before();
			}
			total_pos.forEach(function(item) {

				if (jQuery.inArray(item, selectedValues) !== -1) {
					$("#" + item).toggle(true);
					if ($('#elev-sama').is(':checked')) {
						var elev = $('#elev').val();
						var s = 100 - elev;

						list_check.forEach(function(item) {
							$('#set_persen' + item.split("-")[1]).hide();
							$(".middle" + item.split("-")[1]).css({
								"top": "calc(100% - " + elev + "%)",
								"transform": "translateY(calc(0% - " + s + "%))"
							});
							$(".counter" + item.split("-")[1]).text(elev);
						});
					}
				} else {
					$("#" + item).toggle(false);
				}
			});
			if (selectedValues.length < 1) {

				$('#list_kontrol').append('<h4 class="mb-0 fw-normal kontrol_null">Pilih pintu terlebih dahulu</h4>');
				$('.btn-kontrol').hide();
			} else {
				$('.btn-kontrol').show();
				$('.kontrol_null').remove();
			}

		});

		$('.btn-kontrol').hide();

		$('#list_kontrol').append('<h4 class="mb-0 fw-normal kontrol_null">Pilih pintu terlebih dahulu</h4>');

		var all_pintu = <?php echo json_encode($pintu['list_pintu']) ?>;
		all_pintu.forEach(function(item) {
			let interval = item;
			$('#elev' + item.id_pintu).change(function () {
				var elev = parseInt($(this).val())
				var min = parseInt(item.batas_bawah)
				var max = parseInt(item.batas_atas)

				var elev_cm = ((elev - min) / (max - min)) * 100
				elev_cm = Math.max(0, Math.min(100, elev_cm))

				var s = 100 - elev_cm

				$(".middle" + item.id_pintu).css({
					top: "calc(100% - " + elev_cm + "%)",
					transform: "translateY(calc(0% - " + s + "%))"
				})

				$(".counter" + item.id_pintu).text(elev)
			})


			$('#elev' + item.id_pintu).inputFilter(function (value) {
				return /^-?\d*$/.test(value) &&
					(
					value === "" ||
					(
						parseInt(value) >= item.batas_bawah &&
						parseInt(value) <= item.batas_atas
					)
				)
			}, "Masukkan angka " + item.batas_bawah + " – " + item.batas_atas)

			$('.plus' + item.id_pintu).mousedown(function() {
				interval = setInterval(incrementValue($("#elev" + item.id_pintu), item.batas_atas), 100);
			});

			$('.minus' + item.id_pintu).mousedown(function() {
				interval = setInterval(decrementValue($("#elev" + item.id_pintu), item.batas_bawah), 100);
			});
			$('.plus' + item.id_pintu).mouseup(function() {
				clearInterval(interval);
			});
			$('.minus' + item.id_pintu).mouseup(function() {
				clearInterval(interval);
			});
		});
		$("#elev").inputFilter(function(value) {
			return /^-?\d*$/.test(value) && (value === "" || parseInt(value) <= 100);
		}, "Masukkan angka 1 - 100");
		$('#elev').change(function() {
			var elev = $(this).val();
			var s = 100 - elev;

			list_check.forEach(function(item) {
				$(".middle" + item.split("-")[1]).css({
					"top": "calc(100% - " + elev + "%)",
					"transform": "translateY(calc(0% - " + s + "%))"
				});
				$(".counter" + item.split("-")[1]).text(elev);
			});
		});
		$('.plus').mousedown(function() {
			interval = setInterval(incrementValue($("#elev"),100), 100);
		});

		$('.minus').mousedown(function() {
			interval = setInterval(decrementValue($("#elev"),item), 100);
		});

		$('.plus').mouseup(function() {
			clearInterval(interval);
		});

		$('.minus').mouseup(function() {
			clearInterval(interval);
		});

		$('#elev-sama').change(function() {

			if ($(this).is(':checked')) {
				$("#persentase").toggle(this.checked);
				var elev = $('#elev').val();

				list_check.forEach(function(item) {
					var bts_atas = item.split("-")[2];
					var elv = elev > bts_atas ? bts_atas : elev;
					var elev_cm2 = elv/bts_atas*100;
					var s = 100 - elev_cm2;
					$('#set_persen' + item.split("-")[1]).hide();
					$(".middle" + item.split("-")[1]).css({
						"top": "calc(100% - " + elev_cm2 + "%)",
						"transform": "translateY(calc(0% - " + s + "%))"
					});
					$(".counter" + item.split("-")[1]).text(elv);
				});
			} else {
				$("#persentase").toggle(false);
				set_before();
			}
		});

		$('.toggle').click(function() {
			$('#target').toggle('fast');
		});

		function set_before() {
			list_check.forEach(function(item) {
				$('#set_persen' + item.split("-")[1]).show();
				var id = item.split("-")[1]

				var min = parseFloat($('#batas_bawah' + id).val())
				var max = parseFloat($('#batas_atas' + id).val())
				var elev = parseFloat($('#elev' + id).val())

				var range = max - min
				var elev_cm = range === 0 ? 0 : ((elev - min) / range) * 100
				elev_cm = Math.max(0, Math.min(100, elev_cm))

				$(".middle" + id).css({
					top: "calc(100% - " + elev_cm + "%)",
					transform: "translateY(calc(0% - calc(100% - " + elev_cm + "%)))"
				})
				$(".counter" + item.split("-")[1]).text(elev);
			});
		}

		var latitude = '';
		var longitude = '';
		function getLocation() {
			if (navigator.geolocation) {
				navigator.geolocation.getCurrentPosition(
					function (position) {
						// If permission is granted
						latitude = position.coords.latitude;
						longitude = position.coords.longitude;
						var nm_pos = $('#select-pos').val();
						var nm_pintu = $('#select-pos2').val();
						var nm_metode = $('#pilih-metode').val();

						$(".table-kontrol").empty();
						list_check.forEach(function(item) {
							console.log(item);
							var id_pintu = item.split("-")[1];
							var nama_pintu = $('.nmpintu' + id_pintu).text();
							var elev = $('.counter' + id_pintu).text();
							var elev_asli = $('#elev-asli' + id_pintu).val();
							var satuan = $('.satuan' + id_pintu).text();
							$('.table-kontrol').append(
								'<tr><td class="align-middle">' + nama_pintu + '</td><td class="align-middle">' + elev_asli + ' '+satuan+'</td><td class="align-middle"> <h3 class="mb-0">' + elev + ' '+satuan+'</h3></td></tr>'
							);

						});
						$('#tes').modal('show');
					},
					function (error) {
						switch (error.code) {
							case error.PERMISSION_DENIED:
								console.log("User denied the request for Geolocation.");
								break;
							case error.POSITION_UNAVAILABLE:
								console.log("Location information is unavailable.");
								break;
							case error.TIMEOUT:
								console.log("The request to get user location timed out.");
								break;
							case error.UNKNOWN_ERROR:
								console.log("An unknown error occurred.");
								break;
						}
						if (error.code === error.PERMISSION_DENIED) {
							$('#locationModal').modal('show');
						} else {
							var nm_pos = $('#select-pos').val();
							var nm_pintu = $('#select-pos2').val();
							var nm_metode = $('#pilih-metode').val();

							$(".table-kontrol").empty();
							list_check.forEach(function(item) {
								console.log(item);
								var id_pintu = item.split("-")[1];
								var nama_pintu = $('.nmpintu' + id_pintu).text();
								var elev = $('.counter' + id_pintu).text();
								var elev_asli = $('#elev-asli' + id_pintu).val();
								$('.table-kontrol').append(
									'<tr><td class="align-middle">' + nama_pintu + '</td><td class="align-middle">' + elev_asli + ' cm</td><td class="align-middle"> <h3 class="mb-0">' + elev + ' cm</h3></td></tr>'
								);

							});
							$('#tes').modal('show');
						}
					}
				);
			} else {
				alert("Geolocation is not supported by this browser.");
			}
		}

		function showError(error) {
			switch (error.code) {
				case error.PERMISSION_DENIED:
					alert("User denied the request for Geolocation.");
					break;
				case error.POSITION_UNAVAILABLE:
					alert("Location information is unavailable.");
					break;
				case error.TIMEOUT:
					alert("The request to get user location timed out.");
					break;
				case error.UNKNOWN_ERROR:
					alert("An unknown error occurred.");
					break;
			}
		}

		function showPosition(position) {
			const latitude = position.coords.latitude;
			const longitude = position.coords.longitude;

		}
		function incrementValue($input, batas) {
			var count = parseInt($input.val()) + 5;
			var batas_atas = parseInt(batas);
			count = count > batas_atas ? batas_atas : count;
			$input.val(count);
			$input.change();
		}
		function decrementValue($input,batas) {
			var count = parseInt($input.val()) - 5;
			var batas_bawah = parseInt(batas);
			count = count < batas_bawah ? batas_bawah : count;
			$input.val(count);
			$input.change();
		}
		$('.kontrol').click(function() {
			getLocation();
		});
		var lanjut_kontrol = [];

		if(!$('#kode_akses').val()) {
			$('#submit_kontrol').prop("disabled", true);
		}
		$('#kode_akses').on("input", function() {
			if ($(this).val() !== "") {
				//alert($('#tes').hasClass('show'));
				$('#submit_kontrol').prop("disabled", false);
			} else {
				$('#submit_kontrol').prop("disabled", true);
			}
		});


		$('#submit_kontrol').click(function () {
			$('#load1').removeClass('d-none');
			$('#submit_kontrol').prop('disabled',true);
			lanjut_kontrol = [];
			list_check.forEach(function(item) {
				var id_pintu = item.split("-")[1];
				var nama_pintu = $('.nmpintu' + id_pintu).text();
				var elev = $('.counter' + id_pintu).text();

				var elev_asli = $('#elev-asli' + id_pintu).val();
				lanjut_kontrol.push({
					'id_pintu':id_pintu,
					'nama_pintu':nama_pintu,
					'elev':elev,
					'elev_asli':elev_asli,
				});
			});
			var inp_kode = $('#kode_akses').val();
			console.log(lanjut_kontrol);
			$.ajax({
				type: "POST",
				url: "<?php echo base_url(); ?>kontrol/lanjut_kontrol",
				data: {
					akses: inp_kode,
					data: lanjut_kontrol,
				},
				dataType: "JSON",
				error: function(xhr){
					console.log('RAW RESPONSE:', xhr.responseText);
				},
				success: function (data) {
					console.log(data);
					if(data.status == 'error'){
						$('#kode_salah').removeClass('d-none');
					}else if(data.status == 'fail'){
						$('#proses_gagal').removeClass('d-none');
					} else{
						$('#proses_gagal').addClass('d-none');
						$('#kode_salah').addClass('d-none');
						$('#kode_cont').addClass('d-none');
						$('#tutup').addClass('d-none');
						$('#kontrol_process').append('<div class="d-flex align-items-center"><i class="fa-solid fa-check me-2"></i><span>Mengirim input ke logger</span></div><div class="d-flex align-items-center mt-2" ><i id="resp_log" class="fa-solid fa-spinner fa-spin me-2 "></i><span>Menunggu respon logger</span></div><div class="d-flex mt-2 align-items-center" id="start_op"> </div><div class="d-flex mt-2 align-items-center" id="stop_op"> </div></div>');
						$('.table-kontrol').empty();
						var conv = JSON.parse(data.data);

						conv.forEach(function(item) {

							$('.table-kontrol').append(
								'<tr><td class="align-middle">' + item.nama_pintu + item.status + '</td><td class="align-middle">' + item.elev_asli + ' cm</td><td class="align-middle">' + item.elev_kontrol + ' cm</td></tr>'
							);
						});

						localStorage['status'] = '1'; 
					}

					$('#load1').addClass('d-none');

				}
			}).fail(function(jqXHR, textStatus, errorThrown) {
				console.error("AJAX Error: " + textStatus, errorThrown);
			});

		});
		var stop_kontrol = [];
		$('#stop').click(function () {
			$.ajax({
				type: "POST",
				url: "<?php echo base_url(); ?>kontrol/stop_kontrol",
				data: {
					data: lanjut_kontrol,
				},
				dataType: "JSON",
				success: function (data) {
					if(data.status == 'success'){
						$('#start_icon').removeClass('fa-spinner fa-spin').addClass('fa-remove');
						$('#stop_op').append('<i id="stop_icon" class="fa-solid fa-spinner fa-spin me-2"></i><span>Menghentikan Operasi</span>');
						$('#stop').addClass('d-none');
						localStorage['status'] = '3';
					}
				}
			}).fail(function(jqXHR, textStatus, errorThrown) {
				// Handle the error here
				console.error("AJAX Error: " + textStatus, errorThrown);
			});
		});

		localStorage['status'] = '0';

		var MQTTbroker = 'mqtt.beacontelemetry.com';
		var MQTTport = 8083;
		var MQTTsubTopic = "awgc-<?= $this->session->userdata('idlogger') ?>";
		var MQTTsubTopic2 = "kontrol_pintu-<?= $this->session->userdata('idlogger') ?>";
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
				client.subscribe(MQTTsubTopic2, {qos: 0});
			},
			onFailure: function (message) {
				console.log(message);
			}
		};
		function onConnectionLost(responseObject) {
		};
		function onMessageArrived(message) {
			var sts = localStorage['status'] || '0';
			var dataLog = message.payloadString;
			var dataLogObj = JSON.parse(dataLog);
			$('.temp_waktu').text(dataLogObj.waktu);
			var inp_kode = $('#kode_akses').val();
			if(message.destinationName == 'kontrol_pintu-<?= $this->session->userdata("idlogger") ?>') {

				if(dataLogObj.status_kontrol != '0' ){
					$('.btn-kontrol').prop('disabled',true);
					$('#submit_kontrol').prop("disabled", true);
					$('#use_kontrol').removeClass('d-none');
				}else{
					$('.btn-kontrol').prop('disabled',false);
					$('#select-pos2').prop("disabled",false);
					$('#submit_kontrol').prop("disabled", false);
					$('#use_kontrol').addClass('d-none');
				}
			}else{
				$.ajax({
					url: '<?php echo base_url(); ?>awgc/temp_ajax?id_logger='+ dataLogObj.id_logger,
					method: 'get',
					success:function(data){
						const obj = JSON.parse(data);

						$('.elev_all').replaceWith('<div class="row elev_all"> ' + obj['panel'] + ' </div>');
					}
				});

				$.ajax({
					url: '<?php echo base_url(); ?>kontrol/status_kontrol?id_logger='+ dataLogObj.id_logger,
					method: 'get',
					dataType: "JSON",
					success:function(data){
						if(data.status_kontrol != '0'){
							if(sts == '1'){
								$('#resp_log').removeClass('fa-spinner fa-spin').addClass('fa-check');
								$('#stop').removeClass('d-none');
								$('#tutup').addClass('d-none');
								if($('#start_icon').length)
								{
								}else{
									$('#start_op').append('<i id="start_icon" class="fa-solid fa-spinner fa-spin me-2"></i><span>Memulai Operasi</span>');
								}
								localStorage['status'] = '2';
							}
						}else{
							if(sts == '2'){
								$('#start_icon').removeClass('fa-spinner fa-spin').addClass('fa-check');
								localStorage['status'] = '0';
								$.ajax({
									type: "POST",
									url: "<?php echo base_url(); ?>kontrol/selesai_kontrol/"+ dataLogObj.id_logger,
									data: {
										list_pintu: lanjut_kontrol,
									},
									dataType: "JSON",
									success: function (data) {
										console.log(data);
									}
								}).fail(function(jqXHR, textStatus, errorThrown) {
									console.error("AJAX Error: " + textStatus, errorThrown);
								});
								location.reload();
							}else if(sts == '3') {
								$('#stop_icon').removeClass('fa-spinner fa-spin').addClass('fa-check');
								localStorage['status'] = '0';
								location.reload();
							} else if(sts == '1'){
								location.reload();
							}
						}
						console.log('local : ' + sts);
						console.log('db : ' + data.status_kontrol);
					}
				}); 
			}
		};
		client.connect(options);
	});
</script>