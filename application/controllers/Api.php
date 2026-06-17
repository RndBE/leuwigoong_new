<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Api extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->model('mlogin');
		$this->load->model('m_analisa');
		$this->load->library('PhpMQTT');
	}

	function login_app2()
	{
		$username = $this->input->get('username');
		$password = md5($this->input->get('password'));
		$this->mlogin->apiambilPengguna2($username, $password);
	}

	// === Fungsi debit (lookup tabel rating "ele.v (1).xlsx") ===
	// Implementasi tunggal: application/helpers/debit_helper.php (autoload).
	// Parameter = TMA BENDUNG dalam meter (sensor1 logger 10349).
	// Aturan: TMA bendung 0 => semua debit floodway = 0.

	function debitPintu1($tma_bendung) {
		return debit_pintu1($tma_bendung);
	}

	function debitPintu2($tma_bendung) {
		return debit_pintu2($tma_bendung);
	}

	function debitPintu3($tma_bendung) {
		return debit_pintu3($tma_bendung);
	}

	function debitGabungan($tma_bendung) {
		return debit_gabungan($tma_bendung);
	}

	function debitScouring($tma_bendung) {
		return debit_scouring($tma_bendung);
	}

	function debitFloodwayGabungan($tma_bendung) {
		return debit_floodway_gabungan($tma_bendung);
	}

	public function pilihparameter($idlogger)
	{
		$data = array();
		$q_parameter = $this->db->query("SELECT * FROM parameter_sensor where logger_id='" . $idlogger . "' and analisa ='1'");
		foreach ($q_parameter->result() as $param) {
			$data[] = array(
				'idParameter' => $param->id_param, 'namaParameter' => $param->nama_parameter, 'fieldParameter' => $param->kolom_sensor,
				'icon' => $param->icon_sensor,
				'jenis'=>'umum'
			);
		}
		$cek = $this->db->where('id_logger',$idlogger)->get('t_logger')->row();
		if($cek->kategori_log == '1'){
			$param_pintu = $this->db->join('t_pintu', 't_pintu.id_pintu=parameter_pintu.id_pintu')->where('t_pintu.id_logger',$idlogger)->where('parameter_pintu.analisa','1')->get('parameter_pintu')->result();
			foreach($param_pintu as $v){
				$data[] = array(
					'idParameter' => $v->id_param, 'namaParameter' => $v->nama_parameter . ' - ' .  $v->nama_pintu, 'fieldParameter' => $v->kolom_sensor,
					'icon' => $v->icon_sensor,
					'jenis'=>'awgc'
				);
			}
		}
		$q_parameter = $this->db->query("SELECT * FROM parameter_sensor where logger_id='" . $idlogger . "' and analisa ='0'");
		foreach ($q_parameter->result() as $param) {
			$data[] = array(
				'idParameter' => $param->id_param, 'namaParameter' => $param->nama_parameter, 'fieldParameter' => $param->kolom_sensor,
				'icon' => $param->icon_sensor,
				'jenis'=>'umum'
			);
		}
		echo json_encode($data);
	}

	function get_pintu(){
		$id_sensor = $this->input->get('id_sensor');
		$tanggal = $this->input->get('tanggal');
		$list_pintu = $this->db->join('parameter_pintu','parameter_pintu.id_pintu=t_pintu.id_pintu')->group_by('t_pintu.id_pintu')->get('t_pintu')->result_array();
		$pintu = $this->db->join('parameter_pintu','parameter_pintu.id_pintu=log_kontrol.id_pintu')->join('t_pintu','t_pintu.id_pintu=log_kontrol.id_pintu')->where('parameter_pintu.id_param',$id_sensor)->like('datetime',$tanggal)->order_by('datetime','desc')->get('log_kontrol')->result_array();
		if($pintu){
			$data = [
				'data'=>$pintu,
				'id_pintu'=>$pintu[0]['id_pintu'],
				'nama_pintu'=>$pintu[0]['nama_pintu'],
				'daftar_pintu'=>$list_pintu,
			];
		}else{
			$pintu = $this->db->join('parameter_pintu','parameter_pintu.id_pintu=t_pintu.id_pintu')->where('parameter_pintu.id_param',$id_sensor)->get('t_pintu')->row();
			$data = [
				'data'=>null,
				'id_pintu'=>$pintu->id_pintu,
				'nama_pintu'=>$pintu->nama_pintu,
				'daftar_pintu'=>$list_pintu,
			];
		}
		echo json_encode($data);
	}

	function lokasi_new()
	{
		$kategori = array();
		$data = array();
		$query_kategori = $this->db->query('select * from kategori_logger');
		//$klasifikasi
		foreach ($query_kategori->result()  as $kat) {
			$tabel = $kat->tabel;
			$tabel_temp = $kat->temp_data;
			$content = array();
			$query_lokasilogger = $this->db->query("select * from t_logger inner join t_lokasi ON t_logger.lokasi_logger=t_lokasi.idlokasi where kategori_log='$kat->id_katlogger'");


			foreach ($query_lokasilogger->result() as $loklogger) {
				$id_logger = $loklogger->id_logger;

				$parameter = array();
				$query_data = $this->db->query('select * from ' . $tabel_temp . ' where code_logger="' . $id_logger . '"');
				foreach ($query_data->result() as $dt) {
					$waktu = $dt->waktu;
					$awal = date('Y-m-d H:i', (mktime(date('H') - 1)));
					$query_parameter = $this->db->query('select * from parameter_sensor where logger_id="' . $id_logger . '" limit 1');
					foreach ($query_parameter->result() as $param) {
						$kolom = $param->kolom_sensor;
						$dta = $dt->$kolom;
						$get = 'tabel=' . $kat->tabel . '&id_param=' . $param->id_param;
						$link_parameter = anchor($kat->controller . '/set_sensordash?' . $get, $param->nama_parameter);
						$parameter[] = '
								<td>' . $link_parameter . '</td><td>' . $dta . ' ' . $param->satuan . '</td>
								';
					}
					$data_sensor = $query_parameter->result_array()[0];
					######### cek status koneksi ######
					$dta = $dt->$kolom;
					$koneksi = '';
					if ($waktu >= $awal) {
						$koneksi = 'Koneksi Terhubung';
						$kn = 'On';

						$icon_marker = $kat->tabel . '_on';
					} else {
						$koneksi = 'Koneksi Terputus';
						$kn = 'Off';
						$icon_marker = $kat->tabel . '_off';
					}
				}

				$data[] = array(
					'id_kategori'=>$kat->id_katlogger,
					'tabel' => $tabel,
					'sensor' => $data_sensor['id_param'],
					'nama_param' => $data_sensor['nama_parameter'],
					'icon_sensor' => $data_sensor['icon_sensor'],
					'id_param' => $data_sensor['id_param'],
					'lokasi' => $loklogger->nama_lokasi,
					'latitude' => $loklogger->latitude,
					'longitude' => $loklogger->longitude,
					'id_logger' => $id_logger,
					'waktu' => $waktu,
					'koneksi' => $koneksi,
					'koneksi_log' => $kn,
					'icon' => $icon_marker,
				);
			}
		}
		echo json_encode($data);
	}

	function menu()
	{
		$dataMenu = array();
		$kategori = $this->db->query("SELECT * FROM kategori_logger");
		foreach ($kategori->result() as $kat) {
			if($kat->controller == 'awgc'){
				$query_lokasi = $this->db->where('kategori_log',$kat->id_katlogger)->get('t_logger')->row();
				$filter = $this->db->where('id_kategori',$kat->id_katlogger)->get('filter')->result_array();
				$dataMenu[] = array(
					'id_kategori' => $kat->id_katlogger,
					'menu' => $kat->nama_kategori,
					'id_logger' => $query_lokasi->id_logger,
					'controller' => $kat->controller,
					'tabel' => $kat->tabel,
					'icon' => $kat->icon_app,
					'temp_tabel' => $kat->temp_data,
					'filter'=>$filter
				);
			}else{
				$filter = $this->db->where('id_kategori',$kat->id_katlogger)->get('filter')->result_array();
				$dataMenu[] = array(
					'id_kategori' => $kat->id_katlogger,
					'menu' => $kat->nama_kategori,
					'id_logger' => '',
					'controller' => $kat->controller,
					'tabel' => $kat->tabel,
					'icon' => $kat->icon_app,
					'temp_tabel' => $kat->temp_data,
					'filter'=>$filter
				);
			}

		}
		echo json_encode($dataMenu);
	}


	public function notif_versi()
	{
		$this->load->library('user_agent');
		$versi = '1.1.0';

		$user_agent = $this->agent->agent_string(); // Full user agent string

		if (stripos($user_agent, 'Android') !== false) {
			echo json_encode(array('versi' => $versi, 'link' => 'https://leuwigoong.beacontelemetry.com/unduh/ciliwung_1.2.3.apk'));
		} else {
			echo json_encode(array('versi' => $versi, 'link' => 'https://apps.apple.com/id/app/ciliwung-view/id6739390387'));
		}

	}

	public function notif_versi_ios()
	{
		$versi = '1.1.0';
		echo json_encode(array('versi' => $versi, 'link' => 'https://cikeusik.monitoring4system.com/unduh/cikeusik_1.1.1.apk'));
	}


	function lokasi()
	{
		$tabel = $this->input->get('tabel');

		$dataLokasi = array();
		$kat = $this->db->where('temp_data',$tabel)->get('kategori_logger')->row();


		$query_lokasi = $this->db->query("SELECT * FROM t_logger join t_lokasi on t_logger.lokasi_logger=t_lokasi.idlokasi where kategori_log='".$kat->id_katlogger."'");
		foreach($query_lokasi->result() as $lokasilog)
		{
			$this->session->set_userdata('id_log',$lokasilog->id_logger);
			$query_perbaikan=$this->db->query('select * from t_perbaikan where id_logger="'.$lokasilog->id_logger.'" ');
			if($query_perbaikan->num_rows() == null) {
				$cek = $this->db->where('code_logger',$lokasilog->id_logger)->get($kat->temp_data)->row()->waktu;

				$date = date('Y-m-d H:i:s',(mktime(date('H')-1)));
				if($cek > $date){
					$status = 'On';
				}else{
					$status = 'Off';
				}
				$dataLokasi[]=array(
					'logger_id' =>$lokasilog->id_logger,
					'nama_logger' =>$lokasilog->nama_logger,
					'lokasi' =>$lokasilog->nama_lokasi,
					'latitude'=>$lokasilog->latitude,
					'longitude'=>$lokasilog->longitude,
					'status'=>$status,
				);
			}
			else {
				$dataLokasi[]=array(
					'logger_id' =>$lokasilog->id_logger,
					'nama_logger' =>$lokasilog->nama_logger,
					'lokasi' =>$lokasilog->nama_lokasi,
					'latitude'=>$lokasilog->latitude,
					'longitude'=>$lokasilog->longitude,
					'status'=>"Perbaikan",	
				);
			}
		}
		echo json_encode(array('lokasi_first'=>$dataLokasi[0],'lokasi'=>$dataLokasi));

	}

	function lokasi2()
	{
		$tabel = $this->input->get('tabel');
		$id_logger = $this->input->get('id_logger');

		if($tabel == 'temp_awgc'){
			$query_pintu = $this->db->where('analisa','1')->get('parameter_pintu')->result_array();

			$status_kontrol = $this->db->where('id_logger',$id_logger)->get('status_kontrol')->row();
			$nama_lokasi = $this->db->where('id_logger',$id_logger)->join('t_lokasi','t_lokasi.idlokasi = t_logger.lokasi_logger')->get('t_logger')->row();
			$query_pintu = $this->db->where('analisa','1')->get('parameter_pintu')->result_array();
			$rerata = 0;

			$query_lokasi = $this->db->order_by('id_logger','asc')->order_by('id_pintu','asc')->get('t_pintu')->result_array();
			foreach($query_lokasi as $key => $val) {
				$cek = $this->db->where('code_logger', $val['id_logger'])->get($tabel)->row();

				$date_now = date('Y:m:d H:i:s');
				$date = date('Y-m-d H:i:s', strtotime('-1 hour', strtotime($date_now)));
				if ($cek->waktu > $date) {
					$status = 'On';
				} else {
					$status = 'Off';
				}
				$sensor_level = $val['sensor_level'];
				$r = $val['r'];
				$s = $val['s'];
				$t = $val['t'];
				$status_controller = $val['status_controller'];
				if($cek->$r == '0' or $cek->$s == '0' or $cek->$t == '0'){
					$status_rst = '0';
				}else{
					$status_rst = '1';
				}
				$dataLokasi[] = [
					'id_logger'=>$val['id_logger'],
					'id_pintu'=>$val['id_pintu'],
					'status_logger'=>$status,
					'nama_pintu'=>$val['nama_pintu'],
					'level_pintu'=>(string)  round($cek->$sensor_level),
					'batas_atas'=>$val['batas_atas'],
					'batas_bawah'=>$val['batas_bawah'],
					'r'=>$cek->$r,
					's'=>$cek->$s,
					't'=>$cek->$t,
					'status_controller'=>$cek->$status_controller,
					'jenis_pintu'=>$val['jenis'],
					'status_rst'=>$status_rst
				];
			}

			$parameter = $this->db->where('logger_id',$id_logger)->where('analisa','0')->get('parameter_sensor')->result_array();
			$data = [
				'id_logger'=>$id_logger,
				'nama_lokasi'=>$nama_lokasi->nama_lokasi,
				'waktu'=>$cek->waktu,
				'status'=>$status,
				'first'=>$dataLokasi[0],
				'daftar_pintu'=>$dataLokasi,
			];
			$data['analisa'] = [];
			foreach($parameter as $key=> $v){
				$sensor = $v['kolom_sensor'];

				$data['analisa'][] = [
					'id_param'=>$v['id_param'],
					'nama_param'=>$v['nama_parameter'],
					'nilai'=>$cek->$sensor,
					'satuan'=>$v['satuan'],
					'icon'=>$v['icon_sensor'],
				];
			}

			$parameter2 = $this->db->where('logger_id',$id_logger)->where('analisa','1')->get('parameter_sensor')->result_array();
			$data['param_new'] = [];
			$cek2 = $this->db->where('code_logger', $id_logger)->get($tabel)->row();
			foreach($parameter2 as $key=> $v){
				$sensor = $v['kolom_sensor'];
				$nilai_sensor = $cek2->$sensor;
				if($v['nama_parameter'] == 'Q_Floodway_1' and $id_logger == '10349'){
					$nilai_sensor = $this->debitPintu1($nilai_sensor);
				}elseif($v['nama_parameter'] == 'Q_Floodway_2' and $id_logger == '10349'){
					$nilai_sensor = $this->debitPintu2($nilai_sensor);
				}elseif($v['nama_parameter'] == 'Q_Floodway_3' and $id_logger == '10349'){
					$nilai_sensor = $this->debitPintu3($nilai_sensor);
				}elseif($v['nama_parameter'] == 'Q_Floodway_Gabungan' and $id_logger == '10349'){
					$nilai_sensor = $this->debitFloodwayGabungan($nilai_sensor);
				}elseif($v['nama_parameter'] == 'Debit_Gabungan' and $id_logger == '10349'){
					$nilai_sensor = $this->debitGabungan($nilai_sensor);
				}elseif($v['nama_parameter'] == 'Q_Scouring' and $id_logger == '10349'){
					$nilai_sensor = $this->debitScouring($nilai_sensor);
				}
				$data['param_new'][] = [
					'id_param'=>$v['id_param'],
					'nama_param'=>$v['nama_parameter'],
					'nilai'=>number_format($nilai_sensor,3,'.',''),
					'satuan'=>$v['satuan'],
					'icon'=>$v['icon_sensor'],
				];

			}
			$data['status_kontrol'] = $status_kontrol->status_kontrol;
			echo json_encode($data);
		}else{
			$dataLokasi = array();
			$kat = $this->db->where('temp_data',$tabel)->get('kategori_logger')->row();
			$query_lokasi = $this->db->query("SELECT * FROM t_logger join t_lokasi on t_logger.lokasi_logger=t_lokasi.idlokasi where kategori_log='".$kat->id_katlogger."'");
			foreach($query_lokasi->result() as $lokasilog)
			{
				$this->session->set_userdata('id_log',$lokasilog->id_logger);
				$query_perbaikan=$this->db->query('select * from t_perbaikan where id_logger="'.$lokasilog->id_logger.'" ');
				if($query_perbaikan->num_rows() == null) {
					$cek = $this->db->where('code_logger',$lokasilog->id_logger)->get($tabel)->row()->waktu;

					$date = date('Y-m-d H:i:s',(mktime(date('H')-1)));
					if($cek > $date){
						$status = 'On';
					}else{
						$status = 'Off';
					}
					$dataLokasi[]=array(
						'logger_id' =>$lokasilog->id_logger,
						'nama_logger' =>$lokasilog->nama_logger,
						'lokasi' =>$lokasilog->nama_lokasi,
						'latitude'=>$lokasilog->latitude,
						'longitude'=>$lokasilog->longitude,
						'status'=>$status,
					);
				}
				else {
					$dataLokasi[]=array(
						'logger_id' =>$lokasilog->id_logger,
						'nama_logger' =>$lokasilog->nama_logger,
						'lokasi' =>$lokasilog->nama_lokasi,
						'latitude'=>$lokasilog->latitude,
						'longitude'=>$lokasilog->longitude,
						'status'=>"Perbaikan",	
					);
				}
			}
			echo json_encode(array('lokasi_first'=>$dataLokasi[0],'lokasi'=>$dataLokasi));
		}

	}

	function lokasi3()
	{
		$tabel = $this->input->get('tabel');
		$id_logger = $this->input->get('id_logger');

		if($tabel == 'temp_awgc'){
			$query_pintu = $this->db->where('analisa','1')->get('parameter_pintu')->result_array();

			$status_kontrol = $this->db->where('id_logger',$id_logger)->get('status_kontrol')->row();
			$nama_lokasi = $this->db->where('id_logger',$id_logger)->join('t_lokasi','t_lokasi.idlokasi = t_logger.lokasi_logger')->get('t_logger')->row();
			$query_pintu = $this->db->where('analisa','1')->get('parameter_pintu')->result_array();
			$rerata = 0;

			$query_lokasi = $this->db->where('id_logger',$id_logger)->get('t_pintu')->result_array();
			foreach($query_lokasi as $key => $val) {
				$cek = $this->db->where('code_logger', $val['id_logger'])->get($tabel)->row();

				$date_now = date('Y:m:d H:i:s');
				$date = date('Y-m-d H:i:s', strtotime('-1 hour', strtotime($date_now)));
				if ($cek->waktu > $date) {
					$status = 'On';
				} else {
					$status = 'Off';
				}
				$sensor_level = $val['sensor_level'];
				$r = $val['r'];
				$s = $val['s'];
				$t = $val['t'];
				$status_controller = $val['status_controller'];
				if($cek->$r == '0' or $cek->$s == '0' or $cek->$t == '0'){
					$status_rst = '0';
				}else{
					$status_rst = '1';
				}
				$dataLokasi[] = [
					'id_logger'=>$val['id_logger'],
					'id_pintu'=>$val['id_pintu'],
					'status_logger'=>$status,
					'nama_pintu'=>$val['nama_pintu'],
					'level_pintu'=>(string)  round($cek->$sensor_level),
					'batas_atas'=>$val['batas_atas'],
					'batas_bawah'=>$val['batas_bawah'],
					'r'=>$cek->$r,
					's'=>$cek->$s,
					't'=>$cek->$t,
					'status_controller'=>$cek->$status_controller,
					'jenis_pintu'=>$val['jenis'],
					'status_rst'=>$status_rst
				];
			}

			$parameter = $this->db->where('logger_id',$id_logger)->where('analisa','0')->get('parameter_sensor')->result_array();
			$data = [
				'id_logger'=>$id_logger,
				'nama_lokasi'=>$nama_lokasi->nama_lokasi,
				'waktu'=>$cek->waktu,
				'status'=>$status,
				'first'=>$dataLokasi[0],
				'daftar_pintu'=>$dataLokasi,
			];
			$data['analisa'] = [];
			foreach($parameter as $key=> $v){
				$sensor = $v['kolom_sensor'];

				$data['analisa'][] = [
					'id_param'=>$v['id_param'],
					'nama_param'=>$v['nama_parameter'],
					'nilai'=>$cek->$sensor,
					'satuan'=>$v['satuan'],
					'icon'=>$v['icon_sensor'],
				];
			}

			$parameter2 = $this->db->where('logger_id',$id_logger)->where('analisa','1')->get('parameter_sensor')->result_array();
			$data['param_new'] = [];
			foreach($parameter2 as $key=> $v){
				$sensor = $v['kolom_sensor'];

				$data['param_new'][] = [
					'id_param'=>$v['id_param'],
					'nama_param'=>$v['nama_parameter'],
					'nilai'=>$cek->$sensor,
					'satuan'=>$v['satuan'],
					'icon'=>$v['icon_sensor'],
				];

			}
			$data['status_kontrol'] = $status_kontrol->status_kontrol;
			echo json_encode($data);
		}else{
			$dataLokasi = array();
			$kat = $this->db->where('temp_data',$tabel)->get('kategori_logger')->row();
			$query_lokasi = $this->db->query("SELECT * FROM t_logger join t_lokasi on t_logger.lokasi_logger=t_lokasi.idlokasi where kategori_log='".$kat->id_katlogger."'");
			foreach($query_lokasi->result() as $lokasilog)
			{
				$this->session->set_userdata('id_log',$lokasilog->id_logger);
				$query_perbaikan=$this->db->query('select * from t_perbaikan where id_logger="'.$lokasilog->id_logger.'" ');
				if($query_perbaikan->num_rows() == null) {
					$cek = $this->db->where('code_logger',$lokasilog->id_logger)->get($tabel)->row()->waktu;

					$date = date('Y-m-d H:i:s',(mktime(date('H')-1)));
					if($cek > $date){
						$status = 'On';
					}else{
						$status = 'Off';
					}
					$dataLokasi[]=array(
						'logger_id' =>$lokasilog->id_logger,
						'nama_logger' =>$lokasilog->nama_logger,
						'lokasi' =>$lokasilog->nama_lokasi,
						'latitude'=>$lokasilog->latitude,
						'longitude'=>$lokasilog->longitude,
						'status'=>$status,
					);
				}
				else {
					$dataLokasi[]=array(
						'logger_id' =>$lokasilog->id_logger,
						'nama_logger' =>$lokasilog->nama_logger,
						'lokasi' =>$lokasilog->nama_lokasi,
						'latitude'=>$lokasilog->latitude,
						'longitude'=>$lokasilog->longitude,
						'status'=>"Perbaikan",	
					);
				}
			}
			echo json_encode(array('lokasi_first'=>$dataLokasi[0],'lokasi'=>$dataLokasi));
		}

	}


	function data_pintu2 () {
		$tabel = $this->input->get('tabel');
		$id_pintu = $this->input->get('id_pintu');
		$dataLokasi = array();
		$query_lokasi = $this->db->where('id_pintu',$id_pintu)->get('t_pintu')->row();
		$id_logger = $query_lokasi->id_logger;
		$cek = $this->db->where('code_logger', $id_logger)->get($tabel)->row();
		$date_now = date('Y:m:d H:i:s');
		$date = date('Y-m-d H:i:s', strtotime('-1 hour', strtotime($date_now)));
		if ($cek->waktu > $date) {
			$status = 'On';
		} else {
			$status = 'Off';
		}

		$sensor_level = $query_lokasi->sensor_level;
		$r =  $query_lokasi->r;
		$s =  $query_lokasi->s;
		$t =  $query_lokasi->t;
		$dataLokasi= [
			'id_pintu'=>$query_lokasi->id_pintu,
			'nama_pintu'=>$query_lokasi->nama_pintu,
			'level_pintu'=>(string) round($cek->$sensor_level ),
			'batas_atas'=>$query_lokasi->batas_atas,
			'r'=>$cek->$r,
			's'=>$cek->$s,
			't'=>$cek->$t,
			'jenis_pintu'=>$query_lokasi->jenis,
		];

		$parameter = $this->db->where('logger_id',$id_logger)->where('analisa','0')->get('parameter_sensor')->result_array();
		$sts = $this->db->where('id_logger',$id_logger)->get('status_kontrol')->row();
		$data = [
			'waktu'=>$cek->waktu,
			'status_kontrol'=>$sts->status_kontrol,
			'data_pintu'=>$dataLokasi,
		];
		$data['analisa'] = [];
		foreach($parameter as $key=> $v){
			$sensor = $v['kolom_sensor'];
			$data['analisa'][] = [
				'id_param'=>$v['id_param'],
				'nama_param'=>$v['nama_parameter'],
				'nilai'=>$cek->$sensor,
				'satuan'=>$v['satuan'],
				'icon'=>$v['icon_sensor'],
			];
		}
		$data['param_new'] = [];
		$query_pintu = $this->db->where('analisa','1')->get('parameter_pintu')->result_array();
		$rerata = 0;

		$parameter2 = $this->db->where('logger_id',$id_logger)->where('analisa','1')->get('parameter_sensor')->result_array();
		foreach($parameter2 as $key=> $v){
			$sensor = $v['kolom_sensor'];

			$n =  $cek->$sensor;

			if($v['nama_parameter'] == 'Q_Floodway_1' and $id_logger == '10349'){
				$n = $this->debitPintu1($n);
			}elseif($v['nama_parameter'] == 'Q_Floodway_2' and $id_logger == '10349'){
				$n = $this->debitPintu2($n);
			}elseif($v['nama_parameter'] == 'Q_Floodway_3' and $id_logger == '10349'){
				$n = $this->debitPintu3($n);
			}elseif($v['nama_parameter'] == 'Q_Floodway_Gabungan' and $id_logger == '10349'){
				$n = $this->debitFloodwayGabungan($n);
			}elseif($v['nama_parameter'] == 'Debit_Gabungan' and $id_logger == '10349'){
				$n = $this->debitGabungan($n);
			}elseif($v['nama_parameter'] == 'Q_Scouring' and $id_logger == '10349'){
				$n= $this->debitScouring($n);
			}
			$data['param_new'][] = [
				'id_param'=>$v['id_param'],
				'nama_param'=>$v['nama_parameter'],
				'nilai'=>number_format($n,3,'.',''),
				'satuan'=>$v['satuan'],
				'icon'=>$v['icon_sensor'],
			];

		}
		echo json_encode($data);
	}



	function dtakhir()
	{
		$idlog = $this->input->get('idlogger'); 
		$tabel = $this->input->get('tabel');
		$data_terakhir = array();
		$data_logger = $this->db->join('t_lokasi', 't_logger.lokasi_logger = t_lokasi.idlokasi')->where('t_logger.id_logger', $idlog)->get('t_logger')->row();
		$ipcam = json_decode(file_get_contents('https://leuwigoong.beacontelemetry.com/ipcam/get_all_photo.php'),true);
		$ipcam_link = '';
		$cek = isset($ipcam[$idlog]);

		if($cek){
			$ipcam_link = $ipcam[$idlog];
		}
		$query_perbaikan = $this->db->query('select * from t_perbaikan where id_logger="' . $idlog . '" ');
		if ($query_perbaikan->num_rows() == null) {
			$qparam = $this->db->query("SELECT * FROM parameter_sensor where logger_id='" . $idlog . "'");
			foreach ($qparam->result() as $sensor) {
				$kolom = $sensor->kolom_sensor;
				$qdataparam = $this->db->query("SELECT * FROM " . $tabel . " where code_logger='" . $idlog . "' order by waktu desc limit 1");

				foreach ($qdataparam->result() as $data) {
					$datasensor = $data->$kolom;
					$waktu = $data->waktu;
				}
				$data_terakhir[] = array(
					'idsensor' => $sensor->id_param,
					'sensor' => $sensor->nama_parameter,
					'data' => $datasensor,
					'satuan' => $sensor->satuan,
					'icon' => $sensor->icon_sensor,
					'tipe_graf' => $sensor->tipe_graf,
				);
			}
			//echo json_encode()
			$a = null;
			$data_akhir = array(
				'nama_logger' => $data_logger->nama_lokasi,
				'waktu' => $waktu,
				'tabel' => $tabel,
				'ipcam_link'=>$ipcam_link,
				'data_terakhir' => $data_terakhir
			);
			echo json_encode($data_akhir);
		} else {
			foreach ($query_perbaikan->result() as $data_perbaikan) {
				$d_per =	$data_perbaikan->data_terakhir;
				$data_per = json_decode($d_per);
				$data_akhir = $data_per->kolom;
				$data_terakhir[] = array(
					'idsensor' => $data_per->id_param,
					'sensor' => $data_per->nama_parameter,
					'data' => $data_akhir,
					'satuan' => $data_per->satuan,
					'icon' => $data_per->icon_sensor
				);
			}
			foreach ($data_terakhir as $key => $dt3) {
				$data_terakhir[$key]['kat_data'] = '1';
			}
			$data_akhir = array(
				'nama_logger' => $data_logger->nama_lokasi,
				'waktu' => $data_per->waktu,
				'ipcam_link'=>$ipcam_link,
				'data_terakhir' => $data_terakhir
			);
			echo json_encode($data_akhir);
		}
	}

	function analisapertanggal()
	{
		$idsensor = $this->input->get('idsensor');
		$tabel = $this->input->get('tabel');
		$tanggal = $this->input->get('tanggal');
		$jenis = $this->input->get('jenis');

		if($jenis == 'awgc'){
			$qparam = $this->db
				->select('parameter_pintu.*, t_pintu.id_logger')
				->from('parameter_pintu')
				->join('t_pintu','t_pintu.id_pintu = parameter_pintu.id_pintu')
				->where('parameter_pintu.id_param',$idsensor)
				->get();
		} else {
			$qparam = $this->db
				->select('*')
				->from('parameter_sensor')
				->where('id_param',$idsensor)
				->get();
		}

		foreach($qparam->result() as $param){
			$idlogger = ($jenis == 'awgc') ? $param->id_logger : $param->logger_id;
			$sensor = $param->kolom_sensor;
			$namaSensor = ($param->tipe_graf == 'column') 
				? 'Akumulasi_'.$param->nama_parameter 
				: 'Rerata_'.$param->nama_parameter;
			$select = ($param->tipe_graf == 'column')
				? "SUM($sensor) AS $namaSensor"
				: "AVG($sensor) AS $namaSensor";
			$satuan = $param->satuan;
			$tipegraf = $param->tipe_graf;
		}

		$hsl = $this->db
			->select("waktu,$select,MIN($sensor) AS min,MAX($sensor) AS max",false)
			->from($tabel)
			->where('code_logger',$idlogger)
			->where("waktu >=",$tanggal." 00:00")
			->where("waktu <=",$tanggal." 23:59")
			->group_by(["HOUR(waktu)","DAY(waktu)","MONTH(waktu)","YEAR(waktu)"])
			->get()
			->result();

		foreach($hsl as $d){
			$nilai_avg = $d->$namaSensor;
			$nilai_min = $d->min;
			$nilai_max = $d->max;
			if($param->nama_parameter == 'Q_Floodway_1'){
				$nilai_avg = $this->debitPintu1($nilai_avg );	
				$nilai_min = $this->debitPintu1($nilai_min);	
				$nilai_max = $this->debitPintu1($nilai_max );
			}elseif($param->nama_parameter == 'Q_Floodway_2'){
				$nilai_avg = $this->debitPintu2($nilai_avg );	
				$nilai_min = $this->debitPintu2($nilai_min);	
				$nilai_max = $this->debitPintu2($nilai_max );
			}elseif($param->nama_parameter == 'Q_Floodway_3'){
				$nilai_avg = $this->debitPintu3($nilai_avg );	
				$nilai_min = $this->debitPintu3($nilai_min);	
				$nilai_max = $this->debitPintu3($nilai_max );
			}elseif($param->nama_parameter == 'Q_Floodway_Gabungan'){
				$nilai_avg = $this->debitFloodwayGabungan($nilai_avg);
				$nilai_min = $this->debitFloodwayGabungan($nilai_min);
				$nilai_max = $this->debitFloodwayGabungan($nilai_max);
			}elseif($param->nama_parameter == 'Debit_Gabungan'){
				$nilai_avg = $this->debitGabungan($nilai_avg );	
				$nilai_min = $this->debitGabungan($nilai_min);	
				$nilai_max = $this->debitGabungan($nilai_max );
			}elseif($param->nama_parameter == 'Q_Scouring'){
				$nilai_avg = $this->debitScouring($nilai_avg );	
				$nilai_min = $this->debitScouring($nilai_min);	
				$nilai_max = $this->debitScouring($nilai_max );
			}
			$waktu[] = date('Y-m-d H:00',strtotime($d->waktu));
			$data[] = number_format($nilai_avg,2,'.','');
			$min[] = number_format($nilai_min,2,'.','');
			$max[] = number_format($nilai_max,2,'.','');
		}

		echo json_encode([
			'status' => $hsl ? 'sukses' : 'error',
			'data' => $hsl ? [
				'status'=>'sukses',
				'idLogger'=>$idlogger,
				'nosensor'=>$sensor,
				'namaSensor'=>$namaSensor,
				'satuan'=>$satuan,
				'waktu'=>$waktu,
				'tipegraf'=>$tipegraf,
				'data'=>$data,
				'datamin'=>$min,
				'datamax'=>$max
			] : null
		]);
	}

	function analisaperbulan()
	{
		$idsensor = $this->input->get('idsensor');
		$tabel = $this->input->get('tabel');
		$tanggal = $this->input->get('tanggal');

		$data = array();
		$min = array();
		$max = array();
		$waktu = [];
		$jenis = $this->input->get('jenis');
		if($jenis == 'awgc'){
			$qparam = $this->db
				->select('parameter_pintu.*, t_pintu.id_logger')
				->from('parameter_pintu')
				->join('t_pintu','t_pintu.id_pintu = parameter_pintu.id_pintu')
				->where('parameter_pintu.id_param',$idsensor)
				->get();
		} else {
			$qparam = $this->db
				->select('*')
				->from('parameter_sensor')
				->where('id_param',$idsensor)
				->get();
		}
		foreach($qparam->result() as $param){
			$idlogger = ($jenis == 'awgc') ? $param->id_logger : $param->logger_id;
			$sensor = $param->kolom_sensor;
			$namaSensor = ($param->tipe_graf == 'column') 
				? 'Akumulasi_'.$param->nama_parameter 
				: 'Rerata_'.$param->nama_parameter;
			$select = ($param->tipe_graf == 'column')
				? "SUM($sensor) AS $namaSensor"
				: "AVG($sensor) AS $namaSensor";
			$satuan = $param->satuan;
			$tipegraf = $param->tipe_graf;
		}
		$query_data = $this->db->query("SELECT waktu,DATE(waktu) as tanggal," . $select . ",min(" . $sensor . ") as min,max(" . $sensor . ") as max FROM " . $tabel . " where code_logger='" . $idlogger . "' and waktu >= '" . $tanggal . "-01 00:00' and waktu <= '" . $tanggal . "-31 23:59' group by DAY(waktu),MONTH(waktu),YEAR(waktu);");
		$dbt = 0;

		$hsl = $query_data->result();

		foreach ($hsl as $datalog) {
			$nilai_avg = $datalog->$namaSensor;
			$nilai_min = $datalog->min;
			$nilai_max = $datalog->max;
			if($param->nama_parameter == 'Q_Floodway_1'){
				$nilai_avg = $this->debitPintu1($nilai_avg );	
				$nilai_min = $this->debitPintu1($nilai_min);	
				$nilai_max = $this->debitPintu1($nilai_max );
			}elseif($param->nama_parameter == 'Q_Floodway_2'){
				$nilai_avg = $this->debitPintu2($nilai_avg );	
				$nilai_min = $this->debitPintu2($nilai_min);	
				$nilai_max = $this->debitPintu2($nilai_max );
			}elseif($param->nama_parameter == 'Q_Floodway_3'){
				$nilai_avg = $this->debitPintu3($nilai_avg );	
				$nilai_min = $this->debitPintu3($nilai_min);	
				$nilai_max = $this->debitPintu3($nilai_max );
			}elseif($param->nama_parameter == 'Q_Floodway_Gabungan'){
				$nilai_avg = $this->debitFloodwayGabungan($nilai_avg);
				$nilai_min = $this->debitFloodwayGabungan($nilai_min);
				$nilai_max = $this->debitFloodwayGabungan($nilai_max);
			}elseif($param->nama_parameter == 'Debit_Gabungan'){
				$nilai_avg = $this->debitGabungan($nilai_avg );	
				$nilai_min = $this->debitGabungan($nilai_min);	
				$nilai_max = $this->debitGabungan($nilai_max );
			}elseif($param->nama_parameter == 'Q_Scouring'){
				$nilai_avg = $this->debitScouring($nilai_avg );	
				$nilai_min = $this->debitScouring($nilai_min);	
				$nilai_max = $this->debitScouring($nilai_max );
			}
			$waktu[] = date('Y-m-d', strtotime($datalog->waktu));
			$data[] = number_format($nilai_avg, 2,'.','');
			$min[] = number_format($nilai_min, 2,'.','');
			$max[] = number_format($nilai_max, 2,'.','');
		}

		if ($hsl) {
			$stts = 'sukses';
			$dataAnalisa = array(
				'status' => 'sukses',
				'idLogger' => $idlogger,
				'nosensor' => $sensor,
				'namaSensor' => $namaSensor,
				'satuan' => $satuan,
				'waktu' => $waktu,
				'tipegraf' => $param->tipe_graf,
				'data' => $data,
				'datamin' => $min,
				'datamax' => $max,
			);
		} else {
			$stts = 'error';
			$dataAnalisa = null;
		}

		echo json_encode(
			array(
				'status' => $stts,
				'data' => $dataAnalisa
			)
		);
	}


	function analisaperrange()
	{
		$idsensor = $this->input->get('idsensor');
		$tabel = $this->input->get('tabel');
		$awal = $this->input->get('awal');
		$akhir = $this->input->get('akhir');
		$jenis = $this->input->get('jenis');

		if($jenis == 'awgc'){
			$qparam = $this->db
				->select('parameter_pintu.*, t_pintu.id_logger')
				->from('parameter_pintu')
				->join('t_pintu','t_pintu.id_pintu = parameter_pintu.id_pintu')
				->where('parameter_pintu.id_param',$idsensor)
				->get();
		} else {
			$qparam = $this->db
				->select('*')
				->from('parameter_sensor')
				->where('id_param',$idsensor)
				->get();
		}

		foreach($qparam->result() as $param){
			$idlogger = ($jenis == 'awgc') ? $param->id_logger : $param->logger_id;
			$sensor = $param->kolom_sensor;
			$namaSensor = ($param->tipe_graf == 'column') 
				? 'Akumulasi_'.$param->nama_parameter 
				: 'Rerata_'.$param->nama_parameter;
			$select = ($param->tipe_graf == 'column')
				? "SUM($sensor) AS $namaSensor"
				: "AVG($sensor) AS $namaSensor";
			$satuan = $param->satuan;
			$tipegraf = $param->tipe_graf;
		}

		$query_data = $this->db->query("SELECT waktu,DATE(waktu) as tanggal," . $select . ",min(" . $sensor . ") as min,max(" . $sensor . ") as max FROM " . $tabel . " where code_logger='" . $idlogger . "' and waktu >='" . $awal . "' and waktu <='" . $akhir . " 23:59:00' group by HOUR(waktu),DAY(waktu),MONTH(waktu),YEAR(waktu) order by waktu asc;");
		$dbt = 0;
		$hsl = $query_data->result();

		foreach ($hsl as $datalog) {
			$nilai_avg = $datalog->$namaSensor;
			$nilai_min = $datalog->min;
			$nilai_max = $datalog->max;
			if($param->nama_parameter == 'Q_Floodway_1'){
				$nilai_avg = $this->debitPintu1($nilai_avg );	
				$nilai_min = $this->debitPintu1($nilai_min);	
				$nilai_max = $this->debitPintu1($nilai_max );
			}elseif($param->nama_parameter == 'Q_Floodway_2'){
				$nilai_avg = $this->debitPintu2($nilai_avg );	
				$nilai_min = $this->debitPintu2($nilai_min);	
				$nilai_max = $this->debitPintu2($nilai_max );
			}elseif($param->nama_parameter == 'Q_Floodway_3'){
				$nilai_avg = $this->debitPintu3($nilai_avg );	
				$nilai_min = $this->debitPintu3($nilai_min);	
				$nilai_max = $this->debitPintu3($nilai_max );
			}elseif($param->nama_parameter == 'Q_Floodway_Gabungan'){
				$nilai_avg = $this->debitFloodwayGabungan($nilai_avg);
				$nilai_min = $this->debitFloodwayGabungan($nilai_min);
				$nilai_max = $this->debitFloodwayGabungan($nilai_max);
			}elseif($param->nama_parameter == 'Debit_Gabungan'){
				$nilai_avg = $this->debitGabungan($nilai_avg );	
				$nilai_min = $this->debitGabungan($nilai_min);	
				$nilai_max = $this->debitGabungan($nilai_max );
			}elseif($param->nama_parameter == 'Q_Scouring'){
				$nilai_avg = $this->debitScouring($nilai_avg );	
				$nilai_min = $this->debitScouring($nilai_min);	
				$nilai_max = $this->debitScouring($nilai_max );
			}
			$waktu[] = date('Y-m-d H', strtotime($datalog->waktu)) . ':00';
			$data[] = number_format($nilai_avg, 2,'.','');
			$min[] = number_format($nilai_min, 2,'.','');
			$max[] = number_format($nilai_max, 2,'.','');

		}
		if (!$hsl) {
			$stts = 'error';
			$dataAnalisa = null;
		} else {
			$stts = 'sukses';
			$dataAnalisa = array(
				'status' => 'sukses',
				'idLogger' => $idlogger,
				'nosensor' => $sensor,
				'namaSensor' => $namaSensor,
				'satuan' => $satuan,
				'waktu' => $waktu,
				'tipegraf' => $param->tipe_graf,
				'data' => $data,
				'datamin' => $min,
				'datamax' => $max,
			);
		}

		echo json_encode(
			array(
				'status' => $stts,
				'data' => $dataAnalisa
			)
		);
	}

	function analisapertahun()
	{
		$idsensor = $this->input->get('idsensor');
		$tabel = $this->input->get('tabel');
		$tanggal = $this->input->get('tanggal');
		$jenis = $this->input->get('jenis');

		if($jenis == 'awgc'){
			$qparam = $this->db
				->select('parameter_pintu.*, t_pintu.id_logger')
				->from('parameter_pintu')
				->join('t_pintu','t_pintu.id_pintu = parameter_pintu.id_pintu')
				->where('parameter_pintu.id_param',$idsensor)
				->get();
		} else {
			$qparam = $this->db
				->select('*')
				->from('parameter_sensor')
				->where('id_param',$idsensor)
				->get();
		}

		foreach($qparam->result() as $param){
			$idlogger = ($jenis == 'awgc') ? $param->id_logger : $param->logger_id;
			$sensor = $param->kolom_sensor;
			$namaSensor = ($param->tipe_graf == 'column') 
				? 'Akumulasi_'.$param->nama_parameter 
				: 'Rerata_'.$param->nama_parameter;
			$select = ($param->tipe_graf == 'column')
				? "SUM($sensor) AS $namaSensor"
				: "AVG($sensor) AS $namaSensor";
			$satuan = $param->satuan;
			$tipegraf = $param->tipe_graf;
		}

		$query_data = $this->db->query("SELECT waktu,DATE(waktu) as tanggal,MONTH(waktu) as bulan," . $select . ",min(" . $sensor . ") as min,max(" . $sensor . ") as max FROM " . $tabel . " where code_logger='" . $idlogger . "' and waktu >= '" . $tanggal . "-01-01 00:00' and waktu <= '" . $tanggal . "-12-31 23:59' group by MONTH(waktu),YEAR(waktu);");
		$dbt = 0;
		foreach ($query_data->result() as $datalog) {
			$nilai_avg = $datalog->$namaSensor;
			$nilai_min = $datalog->min;
			$nilai_max = $datalog->max;
			if($param->nama_parameter == 'Q_Floodway_1'){
				$nilai_avg = $this->debitPintu1($nilai_avg );	
				$nilai_min = $this->debitPintu1($nilai_min);	
				$nilai_max = $this->debitPintu1($nilai_max );
			}elseif($param->nama_parameter == 'Q_Floodway_2'){
				$nilai_avg = $this->debitPintu2($nilai_avg );	
				$nilai_min = $this->debitPintu2($nilai_min);	
				$nilai_max = $this->debitPintu2($nilai_max );
			}elseif($param->nama_parameter == 'Q_Floodway_3'){
				$nilai_avg = $this->debitPintu3($nilai_avg );	
				$nilai_min = $this->debitPintu3($nilai_min);	
				$nilai_max = $this->debitPintu3($nilai_max );
			}elseif($param->nama_parameter == 'Q_Floodway_Gabungan'){
				$nilai_avg = $this->debitFloodwayGabungan($nilai_avg);
				$nilai_min = $this->debitFloodwayGabungan($nilai_min);
				$nilai_max = $this->debitFloodwayGabungan($nilai_max);
			}elseif($param->nama_parameter == 'Debit_Gabungan'){
				$nilai_avg = $this->debitGabungan($nilai_avg );	
				$nilai_min = $this->debitGabungan($nilai_min);	
				$nilai_max = $this->debitGabungan($nilai_max );
			}elseif($param->nama_parameter == 'Q_Scouring'){
				$nilai_avg = $this->debitScouring($nilai_avg );	
				$nilai_min = $this->debitScouring($nilai_min);	
				$nilai_max = $this->debitScouring($nilai_max );
			}
			$waktu[] = date('Y-m', strtotime($datalog->waktu));
			$data2[] = number_format($nilai_avg, 2,'.','');
			$min2[] = number_format($nilai_min, 2,'.','');
			$max2[] = number_format($nilai_max, 2,'.','');
		}

		if (!$query_data->result_array()) {
			$stts = 'error';
			$dataAnalisa = null;
		} else {
			$stts = 'sukses';
			$dataAnalisa = array(
				'status' => 'sukses',
				'idLogger' => $idlogger,
				'nosensor' => $sensor,
				'namaSensor' => $namaSensor,
				'satuan' => $satuan,
				'waktu' => $waktu,
				'tipegraf' => $param->tipe_graf,
				'data' => $data2,
				'datamin' => $min2,
				'datamax' => $max2,
			);
		}

		echo json_encode(
			array(
				'status' => $stts,
				'data' => $dataAnalisa
			)
		);
	}


	function infov2()
	{
		$skr2 = date('Y-m-d H:i', mktime(0, 0, 0, date('m'), date('d') - 1, date('Y')));

		$idlogger = $this->input->get('idlogger');
		$data_informasi = array();
		$data_terakhir = array();
		$query = $this->db->query('SELECT * from kategori_logger INNER JOIN t_logger on t_logger.kategori_log = kategori_logger.id_katlogger;')->row();

		$tabel = $query->temp_data;

		$status_sd = 'OK';
		$query_informasi = $this->db->query('SELECT * FROM t_informasi where logger_id="' . $idlogger . '"');
		foreach ($query_informasi->result() as $data) {
			$query_logger = $this->db->query('SELECT * FROM t_logger where id_logger="' . $idlogger . '"');
			foreach ($query_logger->result() as $logger) {
				$query_kategori = $this->db->query('SELECT * FROM kategori_logger where id_katlogger="' . $logger->kategori_log . '"');
				foreach ($query_kategori->result() as $kategori) {
					if($kategori->controller=='awgc'){
						$query_ceksd = $this->db->query('SELECT sensor4, sensor54, sensor55,  sensor53  FROM ' . $kategori->temp_data . ' where code_logger="' . $idlogger . '" order by waktu desc limit 1')->row();

						if ($query_ceksd->sensor55 == '1') {
							$status_sd = 'OK';
						} else {
							$status_sd = 'Terjadi Kesalahan';
						}
						if ($query_ceksd->sensor53 == '1') {
							$status_mux = 'OK';
						} else {
							$status_mux = 'Terjadi Kesalahan';
						}
						$data_informasi = array(
							array(
								'nama' => 'ID Logger', 'nilai' => $data->logger_id
							),
							array('nama' =>
								  'Seri', 'nilai' => $data->seri),
							array('nama' => 'Sensor', 'nilai' => $data->sensor),
							array('nama' => 'Serial Number', 'nilai' => $data->serial_number),
							array('nama' =>
								  'Status SD', 'nilai' => $status_sd),
							array('nama' =>
								  'Awal Kontrak', 'nilai' => $data->tgl_kontrak),
							array('nama' =>
								  'Akhir Garansi', 'nilai' => $data->garansi),
							array('nama' =>
								  'Logger Aktif', 'nilai' => $data->tgl_aktif),
							array('nama' =>
								  'No Seluler', 'nilai' => $data->nosell),
						);

					}else{
						$query_ceksd = $this->db->query('SELECT sensor13  FROM ' . $kategori->temp_data . ' where code_logger="' . $idlogger . '" order by waktu desc limit 1')->row();

						if ($query_ceksd->sensor13 == '1') {
							$status_sd = 'OK';
						} else {
							$status_sd = 'Terjadi Kesalahan';
						}
						if (empty($data->elevasi)) {
							$data_informasi = array(
								array(
									'nama' => 'ID Logger', 'nilai' => $data->logger_id
								),
								array('nama' =>
									  'Seri', 'nilai' => $data->seri),
								array('nama' =>
									  'Sensor', 'nilai' => $data->sensor),
								array('nama' =>
									  'Status SD', 'nilai' => $status_sd),
								array('nama' =>
									  'Awal Kontrak', 'nilai' => $data->tgl_kontrak),
								array('nama' =>
									  'Akhir Garansi', 'nilai' => $data->garansi),
								array('nama' =>
									  'Logger Aktif', 'nilai' => $data->tgl_aktif),
								array('nama' =>
									  'No Seluler', 'nilai' => $data->nosell),
								array('nama' =>
									  'IMEI', 'nilai' => $data->imei)
							);
						} else {
							$data_informasi = array(
								array(
									'nama' => 'ID Logger', 'nilai' => $data->logger_id
								),
								array('nama' =>
									  'Seri', 'nilai' => $data->seri),
								array('nama' => 'Sensor', 'nilai' => $data->sensor),
								array('nama' =>
									  'Status SD', 'nilai' => $status_sd),
								array('nama' =>
									  'Awal Kontrak', 'nilai' => $data->tgl_kontrak),
								array('nama' =>
									  'Akhir Garansi', 'nilai' => $data->garansi),
								array('nama' =>
									  'Logger Aktif', 'nilai' => $data->tgl_aktif),
								array('nama' => 'Elevasi', 'nilai' => $data->elevasi),
								array('nama' =>
									  'No Seluler', 'nilai' => $data->nosell),
								array('nama' =>
									  'IMEI', 'nilai' => $data->imei),
							);
						}
					}
				}
			}


		}
		$data_terakhir = array(
			'data' => $data_informasi,
			//'elevasi'=>$data->elevasi
		);

		echo json_encode($data_terakhir);
	}

	public function lanjut_kontrol(){
		$kode_akses = $this->db->get('kode_akses')->row();
		$inp = md5($this->input->post('kode_akses'));
		$server = 'mqtt.beacontelemetry.com';
		$port = 8883;
		$username = 'userlog';
		$password = 'b34c0n';
		$client_id = 'bemqtt-tes';
		$ca = "/etc/ssl/certs/ca-bundle.crt";
		$mqtt = new phpMQTT($server, $port, $client_id, $ca);
		if($kode_akses->kode_akses != $inp){
			echo json_encode(['status'=>'error']);
		}else{
			$s = [];
			$data = $this->input->post('daftar_kontrol');
			$encData = json_decode($data);
			$nama_pintu = '';
			$id_logger = '';
			$this->load->helper('gcm');
			$gcm_cmds = [];
			foreach ($encData as $row) {
				$pintu = $this->db
					->where('id_pintu', $row->id_pintu)
					->get('t_pintu')
					->row();

				if (!$pintu) continue;

				$id_logger = $pintu->id_logger;
				$gcm = $pintu->mqtt_identifier;
				$level = $row->level;
				$nama_pintu = $pintu->nama_pintu;
				$s[] = [
					'id_pintu'=>$row->id_pintu,
					'set_value'=>$level,
					'status'=>'1'
				];
				$map = gcm_lookup($gcm);
				if ($map) {
					$gcm_cmds[] = [
						'topic'   => gcm_topic($map['logger']),
						'payload' => gcm_gate_set_payload($map['id'], $level),
					];
				}
			}

			$send_kontrol = [
				'status_kontrol'=>'1',
				'waktu'=>date('Y-m-d'),
				'session_id'=>date('Ymdhis'),
			];
			if ($s) { $this->db->update_batch('set_tempkontrol',$s, 'id_pintu'); }

			$this->db->where('id_logger',$this->input->post('id_logger'));
			$this->db->update('status_kontrol',$send_kontrol);
			if ($mqtt->connect(true, NULL, $username, $password)) {
				// Format GCM baru: GCM_GATE SET per pintu -> sub_<id_logger>.
				// Horn pre-warning ditangani firmware (GCM_GATE_WARN), tidak lagi via web.
				$mqtt->publish('kontrol_pintu-'.$id_logger, json_encode($send_kontrol), 0, false);
				foreach ($gcm_cmds as $cmd) {
					$mqtt->publish($cmd['topic'], $cmd['payload'], 0);
				}
				$mqtt->close();
			} else {
				echo "Time out!\n";
			}
			echo json_encode(['status'=>'success']);
		}

	}

	function tes_token () {
		$serviceAccount = json_decode(file_get_contents('https://leuwigoong.beacontelemetry.com/unduh/copong-783f8-cfe02d37fd4c.json'), true);
		$this->load->helper('jwt');
		$now_seconds = time();
		$payload = array(
			"iss" => $serviceAccount['client_email'],
			"sub" => $serviceAccount['client_email'],
			"aud" => "https://oauth2.googleapis.com/token",
			"iat" => $now_seconds,
			"exp" => $now_seconds + 3600,
			"scope" => "https://www.googleapis.com/auth/firebase.messaging"
		);

		$header = [
			'alg' => 'RS256',
			'typ' => 'JWT'
		];
		$privateKey = $serviceAccount['private_key'];
		$jwt = createJWT($header, $payload, $privateKey);
		return $jwt;
	}

	function getAccessToken() {
		$jwt= $this->tes_token();
		$url = 'https://oauth2.googleapis.com/token';
		$data = [
			'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
			'assertion' => $jwt
		];

		$options = [
			'http' => [
				'header' => 'Content-Type: application/x-www-form-urlencoded',
				'method' => 'POST',
				'content' => http_build_query($data)
			]
		];

		$context = stream_context_create($options);
		$result = file_get_contents($url, false, $context);

		if ($result === FALSE) {
			throw new Exception('Error obtaining access token.');
		}

		$response = json_decode($result, true);

		if (isset($response['error'])) {
			throw new Exception('Error obtaining access token: ' . $response['error']);
		}

		return $response['access_token'];
	}


	public function notif_aplikasi($nama_pintu){	
		$accessToken = $this->getAccessToken();
		$headers = [
			'Authorization: Bearer ' . $accessToken,
			'Content-Type: application/json'
		];

		$message = [
			"message" => [
				"topic"=> "kontrol_leuwigoong",
				"notification" => [
					"title" => 'AWGC Sedang Digunakan',
					"body" => $nama_pintu. " sedang beroperasi",
				],
			]
		];

		$options = [
			'http' => [
				'method'  => 'POST',
				'header'  => implode("\r\n", $headers),
				'content' => json_encode($message),
				'ignore_errors' => true
			]
		];
		$fcmUrl = 'https://fcm.googleapis.com/v1/projects/copong-783f8/messages:send'; 

		$context  = stream_context_create($options);
		$response = file_get_contents($fcmUrl, false, $context);

		echo $response;
	}

	public function lanjut_kontrol2(){
		$kode_akses = $this->db->get('kode_akses')->row();
		$inp = md5($this->input->post('kode_akses'));
		$server = 'mqtt.beacontelemetry.com';
		$port = 8883;
		$username = 'userlog';
		$password = 'b34c0n';
		$client_id = 'bemqtt-tes';
		$ca = "/etc/ssl/certs/ca-bundle.crt";
		$mqtt = new phpMQTT($server, $port, $client_id, $ca);
		if($kode_akses->kode_akses != $inp){
			echo json_encode(['status'=>'error']);
		}else{
			$s = [];
			$data = $this->input->post('daftar_kontrol');
			$encData = json_decode($data);
			$nama_pintu = '';
			$id_logger = '';
			$this->load->helper('gcm');
			$gcm_cmds = [];
			foreach ($encData as $row) {
				$pintu = $this->db
					->where('id_pintu', $row->id_pintu)
					->get('t_pintu')
					->row();

				if (!$pintu) continue;

				$id_logger = $pintu->id_logger;
				$gcm = $pintu->mqtt_identifier;
				$level = $row->level;
				$nama_pintu = $pintu->nama_pintu;
				$s[] = [
					'id_pintu'=>$row->id_pintu,
					'set_value'=>$level,
					'status'=>'1'
				];
				$map = gcm_lookup($gcm);
				if ($map) {
					$gcm_cmds[] = [
						'topic'   => gcm_topic($map['logger']),
						'payload' => gcm_gate_set_payload($map['id'], $level),
					];
				}
			}

			$send_kontrol = [
				'status_kontrol'=>'1',
				'waktu'=>date('Y-m-d'),
				'session_id'=>date('Ymdhis'),
			];
			if ($s) { $this->db->update_batch('set_tempkontrol',$s, 'id_pintu'); }

			$this->db->where('id_logger',$this->input->post('id_logger'));
			$this->db->update('status_kontrol',$send_kontrol);
			if ($mqtt->connect(true, NULL, $username, $password)) {
				// Format GCM baru: GCM_GATE SET per pintu -> sub_<id_logger>.
				// Horn pre-warning ditangani firmware (GCM_GATE_WARN), tidak lagi via web.
				$mqtt->publish('kontrol_pintu-'.$id_logger, json_encode($send_kontrol), 0, false);
				foreach ($gcm_cmds as $cmd) {
					$mqtt->publish($cmd['topic'], $cmd['payload'], 0);
				}
				$mqtt->close();
			} else {
				echo "Time out!\n";
			}
			echo json_encode(['status'=>'success']);
		}

	}
}
