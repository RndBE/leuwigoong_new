<script src="<?php echo base_url(); ?>code/highcharts.js"></script>
<script src="<?php echo base_url(); ?>code/highcharts-more.js"></script>
<script src="<?php echo base_url(); ?>code/modules/series-label.js"></script>
<script src="<?php echo base_url(); ?>code/modules/exporting.js"></script>
<script src="<?php echo base_url(); ?>code/modules/export-data.js"></script>
<script src="<?php echo base_url(); ?>code/js/themes/grid.js"></script>
<link rel="stylesheet" href="<?= base_url() ?>js/yearpicker.css" />
<?php if ($this->session->userdata('data') == 'rentang') {
	$title = $this->session->userdata('dari') . " sampai " . $this->session->userdata('sampai');
} else {
	$title = $this->session->userdata('pada');
} ?>
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

$namafile = $this->session->userdata('namalokasi') . ' - ' . $data_sensor->namaSensor . ' - ' . $this->session->userdata('pada');


?>
<?php
$qstatus = $this->db->query('select waktu from ' . $this->session->userdata('tabel') . ' where code_logger="' . $this->session->userdata('idlogger') . '" order by waktu desc limit 1');
foreach ($qstatus->result() as $stat) {
	$awal = date('Y-m-d H:i', (mktime(date('H') - 1)));
	$waktuterakhir = $stat->waktu;
	if ($waktuterakhir >= $awal) {
		$color = "green";
		$status_logger = "Koneksi Terhubung";
	} else {
		$color = "dark";
		$status_logger = "Koneksi Terputus";
	}
	$stts = '0';
	$perbaikan = $this->db->get_where('t_perbaikan', array('id_logger' => $this->session->userdata('idlogger')))->row();
	if ($perbaikan) {
		$stts = '1';
		$status_logger = "Perbaikan";
	} else {
		$stts = '0';
	}
}

$stts = '0';
if ($data_sensor == null) {
	$namasensor = '';
} else {
	$namasensor = str_replace('_', ' ', $data_sensor->{'namaSensor'});
	$satuan = $data_sensor->{'satuan'};
	$tooltip = $data_sensor->{'tooltip'};
	$data = $data_sensor->{'data'};
	$range = $data_sensor->{'range'};
	$nosensor = $data_sensor->{'nosensor'};
	$typegraf = $data_sensor->{'tipe_grafik'};
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
					<?php echo $this->session->userdata('namalokasi'); ?>

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
			<div class="col-12 col-md">

				<div class="row g-3 align-items-center justify-content-end">
					<div class="col-6 col-md-auto">
						<button class="btn w-100 toggle" data-bs-toggle="modal" data-bs-target="#modalKalibrasi">
							<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-layout-list" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
								<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
								<path d="M4 4m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v2a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z"></path>
								<path d="M4 14m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v2a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z"></path>
							</svg>
							Kalibrasi TMA
						</button>
					</div>
					<div class="col-6 col-md-auto">
						<a class="btn w-100" data-bs-toggle="offcanvas" href="#offcanvasEnd" role="button" aria-controls="offcanvasEnd">
							<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-info" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
								<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
								<path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
								<path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path>
								<path d="M11 14h1v4h1"></path>
								<path d="M12 11h.01"></path>
							</svg>
							Informasi
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
			<div class="col-md-4 col-xl-3 col-xxl-2">
				<div class="card">
					<div class="card-header py-3">
						<h3 class="mb-0">Analisa Data</h3>
					</div>
					<div class="card-body">
						<form action="<?= base_url() ?>awgc/ubah_analisa" method="post" enctype="multipart/form-data">
							<div class="subheader"><label class="form-label">Pilih Parameter</label></div>
							<div class="h3 m-0">
								<select type="text" name="id_param" class="form-select" placeholder="Pilih Parameter" id="select-pos2">
									<option value="">Pilih Parameter</option>
									<?php foreach ($pilih_parameter as $mnparam) : ?>
									<option value="<?= $mnparam['id_param'] . '_' . str_replace('_', ' ', $mnparam['jenis']) ?>" <?= ($this->session->userdata('nama_parameter') == $mnparam['nama_parameter']) ? 'selected' : '' ?>><?= str_replace('_', ' ', $mnparam['nama_parameter']) ?></option>
									<?php endforeach ?>
								</select>
							</div>
							<div class="subheader mt-3"><label class="form-label">Analisa Dalam</label></div>

							<div class="d-flex mt-3 px-1 flex-wrap justify-content-between">
								<label class="form-check me-3">
									<input class="form-check-input" type="radio" name="data" value="hari" <?= ($this->session->userdata('data') == 'hari') ? 'checked' : '' ?> />
									<span class="form-check-label">Hari</span>
								</label>
								<label class="form-check me-3">
									<input class="form-check-input" type="radio" name="data" value="bulan" <?= ($this->session->userdata('data') == 'bulan') ? 'checked' : '' ?> />
									<span class="form-check-label">Bulan</span>
								</label>
								<label class="form-check mb-3">
									<input class="form-check-input" type="radio" name="data" value="tahun" <?= ($this->session->userdata('data') == 'tahun') ? 'checked' : '' ?> />
									<span class="form-check-label">Tahun</span>
								</label>
								<label class="form-check mb-3">
									<input class="form-check-input" type="radio" name="data" value="rentang" <?= ($this->session->userdata('data') == 'rentang') ? 'checked' : '' ?> />
									<span class="form-check-label">Rentang</span>
								</label>
							</div>
							<div class="mt-2">
								<div class="subheader sub_tgl <?= ($this->session->userdata('data') != 'hari') ? 'd-none' : '' ?>"><label class="form-label">Pilih Tanggal</label></div>
								<div class="input-icon <?= ($this->session->userdata('data') != 'hari') ? 'd-none' : '' ?>" id="pilih-tanggal">
									<input class="form-control " name="tanggal" placeholder="Pilih Tanggal" id="dptanggal" value="<?= $this->session->userdata('tanggal') ?>" autocomplete="off" />
									<span class="input-icon-addon ">
										<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
											<path stroke="none" d="M0 0h24v24H0z" fill="none" />
											<rect x="4" y="5" width="16" height="16" rx="2" />
											<line x1="16" y1="3" x2="16" y2="7" />
											<line x1="8" y1="3" x2="8" y2="7" />
											<line x1="4" y1="11" x2="20" y2="11" />
											<line x1="11" y1="15" x2="12" y2="15" />
											<line x1="12" y1="15" x2="12" y2="18" />
										</svg>
									</span>
								</div>
								<div class="subheader sub_bln <?= ($this->session->userdata('data') != 'bulan') ? 'd-none' : '' ?>"><label class="form-label">Pilih Bulan</label></div>
								<input type="month" name="bulan" value="<?= $this->session->userdata('bulan') ?>" class="form-control <?= ($this->session->userdata('data') != 'bulan') ? 'd-none' : '' ?>" id="pilih-bulan" />
								<div class="subheader sub_thn <?= ($this->session->userdata('data') != 'tahun') ? 'd-none' : '' ?>"><label class="form-label">Pilih Tahun</label></div>
								<div class="input-icon <?= ($this->session->userdata('data') != 'tahun') ? 'd-none' : '' ?>" id="pilih-tahun">
									<input type="text" name="tahun" class="yearpicker form-control" id="dptahun" value="<?= $this->session->userdata('tahun') ?>" autocomplete="off"/>

									<span class="input-icon-addon">
										<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
											<path stroke="none" d="M0 0h24v24H0z" fill="none" />
											<rect x="4" y="5" width="16" height="16" rx="2" />
											<line x1="16" y1="3" x2="16" y2="7" />
											<line x1="8" y1="3" x2="8" y2="7" />
											<line x1="4" y1="11" x2="20" y2="11" />
											<line x1="11" y1="15" x2="12" y2="15" />
											<line x1="12" y1="15" x2="12" y2="18" />
										</svg>
									</span>

								</div>
								<div id="rentang-hari" class="<?= ($this->session->userdata('data') != 'rentang') ? 'd-none' : '' ?>">
									<div class="subheader sub_rentang "><label class="form-label">Pilih Rentang</label></div>

									<div class="input-group mt-3" id="pilih-dari">
										<span class="input-group-text px-0 d-flex justify-content-center" style="width:60px">Dari</span>
										<input class="form-control " name="dari" placeholder="Pilih Tanggal Mulai" id="dpdari" value="<?= $this->session->userdata('dari') ?>" autocomplete="off" />
										<span class="input-icon-addon ">
											<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
												<path stroke="none" d="M0 0h24v24H0z" fill="none" />
												<rect x="4" y="5" width="16" height="16" rx="2" />
												<line x1="16" y1="3" x2="16" y2="7" />
												<line x1="8" y1="3" x2="8" y2="7" />
												<line x1="4" y1="11" x2="20" y2="11" />
												<line x1="11" y1="15" x2="12" y2="15" />
												<line x1="12" y1="15" x2="12" y2="18" />
											</svg>
										</span>
									</div>
									<div class="input-group mt-2" id="pilih-ke">
										<span class="input-group-text px-0 d-flex justify-content-center" style="width:60px">Sampai</span>
										<input class="form-control " name="sampai" placeholder="Pilih Tanggal Selesai" id="dpsampai" value="<?= $this->session->userdata('sampai') ?>" autocomplete="off" />
										<span class="input-icon-addon ">
											<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
												<path stroke="none" d="M0 0h24v24H0z" fill="none" />
												<rect x="4" y="5" width="16" height="16" rx="2" />
												<line x1="16" y1="3" x2="16" y2="7" />
												<line x1="8" y1="3" x2="8" y2="7" />
												<line x1="4" y1="11" x2="20" y2="11" />
												<line x1="11" y1="15" x2="12" y2="15" />
												<line x1="12" y1="15" x2="12" y2="18" />
											</svg>
										</span>
									</div>
								</div>
							</div>
							<div class="w-100 text-center mt-4">
								<button type="submit" class="btn btn-primary w-100"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check" width="24" height="24" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>Tampil Data</button>		
							</div>
						</form>
						<form method="post" action="<?= base_url() ?>awgc/export_excel_single">
							<input type="text" name='title' value=" <?php echo $title ?>" class="d-none"/>
							<input type="text" name="data" value="<?= htmlspecialchars(json_encode($data_sensor->data_tabel)) ?>" class="d-none">  

							<button type="submit" class="btn btn-outline-success w-100 mt-3" ><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-download" width="24" height="24" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M12 17v-6" /><path d="M9.5 14.5l2.5 2.5l2.5 -2.5" /></svg>Download Data</button>
						</form>
						<!--
   <button class="btn btn-outline-success w-100 mt-3" onclick="ExportToExcel('xlsx')"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-download" width="24" height="24" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M12 17v-6" /><path d="M9.5 14.5l2.5 2.5l2.5 -2.5" /></svg>Download Data</button> -->
					</div>
				</div>
				<div class="card mt-3">
					<div class="card-header py-3 d-flex justify-content-between px-3">
						<h3 class="mb-0">Log Kontrol</h3>
						<a href="<?= base_url() ?>awgc/log_kontrol"><h5 class="mb-0 fw-normal">Lihat Semua</h5></a>
					</div>
					<div class="card-body pb-0">
						<?php
	if (!$log) {
		echo '<div class="w-100 text-center mb-3"><span class="">Belum Ada Data</span></div>';
	}
											foreach ($log as $l) { ?>
						<div class="d-flex w-100 justify-content-between">
							<div>
								<h4 class="mb-1"><?= $l['id_pintu'] . ' - ' . $l['nama_pintu'] ?></h4>
								<h5 class="fw-normal"><?= $l['datetime'] ?></h5>
							</div>
							<div class="text-end">
								<h4 class="mb-1"><?= ($l['sistem'] == '1') ? 'Buka' : 'Tutup'  ?> </h4>
								<h5 class="fw-normal"><?= $l['dari'] ?> cm to <?= $l['ke'] ?> cm</h5>
							</div>
						</div>
						<?php } ?>
					</div>
				</div>
			</div>
			<div class="col-md-8 col-xl-9 col-xxl-10 mt-3 mt-md-0">

				<style>
					.ts-control .item {
						white-space: nowrap;
					}
				</style>
				<div class="card">
					<div class="card-header py-3 d-flex justify-content-between">
						<h3 class="mb-0">Grafik Analisa</h3>
					</div>

					<div class="card-body">

						<div id="analisa" class="pe-2"></div>
						<div class="w-100 mt-3 card">
							<?php if ($this->session->userdata('data') == 'range') {
	$title = " dari " . $this->session->userdata('dari') . " sampai " . $this->session->userdata('sampai');
} else {
	$title = " pada " . $this->session->userdata('pada');
} ?>
							<div class="table-responsive">
								<table class="table mb-0 table-bordered table-sm " id="tbl_exporttable_to_xls">
									<thead>
										<tr>
											<th colspan="4">
												<h5 class="mb-0 fw-bold"><?= str_replace("_", " ", "$data_sensor->namaSensor") ?></h5>
											</th>
										</tr>
										<tr>
											<th class="d-none">
												<h5 class="mb-0 fw-bold"><?= $this->session->userdata('pada') ?></h5>
											</th>
										</tr>
										<tr>
											<th>Waktu</th>
											<th><?= str_replace("_", " ", "$data_sensor->namaSensor") ?></th>
											<th>Minimal</th>
											<th>Maksimal</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ($data_sensor->data_tabel as $dt) : ?>
										<tr>
											<td><?= $dt->waktu ?></td>
											<td><?= $dt->dta . ' ' . $data_sensor->satuan ?></td>
											<td><?= $dt->min . ' ' . $data_sensor->satuan  ?></td>
											<td><?= $dt->max . ' ' . $data_sensor->satuan ?></td>
										</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							</div>
						</div>
						<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEnd" aria-labelledby="offcanvasEndLabel">
							<div class="offcanvas-header">
								<h2 class="offcanvas-title" id="offcanvasEndLabel">Informasi Logger</h2>
								<button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
							</div>
							<div class="offcanvas-body">
								<div>
									<table class="table table-sm table-borderless">
										<tbody>
											<?php
											$query_informasi = $this->db->query('select * from t_informasi where logger_id="' . $this->session->userdata('idlogger') . '"');

											foreach ($query_informasi->result() as $tinfo) {
											?>
											<tr>
												<td class="fw-bold">Id Logger</td>
												<td class="text-end"><?php echo $tinfo->logger_id ?></td>
											</tr>
											<tr>
												<td class="fw-bold">Seri Logger</td>
												<td class="text-end"><?php echo $tinfo->seri ?></td>
											</tr>
											<tr>
												<td class="fw-bold">Serial Number</td>
												<td class="text-end"><?php echo $tinfo->serial_number ?></td>
											</tr>
											<tr>
												<td class="fw-bold">Sensor</td>
												<td class="text-end"><?php echo $tinfo->sensor ?></td>
											</tr>
											<?php

												if ($this->uri->segment(1) == 'awlr') {
											?>
											<tr>
												<td class="fw-bold">Elevasi</td>
												<td class="text-end"><?php echo $tinfo->elevasi ?></td>
											</tr>
											<?php }	?>
											<tr>
												<td class="fw-bold">No. Seluler</td>
												<td class="text-end"><?php echo $tinfo->nosell  ?></td>
											</tr>
											<tr>
												<td class="fw-bold">IMEI</td>
												<td class="text-end"><?php echo $tinfo->imei ?></td>
											</tr>
											<tr>
												<td class="fw-bold">Tanggal Kontrak</td>
												<td class="text-end"><?php echo $tinfo->tgl_kontrak ?></td>
											</tr>
											<tr>
												<td class="fw-bold">Logger Aktif</td>
												<td class="text-end"><?php echo $tinfo->tgl_aktif ?></td>
											</tr>
											<tr>
												<td class="fw-bold">Masa Garansi</td>
												<td class="text-end"><?php echo $tinfo->garansi ?></td>
											</tr>

											<?php } ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="tes" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Kontrol Pintu</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-4">
							<h3 class="mb-0 fw-normal">Nama Pos</h3>
							<h3 class="mb-0 fw-normal">Nama Pintu</h3>
							<h3 class="mb-0 fw-normal">Metode Kontrol</h3>
							<h3 class="mb-0 fw-normal">Persentase</h3>
						</div>
						<div class="col-8">
							<h3 class="mb-0">: <span id="nm_pos"></span></h3>
							<h3 class="mb-0">: <span id="nm_pintu"></span></h3>
							<h3 class="mb-0">: <span id="nm_metode"></span></h3>
							<h3 class="mb-0">: <span id="nm_nilai"></span></h3>
						</div>
					</div>
					<h3 class="mb-0 fw-normal mt-3">Jalankan Kontrol ?</h3>
				</div>
				<div class="modal-footer py-1 d-flex justify-content-center">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
					<button type="button" class="btn btn-primary">Lanjut</button>
				</div>
			</div>
		</div>
	</div>
</div>



<div class="modal fade" id="modalKalibrasi" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Kalibrasi TMA</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>

			<div class="modal-body">
				<form id="formKalibrasi">
					<label class="form-label">Kalibrasi TMA</label>
					<div class="input-group mb-3">
						<input type="text" name="kalibrasi_tma" class="form-control float-only" required>
						<div class="input-group-append">
							<span class="input-group-text">meter</span>
						</div>
					</div>
					<input type="hidden" name="id_logger" value="<?= $this->session->userdata('idlogger')?>">
				</form>
			</div>

			<div class="modal-footer">
				<button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
				<button class="btn btn-primary" id="btnSubmitKalibrasi">Publish & Simpan</button>
			</div>
		</div>
	</div>
</div>
<script>
	$(document).on('input', '.float-only', function () {
		let v = $(this).val()
		v = v.replace(/[^0-9.]/g, '')
		v = v.replace(/(\..*)\./g, '$1')
		$(this).val(v)
	})

	$('#btnSubmitKalibrasi').on('click', function () {
		let form = $('#formKalibrasi')[0]
		if (!form.checkValidity()) {
			form.reportValidity()
			return
		}

		$.ajax({
			url: "<?= site_url('awgc/submit_kalibrasi') ?>",
			type: "POST",
			data: $('#formKalibrasi').serialize(),
			dataType: "json",
			success: function (res) {
				if (res.status === 'success') {
					$('#modalKalibrasi').modal('hide')
				}
			},
			error: function (e) {
				console.log(e);
				alert('Gagal mengirim data')
			}
		})
	})
</script>


<script>
	// @formatter:off
	document.addEventListener("DOMContentLoaded", function() {
		var el;
		window.TomSelect && (new TomSelect(el = document.getElementById('select-pos2'), {
			controlInput: null,
		}));
		window.TomSelect && (new TomSelect(el = document.getElementById('pilih-sesi'), {
			controlInput: null,
		}));
		window.TomSelect && (new TomSelect(el = document.getElementById('pilih-durasi'), {}));
		window.TomSelect && (new TomSelect(el = document.getElementById('pilih-parameter'), {}));
		window.TomSelect && (new TomSelect(el = document.getElementById('select-state'), {}));
	});

	// @formatter:on

	$(document).ready(function() {
		let interval;
		$('input[name="data"]').change(function() {
			var i = $(this).val();
			if (i == 'hari') {
				$('#dptanggal').prop('required', true);
				$('#pilih-tanggal').removeClass('d-none');
				$('#pilih-bulan').addClass('d-none');
				$('#pilih-tahun').addClass('d-none');
				$('#rentang-hari').addClass('d-none');
				$('.sub_tgl').removeClass('d-none');
				$('.sub_bln').addClass('d-none');
				$('.sub_thn').addClass('d-none');
				$('#dpdari').prop('required', false);
				$('#dpsampai').prop('required', false);
				$('#pilih-bulan').prop('required', false);
				$('#dptahun').prop('required', false);
			} else if (i == 'bulan') {
				$('#pilih-bulan').prop('required', true);
				$('#pilih-bulan').removeClass('d-none');
				$('#pilih-tanggal').addClass('d-none');
				$('#rentang-hari').addClass('d-none');
				$('#pilih-tahun').addClass('d-none');
				$('.sub_bln').removeClass('d-none');
				$('.sub_tgl').addClass('d-none');
				$('.sub_thn').addClass('d-none');
				$('#dpdari').prop('required', false);
				$('#dpsampai').prop('required', false);
				$('#dptanggal').prop('required', false);
				$('#dptahun').prop('required', false);
			} else if (i == 'tahun') {
				$('#dptahun').prop('required', true);
				$('#pilih-tahun').removeClass('d-none');
				$('#pilih-tanggal').addClass('d-none');
				$('#pilih-bulan').addClass('d-none');
				$('#rentang-hari').addClass('d-none');
				$('.sub_thn').removeClass('d-none');
				$('.sub_tgl').addClass('d-none');
				$('.sub_bln').addClass('d-none');
				$('#dpdari').prop('required', false);
				$('#dpsampai').prop('required', false);
				$('#dptanggal').prop('required', false);
				$('#pilih-bulan').prop('required', false);
			} 
			else {
				$('#dpdari').prop('required', true);
				$('#dpsampai').prop('required', true);
				$('#pilih-bulan').addClass('d-none');
				$('#pilih-tanggal').addClass('d-none');
				$('#pilih-tahun').addClass('d-none');
				$('#rentang-hari').removeClass('d-none');
				$('.sub_bln').addClass('d-none');
				$('.sub_tgl').addClass('d-none');
				$('.sub_thn').addClass('d-none');
				$('#pilih-bulan').prop('required', false);
				$('#dptanggal').prop('required', false);
				$('#dptahun').prop('required', false);
			}
		});

		function incrementValue() {
			var $input = $('#elev');
			var count = parseInt($input.val()) + 1;

			count = count > 100 ? 100 : count;
			$input.val(count);
			$input.change();
		}

		function decrementValue() {
			var $input = $('#elev');
			var count = parseInt($input.val()) - 1;
			count = count < 1 ? 0 : count;
			$input.val(count);
			$input.change();
		}

		$('.plus').mousedown(function() {
			interval = setInterval(incrementValue, 100);
		});

		$('.minus').mousedown(function() {
			interval = setInterval(decrementValue, 100);
		});

		$('.plus').mouseup(function() {
			clearInterval(interval);
		});
		$('.minus').mouseup(function() {
			clearInterval(interval);
		});

		$('#elev').change(function() {
			var elev = $(this).val();
			var s = 100 - elev;
			console.log(s);
			$(".middle").css({
				"top": "calc(100% - " + elev + "%)",
				"transform": "translateY(calc(0% - " + s + "%))"
			});
			$(".counter").text(elev);
		});
		$('#elev-sama').click(function() {
			$("#persentase").toggle(this.checked);
		});
		$('.kontrol').click(function() {
			var nm_pos = $('#select-pos').val();
			var nm_pintu = $('#select-pos2').val();
			var nm_metode = $('#pilih-metode').val();

			$('#nm_pos').text(nm_pos.split("_")[1]);
			$('#nm_pintu').text(nm_pintu.split("_")[1]);
			$('#nm_metode').text(nm_metode);
			if (nm_metode == 'Persentase') {
				var nm_nilai = $('#elev').val();
				$('#nm_nilai').text(nm_nilai + ' %');
			} else {
				var nm_nilai = $('#pilih-durasi').val();
				$('#nm_nilai').text(nm_nilai + ' Detik');
			}

			// console.log($('#kontrol-persen').val());
			$('#tes').modal('show');
		});
	});
</script>
<script>
	// @formatter:off
	document.addEventListener("DOMContentLoaded", function() {
		var el;
		window.TomSelect && (new TomSelect(el = document.getElementById('select-parameter'), {
			copyClassesToDropdown: false,
			dropdownClass: 'dropdown-menu ts-dropdown',
			optionClass: 'dropdown-item',
			controlInput: '<input>',
			render: {
				item: function(data, escape) {
					if (data.customProperties) {
						return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
					}
					return '<div>' + escape(data.text) + '</div>';
				},
				option: function(data, escape) {
					if (data.customProperties) {
						return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
					}
					return '<div>' + escape(data.text) + '</div>';
				},
			},
		}));
	});
	// @formatter:on
</script>
<script type="text/javascript" src="https://unpkg.com/xlsx@0.15.1/dist/xlsx.full.min.js"></script>
<script>
	function ExportToExcel(type, fn, dl) {
		var elt = document.getElementById('tbl_exporttable_to_xls');
		var wb = XLSX.utils.table_to_book(elt, {
			sheet: "sheet1"
		});
		return dl ?
			XLSX.write(wb, {
			bookType: type,
			bookSST: true,
			type: 'base64'
		}) :
		XLSX.writeFile(wb, fn || ('<?= $namafile ?>.' + (type || 'xlsx')));
	}
</script>
<script src="<?= base_url() ?>js/yearpicker.js"></script>
<script>
	$(".yearpicker").yearpicker();
</script>
<script type="text/javascript">

	Highcharts.chart('analisa', {
		chart: {
			zoomType: 'xy',
			borderWidth: 1.5,
			backgroundColor: '#FEFEFE',
			borderRadius: 3,
			borderColor: '#304C81'
		},

		title: {
			text: "<?php echo $namasensor ?> <?php echo $title ?>"
		},
		subtitle: {
			text: '<?php echo $this->session->userdata('namalokasi') ?>'
		},
		xAxis: [{
			type: 'datetime',
			dateTimeLabelFormats: { // don't display the dummy year
				millisecond: '%H:%M',
				second: '%H:%M',
				minute: '%H:%M',
				hour: '%H:%M',
				day: '%e. %b %y',
				week: '%e. %b %y',
				month: '%b \'%y',
				year: '%Y'

			},
			crosshair: true
		}],
		yAxis: [{ // Secondary yAxis

			tickAmount: 5,

			title: {
				text: "<?php echo $namasensor ?>",
				style: {
					color: Highcharts.getOptions().colors[1]
				}
			},
			labels: {
				format: "{value} <?php echo $satuan ?>",

				style: {
					color: Highcharts.getOptions().colors[1]
				}
			}

		}],
		tooltip: {
			xDateFormat: '<?php echo $tooltip ?>',
			shared: true
		},
		credits: {
			enabled: false
		},
		exporting: {
			buttons: {
				contextButton: {
					menuItems: ['printChart', 'separator', 'downloadPNG', 'downloadJPEG', 'downloadXLS']
				}
			},
			showTable: false
		},

		navigation: {
			buttonOptions: {
				enabled: false
			}
		},

		series: [{
			name: '<?php echo $namasensor; ?>',
			type: '<?php echo $typegraf; ?>',
			data: <?php echo str_replace('"', '', json_encode($data)); ?>,
			zIndex: 1,
			marker: {
			fillColor: 'white',
			lineWidth: 2,
			lineColor: Highcharts.getOptions().colors[0]
	},
					 tooltip: {
					 valueSuffix: ' <?php echo $satuan; ?>',
					 valueDecimals: 2,
					 },

	}
		<?php if ($typegraf != 'column') {
	echo ", 
				{
					name: 'Range',
					data: " . str_replace('"', '', json_encode($range)) . ",
					type: 'areasplinerange',
					lineWidth: 0,
					linkedTo: ':previous',
					color: Highcharts.getOptions().colors[0],
					fillOpacity: 0.3,
					zIndex: 0,
					marker: {
						enabled: false
					},
					tooltip: {
							valueSuffix: ' " . $satuan . "',
								valueDecimals: 3,
						}
					}";
} ?>
	],

		responsive: {
			rules: [{
				condition: {
					maxWidth: 500
				},
				chartOptions: {
					legend: {
						layout: 'horizontal',
						align: 'center',
						verticalAlign: 'bottom'
					}
				}
			}]
		}

	});
</script>
<script type="text/javascript" src="https://unpkg.com/xlsx@0.15.1/dist/xlsx.full.min.js"></script>
<script>
	function ExportToExcel(type, fn, dl) {
		var elt = document.getElementById('tbl_exporttable_to_xls');
		var wb = XLSX.utils.table_to_book(elt, {
			sheet: "sheet1"
		});
		return dl ?
			XLSX.write(wb, {
			bookType: type,
			bookSST: true,
			type: 'base64'
		}) :
		XLSX.writeFile(wb, fn || ('<?= $namafile ?>.' + (type || 'xlsx')));
	}
</script>