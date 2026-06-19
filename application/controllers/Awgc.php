<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Awgc extends CI_Controller
{

	function __construct()
	{
		parent::__construct();

		$this->load->library('csvimport');
		$this->load->model('m_awlr');
		if (!$this->session->userdata('logged_in')) {
			redirect('login');
		}
		
	}
	
	// === Fungsi debit (lookup tabel rating "ele.v (1).xlsx") ===
	// Implementasi tunggal: application/helpers/debit_helper.php (autoload).
	// Parameter = TMA BENDUNG dalam meter (sensor1 logger 10349).
	// Aturan: TMA bendung 0 => semua debit floodway = 0.

	function debitPintu1($tma_bendung) {
		$b = debit_floodway_bukaan();
		return debit_pintu1($tma_bendung, $b[1]);
	}

	function debitPintu2($tma_bendung) {
		$b = debit_floodway_bukaan();
		return debit_pintu2($tma_bendung, $b[2]);
	}

	function debitPintu3($tma_bendung) {
		$b = debit_floodway_bukaan();
		return debit_pintu3($tma_bendung, $b[3]);
	}

	function debitGabungan($tma_bendung) {
		$b = debit_floodway_bukaan();
		return debit_gabungan($tma_bendung, $b[1], $b[2], $b[3]);
	}

	function debitScouring($tma_bendung) {
		return debit_scouring($tma_bendung);
	}

	function debitFloodwayGabungan($tma_bendung) {
		$b = debit_floodway_bukaan();
		return debit_floodway_gabungan($tma_bendung, $b[1], $b[2], $b[3]);
	}

	public function submit_kalibrasi()
	{
		$this->load->library('PhpMQTT');
		$id_logger = $this->input->post('id_logger');
		$tma = (float)$this->input->post('kalibrasi_tma');

		if (!$id_logger) {
			echo json_encode([
				'status' => 'error',
				'message' => 'ID Logger tidak valid'
			]);
			return;
		}
		// TODO(GCM): Kalibrasi TMA BELUM dimigrasi ke format GCM baru.
		// Firmware kini memakai topik per-logger (sub_<id_logger>) dengan payload
		// command GCM; envelope "set_<id>"/"setting":"tma" ke topik AWGC_Garut_Copong
		// di bawah kemungkinan TIDAK lagi didengar firmware. Tunggu definisi format
		// TMA baru sebelum mengubah ini. Lihat docs/superpowers/specs/2026-06-13-migrasi-gcm-kontrol-pintu-design.md
		$payload = [
			"set_$id_logger" => [
				"command" => "set",
				"setting" => "tma",
				"data" => (string)$tma
			]
		];

		$server = 'mqtt.beacontelemetry.com';
		$port = 8883;
		$username = 'userlog';
		$password = 'b34c0n';
		$client_id = 'bemqtt-kalibrasi';
		$ca = "/etc/ssl/certs/ca-bundle.crt";
		$mqtt = new phpMQTT($server, $port, $client_id, $ca);
		if ($mqtt->connect(true, NULL, $username, $password)) {
			$mqtt->publish('AWGC_Garut_Copong', json_encode($payload), 0);
			$mqtt->close();
		} else {
			echo "Time out!\n";
		}
		
		echo json_encode([
			'status' => 'success',
			'message' => 'Kalibrasi berhasil disimpan'
		]);
	}

	

	### Dari Beranda ##########
	function set_sensordash()
	{
		$tabel = $this->input->get('tabel');
		$idparam = $this->input->get('id_param');
		$this->session->set_userdata('id_param', $this->input->get('id_param'));
		$this->session->set_userdata('tabel', $tabel);
		$tgl = date('Y-m-d');
		$this->session->set_userdata('pada', $tgl);
		$this->session->set_userdata('data', 'hari');
		$this->session->set_userdata('tanggal', $tgl);
		$jenis = $this->input->get('jenis');
		$id_logger = $this->input->get('id_logger');
		if ($jenis == 'pintu') {
			$q_parameter = $this->db->query("SELECT * FROM parameter_pintu where id_param='" . $idparam . "'");
			if ($q_parameter->num_rows() > 0) {
				$parameter = $q_parameter->row();
				//data hasil seleksi dimasukkan ke dalam $session
				$session = array(
					'idparameter' => $parameter->id_param,
					'nama_parameter' => $parameter->nama_parameter,
					'kolom' => $parameter->kolom_sensor,
					'satuan' => $parameter->satuan,
					'tipe_grafik' => 'spline',
				);
				//data dari $session akhirnya dimasukkan ke dalam session
				$this->session->set_userdata($session);
			}
		} else {
			if($idparam){
				$q_parameter = $this->db->query("SELECT * FROM parameter_sensor where id_param='" . $idparam . "'");
			}else{
				$q_parameter = $this->db->query("SELECT * FROM parameter_sensor limit 1");
			}
			if ($q_parameter->num_rows() > 0) {
				$parameter = $q_parameter->row();
				//data hasil seleksi dimasukkan ke dalam $session
				$session = array(
					'idparameter' => $parameter->id_param,
					'nama_parameter' => $parameter->nama_parameter,
					'kolom' => $parameter->kolom_sensor,
					'satuan' => $parameter->satuan,
					'tipe_grafik' => $parameter->tipe_graf,
				);
				//data dari $session akhirnya dimasukkan ke dalam session
				$this->session->set_userdata($session);
			}
		}

		$querylogger = $this->db->query('select * from t_logger INNER JOIN t_lokasi ON t_logger.lokasi_logger=t_lokasi.idlokasi where id_logger="' . $id_logger . '";');
		$log = $querylogger->row();
		$lokasilog = $log->nama_lokasi;
		$this->session->set_userdata('namalokasi', $lokasilog);
		$this->session->set_userdata('idlogger', $id_logger);
		$this->session->set_userdata('jenis', $jenis);
		$id_pintu = $this->input->get('id_pintu');
		if (!$id_pintu) {
			$id_pintu = $this->db->where('id_logger', $id_logger)->limit(1)->get('t_pintu')->row()->id_pintu;
		}
		$this->session->set_userdata('id_pintu', $id_pintu);
		redirect('awgc/analisa');
	}

	############################################

	### Set Pos #####
	public function pilihposawgc()
	{
		$data = array();
		$bidang = $this->session->userdata['bidang'];
		$q_pos = $this->db->query("SELECT * FROM t_logger INNER JOIN t_lokasi ON t_logger.lokasi_logger = t_lokasi.idlokasi where kategori_log='1'");

		foreach ($q_pos->result() as $pos) {
			$data[] = array(
				'idLogger' => $pos->id_logger, 'namaPos' => $pos->nama_lokasi
			);
		}

		$data_pos = json_encode($data);
		return json_decode($data_pos);
	}

	public function temp_ajax(){
		$q ='';
		$id_logger = $this->input->get('id_logger');
		$level_gate = $this->db->where('id_logger', $id_logger)->get('t_pintu')->result_array();
		$data_temp = $this->db->where('code_logger', $id_logger)->get('temp_awgc')->row();

		foreach ($level_gate as $key => $vl) {
			$sensor_level = $vl['sensor_level'];
			$status_controller = $vl['status_controller'];
			$r = $vl['r'];
			$s = $vl['s'];
			$t = $vl['t'];
			$nilai = $data_temp->$sensor_level;
			$status_controller = $data_temp->$status_controller;	
			if($status_controller == '0' or $data_temp->$t == '0' or $data_temp->$s == '0' or $data_temp->$r == '0'){
				$text = 'text-secondary';
				$sts = ' - Off';
			}else{
				$text = '';
				$sts = '';
			}
			$q .= '<div class="col-lg-6 mb-3"><div class="card"><div class="card-body py-3 text-center"><h4 class="mb-1 fw-normal '. $text.'">' .	$vl["nama_pintu"] . $sts.'</h4><h2 class="mb-0 fw-bold '. $text.'">'. $nilai .' cm</h2></div></div></div>';
		}

		echo json_encode(array('panel'=>$q));
	}

	public function temp_ajax2(){
		$q ='';
		$id_logger = $this->input->get('id_logger');
		$level_gate = $this->db->where('id_logger', $id_logger)->get('t_pintu')->result_array();
		$data_temp = $this->db->where('code_logger', $id_logger)->get('temp_awgc')->row();

		foreach ($level_gate as $key => $vl) {
			$sensor_level = $vl['sensor_level'];
			$status_controller = $vl['status_controller'];
			$batas_atas = $vl['batas_atas'];
			$r = $vl['r'];
			$s = $vl['s'];
			$t = $vl['t'];
			$nilai = $data_temp->$sensor_level *$batas_atas/100;
			$status_controller = $data_temp->$status_controller;	
			if($status_controller == '0' or $data_temp->$t == '0' or $data_temp->$s == '0' or $data_temp->$r == '0'){
				$text = 'text-secondary';
				$sts = ' - Off';
			}else{
				$text = '';
				$sts = '';
			}
			$q .= '<div class="col-lg-6 mb-3"><div class="card"><div class="card-body py-3 text-center"><h4 class="mb-1 fw-normal '. $text.'">' .	$vl["nama_pintu"] . $sts.'</h4><h2 class="mb-0 fw-bold '. $text.'">'. $nilai .' cm</h2></div></div></div>';
		}

		echo json_encode(array('panel'=>$q));
	}

	function set_pos()
	{
		$idlog = $this->input->post('pilihpos');
		$querylogger = $this->db->query('select * from t_logger INNER JOIN t_lokasi ON t_logger.lokasi_logger=t_lokasi.idlokasi where id_logger="' . $idlog . '";');
		$log = $querylogger->row();
		$lokasilog = $log->nama_lokasi;
		$id_logger = $log->id_logger;
		$this->session->set_userdata('namalokasi', $lokasilog);
		$this->session->set_userdata('id_logger', $id_logger);

		$q_parameter = $this->db->query("SELECT * FROM parameter_sensor where logger_id='" . $idlog . "' order by id_param limit 1");
		if ($q_parameter->num_rows() > 0) {
			$parameter = $q_parameter->row();
			//data hasil seleksi dimasukkan ke dalam $session
			$session = array(
				'idlogger' => $parameter->logger_id,
				'idparameter' => $parameter->id_param,
				'nama_parameter' => $parameter->nama_parameter,
				'kolom' => $parameter->kolom_sensor,
				'satuan' => $parameter->satuan,
				'tipe_grafik' => $parameter->tipe_graf,
				'kolom_acuan' => $parameter->kolom_acuan
			);
			$this->session->set_userdata('id_param', $parameter->id_param);
			//data dari $session akhirnya dimasukkan ke dalam session
			$this->session->set_userdata($session);
		}

		redirect('awgc/analisa');
	}

	function set_pintu()
	{
		$id_pintu = explode("_", $this->input->post('id_pintu'))[0];
		$id_param = $this->db->where('id_pintu', $id_pintu)->limit(1)->get('parameter_pintu')->row()->id_param;
		$tgl = date('Y-m-d');
		$this->session->set_userdata('pada', $tgl);
		$this->session->set_userdata('data', 'hari');
		$this->session->set_userdata('tanggal', $tgl);
		$jenis = $this->session->set_userdata('jenis', 'pintu');

		// if ($jenis == 'pintu') {
		$q_parameter = $this->db->query("SELECT * FROM parameter_pintu where id_param='" . $id_param . "'");
		if ($q_parameter->num_rows() > 0) {
			$parameter = $q_parameter->row();
			//data hasil seleksi dimasukkan ke dalam $session
			$session = array(
				'idparameter' => $parameter->id_param,
				'nama_parameter' => $parameter->nama_parameter,
				'kolom' => $parameter->kolom_sensor,
				'satuan' => $parameter->satuan,
				'tipe_grafik' => 'spline',
			);
			//data dari $session akhirnya dimasukkan ke dalam session
			$this->session->set_userdata($session);
		}
		$id_logger = $this->session->userdata('idlogger');
		$querylogger = $this->db->query('select * from t_logger INNER JOIN t_lokasi ON t_logger.lokasi_logger=t_lokasi.idlokasi where id_logger="' . $id_logger . '";');
		$log = $querylogger->row();
		$lokasilog = $log->nama_lokasi;
		$this->session->set_userdata('namalokasi', $lokasilog);

		if (!$id_pintu) {
			$id_pintu = $this->db->where('id_logger', $id_logger)->limit(1)->get('t_pintu')->row()->id_pintu;
		}
		$this->session->set_userdata('id_pintu', $id_pintu);
		redirect('awgc/analisa');
	}

	function ubah_analisa()
	{

		$id_parameter = $this->input->post('id_param');
		$sesi_data = $this->input->post('data');
		if ($sesi_data == 'hari') {
			$pada = $this->input->post('tanggal');
			$this->session->set_userdata('tanggal', $pada);
			$this->session->set_userdata('pada', $pada);
		} elseif ($sesi_data == 'bulan') {
			$bln = str_replace('/', '-', $this->input->post('bulan'));
			$this->session->set_userdata('bulan', $bln);
			$this->session->set_userdata('pada', $bln);
		}elseif ($sesi_data == 'tahun') {
			$thn = str_replace('/', '-', $this->input->post('tahun'));
			$this->session->set_userdata('tahun', $thn);
			$this->session->set_userdata('pada', $thn);
		}else{
			$dari = str_replace('/', '-', $this->input->post('dari'));
			$sampai = str_replace('/', '-', $this->input->post('sampai'));
			$this->session->set_userdata('dari', $dari);
			$this->session->set_userdata('sampai', $sampai);
		}
		$this->session->set_userdata('data', $sesi_data);
		$idparam = (explode("_", $id_parameter))[0];
		$jenis = (explode("_", $id_parameter))[1];
		if ($jenis == 'pintu') {
			$q_parameter = $this->db->query("SELECT * FROM parameter_pintu where id_param='" . $idparam . "'");
			if ($q_parameter->num_rows() > 0) {
				$parameter = $q_parameter->row();
				//data hasil seleksi dimasukkan ke dalam $session
				$session = array(
					'idparameter' => $parameter->id_param,
					'nama_parameter' => $parameter->nama_parameter,
					'kolom' => $parameter->kolom_sensor,
					'satuan' => $parameter->satuan,
					'tipe_grafik' => 'spline',
				);
				//data dari $session akhirnya dimasukkan ke dalam session
				$this->session->set_userdata($session);
			}
		} else {
			$q_parameter = $this->db->query("SELECT * FROM parameter_sensor where id_param='" . $idparam . "'");
			if ($q_parameter->num_rows() > 0) {
				$parameter = $q_parameter->row();
				//data hasil seleksi dimasukkan ke dalam $session
				$session = array(
					'idparameter' => $parameter->id_param,
					'nama_parameter' => $parameter->nama_parameter,
					'kolom' => $parameter->kolom_sensor,
					'satuan' => $parameter->satuan,
					'tipe_grafik' => $parameter->tipe_graf,
				);
				//data dari $session akhirnya dimasukkan ke dalam session
				$this->session->set_userdata($session);
			}
		}
		redirect('awgc/analisa');
	}

	function ubah_analisa_multi()
	{
		$id_pintu = $this->input->post('id_pintu');
		$id_parameter = $this->input->post('id_param');
		$sesi_data = $this->input->post('data');

		if ($sesi_data == 'hari') {
			$pada = $this->input->post('tanggal');
			$this->session->set_userdata('tanggal', $pada);
			$this->session->set_userdata('pada', $pada);
		} elseif ($sesi_data == 'bulan') {
			$bln = str_replace('/', '-', $this->input->post('bulan'));
			$this->session->set_userdata('bulan', $bln);
			$this->session->set_userdata('pada', $bln);
		}elseif ($sesi_data == 'tahun') {
			$thn = str_replace('/', '-', $this->input->post('tahun'));
			$this->session->set_userdata('tahun', $thn);
			$this->session->set_userdata('pada', $thn);
		}else{
			$dari = str_replace('/', '-', $this->input->post('dari'));
			$sampai = str_replace('/', '-', $this->input->post('sampai'));
			$this->session->set_userdata('dari', $dari);
			$this->session->set_userdata('sampai', $sampai);
		}
		$this->session->set_userdata('data', $sesi_data);
		redirect('awgc/multiview');
	}


	function kontrol_pintu()
	{
		$idlogger = $this->input->get('idlogger');
		$id_pintu = $this->db->where('id_logger', $idlogger)->limit(1)->get('t_pintu')->row()->id_pintu;
		$querylogger = $this->db->query('select * from t_logger INNER JOIN t_lokasi ON t_logger.lokasi_logger=t_lokasi.idlokasi where id_logger="' . $idlogger . '";');
		$log = $querylogger->row();
		$lokasilog = $log->nama_lokasi;
		$this->session->set_userdata('namalokasi', $lokasilog);
		$this->session->set_userdata('namalokasi', $lokasilog);
		$this->session->set_userdata('idlogger', $idlogger);
		$this->session->set_userdata('id_pintu', $id_pintu);
		redirect('awgc/kontrol');
	}

	function kontrol2()
	{
		if ($this->session->userdata('logged_in')) {
			$data = array();
			$temp_data = $this->db->where('code_logger', $this->session->userdata('idlogger'))->get('temp_awgc')->row();
			$awal = date('Y-m-d H:i', (mktime(date('H') - 1)));
			$data['waktuterakhir'] = $temp_data->waktu;
			if ($temp_data->waktu >= $awal) {
				$data['color'] = "green";
				$data['status']= 'On';
				$data['status_logger'] = "Koneksi Terhubung";
			} else {
				$data['color'] = "dark";
				$data['status']= 'Off';
				$data['status_logger'] = "Koneksi Terputus";
			}
			$id_pintu = $this->session->userdata('id_pintu');
			$data_pintu = $this->db->where('id_pintu', $id_pintu)->get('t_pintu')->row();
			$sn_pintu = $data_pintu->sensor_level;
			$param_analisa = $this->db->where('logger_id', $this->session->userdata('idlogger'))->where('analisa', '1')->get('parameter_sensor')->result_array();

			$pr_pintu = $this->db->where('id_pintu', $id_pintu)->get('parameter_pintu')->result_array();
			$gb = array_merge($param_analisa, $pr_pintu);
			$param_pintu[] = [
				'nama_parameter' => 'ID Pintu',
				'nilai' => $id_pintu,
				'satuan' => '',
			];
			foreach ($gb as $key => $pr) {
				$sn_pr = $pr['kolom_sensor'];
				$param_pintu[] = [
					'nama_parameter' => $pr['nama_parameter'],
					'nilai' => $temp_data->$sn_pr,
					'satuan' => $pr['satuan']
				];
			}

			$data['pilih_pos'] = $this->pilihposawgc();
			$data['kode_akses'] = $this->db->where('id_user',$this->session->userdata('id_user'))->get('kode_akses')->row();
			$data['konten'] = 'konten/back/awgc/kontrol_awgc';
			$list_pintu = $this->db->where('id_logger', $this->session->userdata('idlogger'))->get('t_pintu')->result_array();
			foreach ($list_pintu as $key => $v) {
				$sensor = $v['sensor_level'];
				$list_pintu[$key]['elevasi'] = $temp_data->$sensor;
				$phase_r = $v['r'];
				$phase_s = $v['s'];
				$phase_t = $v['t'];
				$status_controller = $v['status_controller'];
				$list_pintu[$key]['r'] = $temp_data->$phase_r;
				$list_pintu[$key]['s'] = $temp_data->$phase_s;
				$list_pintu[$key]['t'] = $temp_data->$phase_t;
				if($temp_data->$phase_t == '0' or $temp_data->$phase_r  == '0' or $temp_data->$phase_s == '0'){
					$list_pintu[$key]['status_rst'] = '0';
				}else{
					$list_pintu[$key]['status_rst'] = '1';
				}
				$list_pintu[$key]['status_controller'] = $temp_data->$status_controller;
			}
			$data['pintu'] = [
				'list_pintu' => $list_pintu,
				'id_pintu' => $id_pintu,
				'nama_pintu' => $data_pintu->nama_pintu,
				'elevasi' => $temp_data->$sn_pintu,
				'param_temp' => $param_pintu
			];
			$data['status_kontrol'] = $this->db->where('id_logger',$this->session->userdata('idlogger'))->get('status_kontrol')->row();
			$data['log'] = $this->db->limit(10)->get('log_kontrol')->result_array();

			$this->load->view('template_admin/site', $data);
		} else {
			redirect('login');
		}
	}


	function kontrol()
	{
		if ($this->session->userdata('logged_in')) {
			$data = array();
			$temp_data = $this->db->where('code_logger', $this->session->userdata('idlogger'))->get('temp_awgc')->row();
			$awal = date('Y-m-d H:i', (mktime(date('H') - 1)));
			$data['waktuterakhir'] = $temp_data->waktu;
			if ($temp_data->waktu >= $awal) {
				$data['color'] = "green";
				$data['status']= 'On';
				$data['status_logger'] = "Koneksi Terhubung";
			} else {
				$data['color'] = "dark";
				$data['status']= 'Off';
				$data['status_logger'] = "Koneksi Terputus";
			}
			$id_pintu = $this->session->userdata('id_pintu');
			$data_pintu = $this->db->where('id_pintu', $id_pintu)->get('t_pintu')->row();
			$sn_pintu = $data_pintu->sensor_level;
			$param_analisa = $this->db->where('logger_id', $this->session->userdata('idlogger'))->where('analisa', '1')->get('parameter_sensor')->result_array();

			$pr_pintu = $this->db->where('id_pintu', $id_pintu)->get('parameter_pintu')->result_array();
			$gb = array_merge($param_analisa, $pr_pintu);
			$param_pintu[] = [
				'nama_parameter' => 'ID Pintu',
				'nilai' => $id_pintu,
				'satuan' => '',
			];
			foreach ($gb as $key => $pr) {
				$sn_pr = $pr['kolom_sensor'];
				$param_pintu[] = [
					'nama_parameter' => $pr['nama_parameter'],
					'nilai' => $temp_data->$sn_pr,
					'satuan' => $pr['satuan']
				];
			}

			$data['pilih_pos'] = $this->pilihposawgc();
			$data['kode_akses'] = $this->db->where('id_user',$this->session->userdata('id_user'))->get('kode_akses')->row();
			$data['konten'] = 'konten/back/awgc/kontrol_awgc3';
			$list_pintu = $this->db->where('id_logger', $this->session->userdata('idlogger'))->get('t_pintu')->result_array();
			foreach ($list_pintu as $key => $v) {
				$sensor = $v['sensor_level'];
				$list_pintu[$key]['elevasi'] = $temp_data->$sensor;
				$list_pintu[$key]['satuan'] = $v['satuan_level'];

				$phase_r = $v['r'];
				$phase_s = $v['s'];
				$phase_t = $v['t'];
				$status_controller = $v['status_controller'];
				$list_pintu[$key]['r'] = $temp_data->$phase_r;
				//$list_pintu[$key]['batas_atas'] = $v['batas_atas'];
				$list_pintu[$key]['s'] = $temp_data->$phase_s;
				$list_pintu[$key]['t'] = $temp_data->$phase_t;
				if($temp_data->$phase_t == '0' or $temp_data->$phase_r  == '0' or $temp_data->$phase_s == '0'){
					$list_pintu[$key]['status_rst'] = '0';
				}else{
					$list_pintu[$key]['status_rst'] = '1';
				}
				$list_pintu[$key]['status_controller'] = $temp_data->$status_controller;
			}

			$data['pintu'] = [
				'list_pintu' => $list_pintu,
				'id_pintu' => $id_pintu,
				'nama_pintu' => $data_pintu->nama_pintu,
				'elevasi' => $temp_data->$sn_pintu,
				'param_temp' => $param_pintu
			];
			$data['status_kontrol'] = $this->db->where('id_logger',$this->session->userdata('idlogger'))->get('status_kontrol')->row();
			$data['log'] = $this->db->limit(10)->get('log_kontrol')->result_array();

			$this->load->view('template_admin/site', $data);
		} else {
			redirect('login');
		}
	}

	function analisa()
	{
		if ($this->session->userdata('logged_in')) {
			$data = array();
			$data_tabel = array();
			$prs = [];
			$prs2 = [];
			$range = array();
			$temp_data = $this->db->where('code_logger', $this->session->userdata('idlogger'))->get('temp_awgc')->row();

			$param_analisa = $this->db->where('logger_id', $this->session->userdata('idlogger'))->where('analisa', '1')->get('parameter_sensor')->result_array();

			$param_log = $this->db->where('logger_id', $this->session->userdata('idlogger'))->get('parameter_sensor')->result_array();
			foreach ($param_log as $key => $pa) {
				$prs[] = [
					'id_param' => $pa['id_param'],
					'nama_parameter' => $pa['nama_parameter'],
					'satuan' => $pa['satuan'],
					'jenis' => 'analisa',
					'jns'=>''
				];
			}
			$jenis_pintu = $this->db->select('id_pintu')->where('id_logger',$this->session->userdata('idlogger'))->get('t_pintu')->result_array();
			$id_list = array_column($jenis_pintu, 'id_pintu');

			$pr_pintu = $this->db
				->where_in('id_pintu', $id_list)
				->where('analisa', '1')
				->get('parameter_pintu')
				->result_array();
			$sekunder = [];
			foreach ($pr_pintu as $key => $pz) {
				$prs2[] = [
					'id_param' => $pz['id_param'],
					'nama_parameter' => $pz['nama_parameter'],
					'kolom' => $pz['kolom_sensor'],
					'satuan' => $pz['satuan'],
					'jenis' => 'pintu',
				];
			}
			$param_all = array_merge($prs2,$prs);
			$gb = array_merge($param_analisa, $pr_pintu);
			foreach ($gb as $key => $pr) {
				$sn_pr = $pr['kolom_sensor'];
				$param_pintu[] = [
					'nama_parameter' => $pr['nama_parameter'],
					'nilai' => $temp_data->$sn_pr,
					'satuan' => $pr['satuan']
				];
			}
			
			####################################################################################### HARI ##################
			if ($this->session->userdata('data') == 'hari') {
				$sensor = $this->session->userdata('kolom');
				$nama_sensor = "Rerata_" . $this->session->userdata('nama_parameter');

				$kolom = $this->session->userdata('kolom');

				$select = 'avg(' . $kolom . ') as ' . $nama_sensor;
				$satuan = $this->session->userdata('satuan');

				$query_data = $this->db->query("SELECT waktu, HOUR(waktu) as jam,DAY(waktu) as hari,MONTH(waktu) as bulan,YEAR(waktu) as tahun," . $select . ",min(" . $kolom . ") as min,max(" . $kolom . ") as max  FROM " . $this->session->userdata('tabel') . " where code_logger='" . $this->session->userdata('idlogger') . "' and waktu >= '" . $this->session->userdata('pada') . " 00:00' and waktu <= '" . $this->session->userdata('pada') . " 23:59' group by HOUR(waktu),DAY(waktu),MONTH(waktu),YEAR(waktu) order by waktu asc;");

				foreach ($query_data->result() as $datalog) {
					$nilai_avg = $datalog->$nama_sensor;
					$nilai_max = $datalog->max;
					$nilai_min = $datalog->min;
					
					if($this->session->userdata('nama_parameter') == 'Q_Floodway_1' and $this->session->userdata('idlogger') == '10349'){
						$nilai_avg = $this->debitPintu1($nilai_avg);
						$nilai_max = $this->debitPintu1($nilai_max);
						$nilai_min = $this->debitPintu1($nilai_min);
					}elseif($this->session->userdata('nama_parameter') == 'Q_Floodway_2' and $this->session->userdata('idlogger') == '10349'){
						$nilai_avg = $this->debitPintu2($nilai_avg);
						$nilai_max = $this->debitPintu2($nilai_max);
						$nilai_min = $this->debitPintu2($nilai_min);
					}elseif($this->session->userdata('nama_parameter') == 'Q_Floodway_3' and $this->session->userdata('idlogger') == '10349'){
						$nilai_avg = $this->debitPintu3($nilai_avg);
						$nilai_max = $this->debitPintu3($nilai_max);
						$nilai_min = $this->debitPintu3($nilai_min);
					}elseif($this->session->userdata('nama_parameter') == 'Q_Floodway_Gabungan' and $this->session->userdata('idlogger') == '10349'){
						$nilai_avg = $this->debitFloodwayGabungan($nilai_avg);
						$nilai_max = $this->debitFloodwayGabungan($nilai_max);
						$nilai_min = $this->debitFloodwayGabungan($nilai_min);
					}elseif($this->session->userdata('nama_parameter') == 'Debit_Gabungan' and $this->session->userdata('idlogger') == '10349'){
						$nilai_avg = $this->debitGabungan($nilai_avg);
						$nilai_max = $this->debitGabungan($nilai_max);
						$nilai_min = $this->debitGabungan($nilai_min);
					}elseif($this->session->userdata('nama_parameter') == 'Q_Scouring' and $this->session->userdata('idlogger') == '10349'){
						$nilai_avg = $this->debitScouring($nilai_avg);
						$nilai_max = $this->debitScouring($nilai_max);
						$nilai_min = $this->debitScouring($nilai_min);
					}


					$data[] = "[ Date.UTC(" . $datalog->tahun . "," . $datalog->bulan . "-1," . $datalog->hari . "," . $datalog->jam . ")," . number_format($nilai_avg, 3) . "]";
					$range[] = "[ Date.UTC(" . $datalog->tahun . "," . $datalog->bulan . "-1," . $datalog->hari . "," . $datalog->jam . ")," . $nilai_min. "," . $nilai_max . "]";
					$data_tabel[] = array(
						'waktu' => date('Y-m-d H', strtotime($datalog->waktu)) . ':00:00',
						'dta' => number_format($nilai_avg, 2),
						'min' => number_format($nilai_min, 2),
						'max' => number_format($nilai_max, 2)
					);
				}
				$dataAnalisa = array(
					'idLogger' => $this->session->userdata('idlogger'),
					'namaSensor' => $nama_sensor,
					'satuan' => $satuan,
					'tipe_grafik' => $this->session->userdata('tipe_grafik'),
					'data' => $data,
					'data_tabel' => $data_tabel,
					'nosensor' => $kolom,
					'range' => $range,
					'tooltip' => "Waktu %d-%m-%Y %H:%M"
				);

				$dataparam = json_encode($dataAnalisa);
				$data['data_sensor'] = json_decode($dataparam);
			}

			####################################################################################### BULAN ##################
			elseif ($this->session->userdata('data') == 'bulan') {
				$sensor = $this->session->userdata('kolom');
				$nama_sensor = "Rerata_" . $this->session->userdata('nama_parameter');
				if ($sensor == 'debit') {
					$kolom = $this->session->userdata('kolom_acuan');
				} else {
					$kolom = $this->session->userdata('kolom');
				}
				$select = 'avg(' . $kolom . ') as ' . $nama_sensor;
				$satuan = $this->session->userdata('satuan');
				$query_data = $this->db->query("SELECT waktu, DATE(waktu) as tanggal, DAY(waktu) as hari,MONTH(waktu) as bulan,YEAR(waktu) as tahun," . $select . ",min(" . $kolom . ") as min,max(" . $kolom . ") as max  FROM " . $this->session->userdata('tabel') . " where code_logger='" . $this->session->userdata('idlogger') . "' and waktu >= '" . $this->session->userdata('pada') . "-01 00:00' and waktu <= '" . $this->session->userdata('pada') . "-31 23:59' group by DAY(waktu),MONTH(waktu),YEAR(waktu)  order by waktu asc;");
				foreach ($query_data->result() as $datalog) {
					$nilai_avg = $datalog->$nama_sensor;
					$nilai_max = $datalog->max;
					$nilai_min = $datalog->min;

					if($this->session->userdata('nama_parameter') == 'Q_Floodway_1' and $this->session->userdata('idlogger') == '10349'){
						$nilai_avg = $this->debitPintu1($nilai_avg);
						$nilai_max = $this->debitPintu1($nilai_max);
						$nilai_min = $this->debitPintu1($nilai_min);
					}elseif($this->session->userdata('nama_parameter') == 'Q_Floodway_2' and $this->session->userdata('idlogger') == '10349'){
						$nilai_avg = $this->debitPintu2($nilai_avg);
						$nilai_max = $this->debitPintu2($nilai_max);
						$nilai_min = $this->debitPintu2($nilai_min);
					}elseif($this->session->userdata('nama_parameter') == 'Q_Floodway_3' and $this->session->userdata('idlogger') == '10349'){
						$nilai_avg = $this->debitPintu3($nilai_avg);
						$nilai_max = $this->debitPintu3($nilai_max);
						$nilai_min = $this->debitPintu3($nilai_min);
					}elseif($this->session->userdata('nama_parameter') == 'Q_Floodway_Gabungan' and $this->session->userdata('idlogger') == '10349'){
						$nilai_avg = $this->debitFloodwayGabungan($nilai_avg);
						$nilai_max = $this->debitFloodwayGabungan($nilai_max);
						$nilai_min = $this->debitFloodwayGabungan($nilai_min);
					}elseif($this->session->userdata('nama_parameter') == 'Debit_Gabungan' and $this->session->userdata('idlogger') == '10349'){
						$nilai_avg = $this->debitGabungan($nilai_avg);
						$nilai_max = $this->debitGabungan($nilai_max);
						$nilai_min = $this->debitGabungan($nilai_min);
					}elseif($this->session->userdata('nama_parameter') == 'Q_Scouring' and $this->session->userdata('idlogger') == '10349'){
						$nilai_avg = $this->debitScouring($nilai_avg);
						$nilai_max = $this->debitScouring($nilai_max);
						$nilai_min = $this->debitScouring($nilai_min);
					}

					$data[] = "[ Date.UTC(" . $datalog->tahun . "," . $datalog->bulan . "-1," . $datalog->hari . ")," . number_format($nilai_avg, 3) . "]";
					$range[] = "[ Date.UTC(" . $datalog->tahun . "," . $datalog->bulan . "-1," . $datalog->hari . ")," . $nilai_min . "," . $nilai_max . "]";
					$data_tabel[] = array(
						'waktu' => date('Y-m-d', strtotime($datalog->waktu)),
						'dta' => number_format($nilai_avg, 2),
						'min' => number_format($nilai_min, 2),
						'max' => number_format($nilai_max, 2)
					);
					//$waktu[]= date('Y-m-d H',strtotime($datalog->waktu)).":00";

				}
				$dataAnalisa = array(
					'idLogger' => $this->session->userdata('idlogger'),
					'namaSensor' => $nama_sensor,
					'satuan' => $satuan,
					'tipe_grafik' => $this->session->userdata('tipe_grafik'),
					'data' => $data,
					'data_tabel' => $data_tabel,
					'nosensor' => $sensor,
					'range' => $range,
					'tooltip' => "Tanggal %d-%m-%Y"
				);
				$dataparam = json_encode($dataAnalisa);
				$data['data_sensor'] = json_decode($dataparam);
			}
			####################################################################################### TAHUN ##################
			elseif ($this->session->userdata('data') == 'tahun') {
				$sensor = $this->session->userdata('kolom');
				$nama_sensor = "Rerata_" . $this->session->userdata('nama_parameter');
				if ($sensor == 'debit') {
					$kolom = $this->session->userdata('kolom_acuan');
				} else {
					$kolom = $this->session->userdata('kolom');
				}
				$select = 'avg(' . $kolom . ') as ' . $nama_sensor;
				$satuan = $this->session->userdata('satuan');

				$query_data = $this->db->query("SELECT DATE(waktu) as tanggal,MONTH(waktu) as bulan,YEAR(waktu) as tahun," . $select . ",min(" . $kolom . ") as min,max(" . $kolom . ") as max  FROM " . $this->session->userdata('tabel') . " where code_logger='" . $this->session->userdata('idlogger') . "' and waktu >= '" . $this->session->userdata('pada') . "-01-01 00:00' and waktu <= '" . $this->session->userdata('pada') . "-12-31 23:59' group by MONTH(waktu),YEAR(waktu)  order by waktu asc;");
				foreach ($query_data->result() as $datalog) {
					$nilai_avg = $datalog->$nama_sensor;
					$nilai_max = $datalog->max;
					$nilai_min = $datalog->min;

					if($this->session->userdata('nama_parameter') == 'Q_Floodway_1' and $this->session->userdata('idlogger') == '10349'){
						$nilai_avg = $this->debitPintu1($nilai_avg);
						$nilai_max = $this->debitPintu1($nilai_max);
						$nilai_min = $this->debitPintu1($nilai_min);
					}elseif($this->session->userdata('nama_parameter') == 'Q_Floodway_2' and $this->session->userdata('idlogger') == '10349'){
						$nilai_avg = $this->debitPintu2($nilai_avg);
						$nilai_max = $this->debitPintu2($nilai_max);
						$nilai_min = $this->debitPintu2($nilai_min);
					}elseif($this->session->userdata('nama_parameter') == 'Q_Floodway_3' and $this->session->userdata('idlogger') == '10349'){
						$nilai_avg = $this->debitPintu3($nilai_avg);
						$nilai_max = $this->debitPintu3($nilai_max);
						$nilai_min = $this->debitPintu3($nilai_min);
					}elseif($this->session->userdata('nama_parameter') == 'Q_Floodway_Gabungan' and $this->session->userdata('idlogger') == '10349'){
						$nilai_avg = $this->debitFloodwayGabungan($nilai_avg);
						$nilai_max = $this->debitFloodwayGabungan($nilai_max);
						$nilai_min = $this->debitFloodwayGabungan($nilai_min);
					}elseif($this->session->userdata('nama_parameter') == 'Debit_Gabungan' and $this->session->userdata('idlogger') == '10349'){
						$nilai_avg = $this->debitGabungan($nilai_avg);
						$nilai_max = $this->debitGabungan($nilai_max);
						$nilai_min = $this->debitGabungan($nilai_min);
					}elseif($this->session->userdata('nama_parameter') == 'Q_Scouring' and $this->session->userdata('idlogger') == '10349'){
						$nilai_avg = $this->debitScouring($nilai_avg);
						$nilai_max = $this->debitScouring($nilai_max);
						$nilai_min = $this->debitScouring($nilai_min);
					}
					$data[] = "[ Date.UTC(" . $datalog->tahun . "," . $datalog->bulan . "-1)," . number_format($nilai_avg, 3) . "]";
					$range[] = "[ Date.UTC(" . $datalog->tahun . "," . $datalog->bulan . "-1)," . $nilai_min . "," . $nilai_max . "]";
					$data_tabel[] = array(
						'waktu' => date('Y-m', strtotime($datalog->tanggal)),
						'dta' => number_format(number_format($nilai_avg, 3), 2),
						'min' => number_format($nilai_min, 2),
						'max' => number_format($nilai_max, 2)
					);

					//$waktu[]= date('Y-m-d H',strtotime($datalog->waktu)).":00";

				}
				$dataAnalisa = array(
					'idLogger' => $this->session->userdata('idlogger'),
					'namaSensor' => $nama_sensor,
					'satuan' => $satuan,
					'tipe_grafik' => $this->session->userdata('tipe_grafik'),
					'data' => $data,
					'data_tabel' => $data_tabel,
					'nosensor' => $sensor,
					'range' => $range,
					'tooltip' => "Tanggal %d-%m-%Y"
				);
				$dataparam = json_encode($dataAnalisa);
				$data['data_sensor'] = json_decode($dataparam);
			}else{
				$sensor = $this->session->userdata('kolom');
				$nama_sensor = "Rerata_" . $this->session->userdata('nama_parameter');

				$kolom = $this->session->userdata('kolom');

				$select = 'avg(' . $kolom . ') as ' . $nama_sensor;
				$satuan = $this->session->userdata('satuan');

				$query_data = $this->db->query("SELECT waktu, HOUR(waktu) as jam,DAY(waktu) as hari,MONTH(waktu) as bulan,YEAR(waktu) as tahun," . $select . ",min(" . $kolom . ") as min,max(" . $kolom . ") as max  FROM " . $this->session->userdata('tabel') . " where code_logger='" . $this->session->userdata('idlogger') . "' and waktu >= '" . $this->session->userdata('dari') . " 00:00' and waktu <= '" . $this->session->userdata('sampai') . " 23:59' group by HOUR(waktu),DAY(waktu),MONTH(waktu),YEAR(waktu) order by waktu asc;");

				foreach ($query_data->result() as $datalog) {
					$nilai_avg = $datalog->$nama_sensor;
					$nilai_max = $datalog->max;
					$nilai_min = $datalog->min;

					if($this->session->userdata('nama_parameter') == 'Q_Floodway_1' and $this->session->userdata('idlogger') == '10349'){
						$nilai_avg = $this->debitPintu1($nilai_avg);
						$nilai_max = $this->debitPintu1($nilai_max);
						$nilai_min = $this->debitPintu1($nilai_min);
					}elseif($this->session->userdata('nama_parameter') == 'Q_Floodway_2' and $this->session->userdata('idlogger') == '10349'){
						$nilai_avg = $this->debitPintu2($nilai_avg);
						$nilai_max = $this->debitPintu2($nilai_max);
						$nilai_min = $this->debitPintu2($nilai_min);
					}elseif($this->session->userdata('nama_parameter') == 'Q_Floodway_3' and $this->session->userdata('idlogger') == '10349'){
						$nilai_avg = $this->debitPintu3($nilai_avg);
						$nilai_max = $this->debitPintu3($nilai_max);
						$nilai_min = $this->debitPintu3($nilai_min);
					}elseif($this->session->userdata('nama_parameter') == 'Q_Floodway_Gabungan' and $this->session->userdata('idlogger') == '10349'){
						$nilai_avg = $this->debitFloodwayGabungan($nilai_avg);
						$nilai_max = $this->debitFloodwayGabungan($nilai_max);
						$nilai_min = $this->debitFloodwayGabungan($nilai_min);
					}elseif($this->session->userdata('nama_parameter') == 'Debit_Gabungan' and $this->session->userdata('idlogger') == '10349'){
						$nilai_avg = $this->debitGabungan($nilai_avg);
						$nilai_max = $this->debitGabungan($nilai_max);
						$nilai_min = $this->debitGabungan($nilai_min);
					}elseif($this->session->userdata('nama_parameter') == 'Q_Scouring' and $this->session->userdata('idlogger') == '10349'){
						$nilai_avg = $this->debitScouring($nilai_avg);
						$nilai_max = $this->debitScouring($nilai_max);
						$nilai_min = $this->debitScouring($nilai_min);
					}
					$data[] = "[ Date.UTC(" . $datalog->tahun . "," . $datalog->bulan . "-1," . $datalog->hari . "," . $datalog->jam . ")," . number_format($nilai_avg, 3) . "]";
					$range[] = "[ Date.UTC(" . $datalog->tahun . "," . $datalog->bulan . "-1," . $datalog->hari . "," . $datalog->jam . ")," . $nilai_min . "," . $nilai_max . "]";
					$data_tabel[] = array(
						'waktu' => date('Y-m-d H', strtotime($datalog->waktu)) . ':00:00',
						'dta' => number_format($nilai_avg, 2),
						'min' => number_format($nilai_min, 2),
						'max' => number_format($nilai_max, 2)
					);


				}
				$dataAnalisa = array(
					'idLogger' => $this->session->userdata('idlogger'),
					'namaSensor' => $nama_sensor,
					'satuan' => $satuan,
					'tipe_grafik' => $this->session->userdata('tipe_grafik'),
					'data' => $data,
					'data_tabel' => $data_tabel,
					'nosensor' => $kolom,
					'range' => $range,
					'tooltip' => "Waktu %d-%m-%Y %H:%M"
				);

				$dataparam = json_encode($dataAnalisa);
				$data['data_sensor'] = json_decode($dataparam);
			}

			$data['pilih_pos'] = $this->pilihposawgc();

			$data['pilih_parameter'] = $param_all;
			$data['konten'] = 'konten/back/awgc/analisa_awgc';
			$data['pintu'] = [
				'list_pintu' => $this->db->where('id_logger', $this->session->userdata('idlogger'))->get('t_pintu')->result_array(),
				'param_temp' => $param_pintu
			];
			$data['log'] = $this->db->join('t_pintu','t_pintu.id_pintu = log_kontrol.id_pintu')->order_by('datetime','desc')->limit(10)->get('log_kontrol')->result_array();

			$this->load->view('template_admin/site', $data);
		} else {
			redirect('login');
		}
	}


	function multiview()
	{

		if ($this->session->userdata('logged_in')) {
			$data = array();
			$data_tabel = array();
			$prs = [];
			$prs2 = [];
			$range = array();
			$param_log = $this->db->where('analisa','1')->where('logger_id', $this->session->userdata('idlogger'))->get('parameter_sensor')->result_array();
			foreach ($param_log as $key => $pa) {
				$prs[] = [
					'id_param' => $pa['id_param'],
					'nama_parameter' => $pa['nama_parameter'],
					'satuan' => $pa['satuan'],
					'kolom' => $pa['kolom_sensor'],
					'jenis' => 'analisa',
					'chart_index'=>1,
					'jns'=>''
				];
			}
			$pr_pintu = $this->db->where('analisa', '1')->get('parameter_pintu')->result_array();
			foreach ($pr_pintu as $key => $pz) {
				$jenis_pintu = $this->db->where('id_pintu',$pz['id_pintu'])->get('t_pintu')->row();
				if($jenis_pintu->jenis == 'Sekunder'){
					$sekunder[] = [
						'id_param' => $pz['id_param'],
						'nama_parameter' => $pz['nama_parameter'],
						'kolom' => $pz['kolom_sensor'],
						'satuan' => $pz['satuan'],
						'jenis' => 'pintu',
						'jns'=>$jenis_pintu->jenis
					];
				}
				$prs2[] = [
					'id_param' => $pz['id_param'],
					'nama_parameter' => $pz['nama_parameter'],
					'satuan' => $pz['satuan'],
					'kolom' => $pz['kolom_sensor'],
					'jenis' => 'pintu',
					'chart_index'=>0,
					'jns'=>$jenis_pintu->jenis
				];
			}
			$param_all = array_merge($prs2,$prs);
			$sel = '';
			$debit_q = '(';
			foreach($param_all as $key => $vl){
				if($key != array_key_last($param_all)){
					$sel .= 'avg('.$vl["kolom"].') as '.$vl["nama_parameter"].', ';	
				}else{
					$sel .= 'avg('.$vl["kolom"].') as '.$vl["nama_parameter"].' ';	
				}

			}
			foreach($sekunder as $key => $vl){
				if($key != array_key_last($sekunder)){
					$debit_q .= 'avg('.$vl["kolom"].') + ';	
				}else{
					$debit_q .= 'avg('.$vl["kolom"].'))/6 as rerata';	
				}

			}
			if($this->session->userdata('data') == 'hari'){
				$query_data = $this->db->query("SELECT waktu, HOUR(waktu) as jam,DAY(waktu) as hari,MONTH(waktu) as bulan,YEAR(waktu) as tahun,$sel, $debit_q FROM " . $this->session->userdata('tabel') . " where code_logger='" . $this->session->userdata('idlogger') . "' and waktu >= '" . $this->session->userdata('pada') . " 00:00' and waktu <= '" . $this->session->userdata('pada') . " 23:59' group by HOUR(waktu),DAY(waktu),MONTH(waktu),YEAR(waktu) order by waktu asc;")->result_array();
				$dt_all = [];

				foreach($param_all as $key => $vl){
					$kolom = $vl['nama_parameter'];
					$data_each = [];
					$dt_each = [];
					if($vl['nama_parameter'] == 'Debit_Intake'){

						foreach($query_data as $k=>$v){
							$rerata = number_format($v['rerata'] * 1.5/100 - 0.5, 2);
							$n_debit = number_format(1 * 0.8 * $rerata * 10.02 * pow((19.62 * 0.15),0.5), 3);
							if($n_debit < 0 ){
								$n_debit = 0;
							}
							$data_each[] = array(
								'waktu'=>$v['waktu'],
								'nilai'=> number_format($n_debit, 2) ,
							);
							$dt_each[] = "[ Date.UTC(" . $v['tahun'] . "," . $v['bulan'] . "-1," . $v['hari'] . "," . $v['jam'] . ")," . number_format($n_debit, 2) . "]";
						}
					}else{
						foreach($query_data as $k=>$v){

							$data_each[] = array(
								'waktu'=>$v['waktu'],
								'nilai'=> number_format($v[$kolom], 2) ,
							);
							$dt_each[] = "[ Date.UTC(" . $v['tahun'] . "," . $v['bulan'] . "-1," . $v['hari'] . "," . $v['jam'] . ")," . number_format($v[$kolom], 2) . "]";
						}
					}

					$dt_all[] = [
						'nama_param'=>$vl['nama_parameter'],
						'data_each'=>$dt_each,
						'export_data'=>$data_each,
						'satuan'=>$vl['satuan'],
						'chart_index'=>$vl['chart_index'],
					];
				}
				$data['tooltip'] = "Waktu %d-%m-%Y %H:%M";
			}elseif ($this->session->userdata('data') == 'bulan'){
				$query_data = $this->db->query("SELECT waktu, DAY(waktu) as hari,MONTH(waktu) as bulan,YEAR(waktu) as tahun ,$sel, $debit_q  FROM " . $this->session->userdata('tabel') . " where code_logger='" . $this->session->userdata('idlogger') . "' and waktu >= '" . $this->session->userdata('bulan') . "-01 00:00' and waktu <= '" . $this->session->userdata('bulan') . "-31 23:59' group by DAY(waktu),MONTH(waktu),YEAR(waktu) order by waktu asc;")->result_array();
				$dt_all = [];
				foreach($param_all as $key => $vl){
					$kolom = $vl['nama_parameter'];
					$data_each = [];
					$dt_each = [];
					if($vl['nama_parameter'] == 'Debit_Intake'){

						foreach($query_data as $k=>$v){
							$rerata = number_format($v['rerata'] * 1.5/100 - 0.5, 2);
							$n_debit = number_format(1 * 0.8 * $rerata * 10.02 * pow((19.62 * 0.15),0.5), 3);
							if($n_debit < 0 ){
								$n_debit = 0;
							}
							$data_each[] = array(
								'waktu'=>$v['waktu'],
								'nilai'=>number_format($n_debit, 2),
							);
							$dt_each[] = "[ Date.UTC(" . $v['tahun'] . "," . $v['bulan'] . "-1," . $v['hari'] . ")," . number_format($n_debit, 2) . "]";
						}
					}else{
						foreach($query_data as $k=>$v){
							$data_each[] = array(
								'waktu'=>$v['waktu'],
								'nilai'=>number_format($v[$kolom], 2),
							);
							$dt_each[] = "[ Date.UTC(" . $v['tahun'] . "," . $v['bulan'] . "-1," . $v['hari'] . ")," . number_format($v[$kolom], 2) . "]";
						}
					}
					$dt_all[] = [
						'nama_param'=>$vl['nama_parameter'],
						'data_each'=>$dt_each,
						'export_data'=>$data_each,
						'satuan'=>$vl['satuan'],
						'chart_index'=>$vl['chart_index'],
					];
				}
				$data['tooltip'] = "Tanggal %d-%m-%Y";
			}elseif ($this->session->userdata('data') == 'tahun'){
				$query_data = $this->db->query("SELECT waktu, MONTH(waktu) as bulan,YEAR(waktu) as tahun,$sel, $debit_q FROM " . $this->session->userdata('tabel') . " where code_logger='" . $this->session->userdata('idlogger') . "' and waktu >= '" . $this->session->userdata('pada') . "-01-01 00:00' and waktu <= '" . $this->session->userdata('pada') . "-12-31 23:59' group by MONTH(waktu),YEAR(waktu) order by waktu asc;")->result_array();
				$dt_all = [];
				foreach($param_all as $key => $vl){
					$kolom = $vl['nama_parameter'];
					$data_each = [];
					$dt_each = [];
					if($vl['nama_parameter'] == 'Debit_Intake'){
						foreach($query_data as $k=>$v){
							$rerata = number_format($v['rerata'] * 1.5/100 - 0.5, 2);
							$n_debit = number_format(1 * 0.8 * $rerata * 10.02 * pow((19.62 * 0.15),0.5), 3);
							if($n_debit < 0 ){
								$n_debit = 0;
							}
							$data_each[] = array(
								'waktu'=>$v['waktu'],
								'nilai'=>number_format($n_debit, 2),
							);
							$dt_each[] = "[ Date.UTC(" . $v['tahun'] . "," . $v['bulan'] . "-1"  . ")," . number_format($n_debit, 2) . "]";
						}
					}else{
						foreach($query_data as $k=>$v){
							$data_each[] = array(
								'waktu'=>$v['waktu'],
								'nilai'=>number_format($v[$kolom], 2),
							);
							$dt_each[] = "[ Date.UTC(" . $v['tahun'] . "," . $v['bulan'] . "-1"  . ")," . number_format($v[$kolom], 2) . "]";
						}
					}

					$dt_all[] = [
						'nama_param'=>$vl['nama_parameter'],
						'data_each'=>$dt_each,
						'export_data'=>$data_each,
						'satuan'=>$vl['satuan'],
						'chart_index'=>$vl['chart_index'],
					];
				}
				$data['tooltip'] = "Tanggal %m-%Y";
			}else{
				$query_data = $this->db->query("SELECT waktu, HOUR(waktu) as jam,DAY(waktu) as hari,MONTH(waktu) as bulan,YEAR(waktu) as tahun,$sel, $debit_q FROM " . $this->session->userdata('tabel') . " where code_logger='" . $this->session->userdata('idlogger') . "' and waktu >= '" . $this->session->userdata('dari') . " 00:00' and waktu <= '" . $this->session->userdata('sampai') . " 23:59' group by HOUR(waktu),DAY(waktu),MONTH(waktu),YEAR(waktu) order by waktu asc;")->result_array();
				$dt_all = [];
				foreach($param_all as $key => $vl){
					$kolom = $vl['nama_parameter'];
					$data_each = [];
					$dt_each = [];
					if($vl['nama_parameter'] == 'Debit_Intake'){
						foreach($query_data as $k=>$v){
							$rerata = number_format($v['rerata'] * 1.5/100 - 0.5, 2);
							$n_debit = number_format(1 * 0.8 * $rerata * 10.02 * pow((19.62 * 0.15),0.5), 3);
							if($n_debit < 0 ){
								$n_debit = 0;
							}
							$data_each[] = array(
								'waktu'=>$v['waktu'],
								'nilai'=>number_format($n_debit, 2),
							);
							$dt_each[] = "[ Date.UTC(" . $v['tahun'] . "," . $v['bulan'] . "-1," . $v['hari'] . "," . $v['jam'] . ")," . number_format($n_debit, 2) . "]";}
					}else{
						foreach($query_data as $k=>$v){
							$data_each[] = array(
								'waktu'=>$v['waktu'],
								'nilai'=>number_format($v[$kolom], 2),
							);
							$dt_each[] = "[ Date.UTC(" . $v['tahun'] . "," . $v['bulan'] . "-1," . $v['hari'] . "," . $v['jam'] . ")," . number_format($v[$kolom], 2) . "]";
						}
					}
					$dt_all[] = [
						'nama_param'=>$vl['nama_parameter'],
						'data_each'=>$dt_each,
						'export_data'=>$data_each,
						'satuan'=>$vl['satuan'],
						'chart_index'=>$vl['chart_index'],
					];
				}
				$data['tooltip'] = "Waktu %d-%m-%Y %H:%M";
			}

			$data['pilih_pos'] = $this->pilihposawgc();
			$data['pilih_parameter'] = $param_all;
			$data['log'] = $this->db->join('t_pintu','t_pintu.id_pintu = log_kontrol.id_pintu')->order_by('datetime','desc')->limit(10)->get('log_kontrol')->result_array();

			$data['dt_all']=$dt_all;

			$data['konten'] = 'konten/back/awgc/multiview_awgc';
			$this->load->view('template_admin/site', $data);
		} else {
			redirect('login');
		}
	}

	function log_kontrol(){
		$data['log'] = $this->db->join('t_pintu','t_pintu.id_pintu = log_kontrol.id_pintu')->order_by('datetime','desc')->get('log_kontrol')->result_array();
		$data['konten'] = 'konten/back/awgc/log_kontrol';
		$this->load->view('template_admin/site', $data);
	}

	function export_excel (){

		include APPPATH.'third_party/PHPExcel/PHPExcel.php';

		// Panggil class PHPExcel nya
		$excel = new PHPExcel();
		// Settingan awal fil excel
		$excel->getProperties()->setCreator('Beacon Engineering')
			->setTitle("Data")
			->setDescription("Data Semua Parameter");
		$excel->setActiveSheetIndex(0)->setCellValue('B2', "Grafik Semua Parameter");
		$data = json_decode(htmlspecialchars_decode($this->input->post('data')));
		$title = $this->input->post('title');
		$column = 'C';
		$row = '3';
		$excel->setActiveSheetIndex(0)->setCellValue('B3', 'Waktu');

		foreach($data as $key=>$v){
			$cl = $column ++;
			$excel->setActiveSheetIndex(0)->setCellValue($cl . $row, $v->nama_param);
			foreach($v->export_data as $k =>$vl){
				$rows = $row + 1 + $k ;
				$excel->setActiveSheetIndex(0)->setCellValue('B' . $rows, $vl->waktu);
				$excel->setActiveSheetIndex(0)->setCellValue($cl . $rows, $vl->nilai . ' ' . $v->satuan);
			}
		}
		foreach(range('B','L') as $columnID) {
			$excel->getActiveSheet()->getColumnDimension($columnID)
				->setAutoSize(true);
		}
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="'. $this->session->userdata('namalokasi') .' - Data Semua Parameter -'.$title.'.xlsx"'); // Set nama file excel nya
		header('Cache-Control: max-age=0');
		$write = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
		$write->save('php://output');
	}

	function export_excel_single (){

		include APPPATH.'third_party/PHPExcel/PHPExcel.php';

		// Panggil class PHPExcel nya
		$excel = new PHPExcel();
		// Settingan awal fil excel
		$title = $this->input->post('title');
		$excel->getProperties()->setCreator('Beacon Engineering')
			->setTitle("Data")
			->setDescription("Data Semua Parameter");
		$excel->setActiveSheetIndex(0)->setCellValue('B2', "Data - " . $this->session->userdata('namalokasi') ." - ". $this->session->userdata('nama_parameter')." -".$title);
		$data = json_decode(htmlspecialchars_decode($this->input->post('data')));
		$excel->getActiveSheet()->mergeCells('B2:E2');
		$column = 'C';
		$row = '3';
		$excel->setActiveSheetIndex(0)->setCellValue('B3','Waktu');
		$excel->setActiveSheetIndex(0)->setCellValue('C3','Rerata');
		$excel->setActiveSheetIndex(0)->setCellValue('D3','Minimal');
		$excel->setActiveSheetIndex(0)->setCellValue('E3','Maksimal');
		//$excel->setActiveSheetIndex(0)->setCellValue($cl . $row, $v->nama_param);
		foreach($data as $k =>$vl){
			$cl = $column ++;
			$rows = $row + 1 + $k ;
			$excel->setActiveSheetIndex(0)->setCellValue('B' . $rows, $vl->waktu);
			$excel->setActiveSheetIndex(0)->setCellValue('C'  . $rows, $vl->dta);
			$excel->setActiveSheetIndex(0)->setCellValue('D' . $rows, $vl->min);
			$excel->setActiveSheetIndex(0)->setCellValue('E' . $rows, $vl->max);
		}
		foreach(range('B','E') as $columnID) {
			$excel->getActiveSheet()->getColumnDimension($columnID)
				->setAutoSize(true);
		}
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="'. $this->session->userdata('namalokasi') .' - '. $this->session->userdata('nama_parameter').' -'.$title.'.xlsx"'); // Set nama file excel nya
		header('Cache-Control: max-age=0');
		$write = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
		$write->save('php://output');
	}
}
