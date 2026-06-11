
<style>
	#example_length{
		margin-bottom:15px
	}
</style>
<div class="container-md">
	<div class="page-header d-print-none">
		<div class="row g-3 align-items-center">
			<div class="col-auto">

				<?php
				echo anchor('awgc/analisa', '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-big-left-lines" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
			<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
			<path d="M12 15v3.586a1 1 0 0 1 -1.707 .707l-6.586 -6.586a1 1 0 0 1 0 -1.414l6.586 -6.586a1 1 0 0 1 1.707 .707v3.586h3v6h-3z"></path>
			<path d="M21 15v-6"></path>
			<path d="M18 15v-6"></path>
		</svg>
') ?>

			</div>
			<div class="col col-md-auto">
				<h2 class="page-title mb-1">
					<?php echo $this->session->userdata('namalokasi')?>

				</h2>
			</div>
		</div>
	</div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<div class="page-body mt-3">
	<div class="container-xl">
		<div class="row msn_row">
			<div class="col-md-12">
				<div class="card">
					<div class="card-header py-3">
						<h3 class="mb-0">History Kontrol</h3>
					</div>
					<div class="card-body">
						<div class="table-responsive">
							<table id="example" class="table table-striped table-bordered border" style="width:100%">
								<thead>
									<tr>
										<th width="10px">No</th>
										<th>Nama Pintu</th>
										<th>Metode</th>
										<th>Waktu</th>
										<th>Dari</th>
										<th>Ke</th>
									</tr>
								</thead>
								<tbody>
									<?php 
					$i = 1;
				foreach($log as $key => $v){ ?>
									<tr>
										<th class="fw-normal text-center"><?= $i++ ?></th>
										<td><?= $v['nama_pintu'] ?></td>
										<td><?=  ($v['sistem'] == 1) ? 'Buka' : 'Tutup'   ?></td>
										<td><?= $v['datetime'] ?></td>
										<td><?= $v['dari'] ?> cm</td>
										<td><?= $v['ke'] ?> cm</td>
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
<script type="text/javascript">
	new DataTable('#example');
</script>