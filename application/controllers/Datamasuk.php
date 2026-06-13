<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Datamasuk extends CI_Controller
{
	function __construct()
	{
		parent::__construct();

		$this->load->model('m_inputdata');
		$this->load->library('PhpMQTT');
	}
	public function index()
	{
		if (empty($this->session->userdata('tgl_search'))) {
			$tgl = date('Y-m-d');
			$this->session->set_userdata('tgl_search', $tgl);
		}
		$id_logger = $this->session->userdata('log_id');
		$data['list_logger'] = $this->db->get('t_logger')->result_array();

		$ky = [];

		$tabel = $this->db->join('kategori_logger', 'kategori_logger.id_katlogger = t_logger.kategori_log')->where('t_logger.id_logger', $id_logger)->get('t_logger')->row();

		if ($tabel) {
			$data['data'] = $this->db->query('SELECT * FROM ' . $tabel->tabel . ' where code_logger="' . $id_logger . '" and waktu >= "' . $this->session->userdata('tgl_search') . ' 00:00" and waktu <= "' . $this->session->userdata('tgl_search') . ' 23:59" ORDER BY waktu desc')->result_array();
			$data['tabel'] = $tabel->tabel;
			if($data['data']){
				foreach ($data['data'][0] as $key => $vl) {
					$ky[] = ['key'=>$key];
				}
				$data['key'] = $ky;
			}else{
				$data['key'] = $ky;
			}

			$data20 =  $this->db->query('select count(DISTINCT waktu) as waktu from '.$tabel->tabel.' where code_logger="'.$this->session->userdata('log_id').'" and waktu >= "'.  $this->session->userdata('tgl_search').'  00:00" and  waktu <= "'.  $this->session->userdata('tgl_search').'  23:59" ')->row();
			$current_time = time();
			$current_minute = date('i', $current_time);
			$total_minutes = ((int)date('H', $current_time) * 60) + (int)$current_minute;
			$data_count = $data20->waktu;
			if ($this->session->userdata('tgl_search') == date('Y-m-d')) {
				$tgl = date('Y-m-d H:i');

				if ($data_count > $total_minutes) {
					$data_count = $total_minutes;
				}
				$res = number_format(($data_count / $total_minutes * 100), 2);
				$res2 = $res . ' %';
			} else {
				$tgl = $this->session->userdata('tgl_search');
				$total_minutes = 1440;
				$res = number_format(($data_count / 1440 * 100), 2);
				$res2 = $res . ' %';
			}
			$data['data_count'] = $data_count;
			$data['total_minutes'] = $total_minutes;
		} else {
			$data['data'] = array();
			$data['tabel'] = null;
			$data['data_count'] = 0;
			$data['total_minutes'] = 0;
		}
		if($ky){
			foreach($data['key'] as $k=> $vl){
				$param = $this->db->where('kolom_sensor',$vl['key'])->where('logger_id',$this->session->userdata('log_id'))->get('parameter_sensor')->row();

				if($tabel->tabel == 'awgc'){
					$p_pintu = $this->db->where('kolom_sensor',$vl['key'])->get('parameter_pintu')->row();
					if($p_pintu){
						$data['key'][$k]['nama'] = $p_pintu->nama_parameter;
					}else{
						$data['key'][$k]['nama'] = '';	
					}
				}else{
					if($param){
						$data['key'][$k]['nama'] = $param->nama_parameter;
					}else{
						$data['key'][$k]['nama'] = '';	
					}
				}
			}

		}else{
			$data['key'] = $ky;
		}
		$this->load->view('konten/inputdata/view_awgc', $data);
	}


	public function sesi_logger()
	{
		$this->session->set_userdata('log_id', $this->input->post('logger_id'));
		redirect('datamasuk');
	}

	function tgl_search()
	{
		$date = date_create($this->input->post('tgl'));
		$tgl = date_format($date, "Y-m-d");
		$this->session->set_userdata('tgl_search', $tgl);
		redirect('datamasuk');
	}

	public function data_awgc()
	{
		if (empty($this->session->userdata('tgl_awgc'))) {
			$tgl = date('Y-m-d');
			$this->session->set_userdata('tgl_awgc', $tgl);
		}
		$ky = [];
		$data['data_awgc'] = $this->db->query('SELECT * FROM awgc where code_logger="' . $this->session->userdata('log_awgc') . '" and waktu like "' . $this->session->userdata('tgl_awgc') . '%" ORDER BY waktu desc')->result_array();
		if($data['data_awgc']){
			foreach ($data['data_awgc'][0] as $key => $vl) {
				$ky[] = ['key'=>$key];
			}
			$data['key'] = $ky;
		}else{
			$data['key'] = $ky;
		}

		foreach($data['key'] as $k=> $vl){
			$param = $this->db->where('kolom_sensor',$vl['key'])->get('parameter_pintu')->row();
			if($param){
				$data['key'][$k]['nama'] = $param->nama_parameter;
			}else{
				$param2 = $this->db->where('kolom_sensor',$vl['key'])->get('parameter_sensor')->row();
				if($param2){
					$data['key'][$k]['nama'] = $param2->nama_parameter;	
				}else{
					$data['key'][$k]['nama'] = '';	
				}
			}

		}

		$this->load->view('konten/inputdata/view_awgc', $data);
	}
	
	public function add_baterai()
	{
		$tgl=GETDATE();
		$tanggal = $this->input->post('tanggal');
		$jam = $this->input->post('jam');
		$waktu = $tanggal.' '.$jam;

		$data = array (
			'code_logger'=>$this->input->post('id_alat'),
			//'user_id'=>$this->input->post('user_id'),
			'waktu'=>$waktu,
			'sensor1'=>$this->input->post('sensor1'),
			'sensor2'=>$this->input->post('sensor2'),
			'sensor3'=>$this->input->post('sensor3'),
			'sensor4'=>$this->input->post('sensor4'),
			'sensor5'=>$this->input->post('sensor5'),
			'sensor6'=>$this->input->post('sensor6'),
			'sensor7'=>$this->input->post('sensor7'),
			'sensor8'=>$this->input->post('sensor8'),
			'sensor9'=>$this->input->post('sensor9'),
			'sensor10'=>$this->input->post('sensor10'),
			'sensor11'=>$this->input->post('sensor11'),
			'sensor12'=>$this->input->post('sensor12'),
			'sensor13'=>$this->input->post('sensor13'),
			'sensor14'=>$this->input->post('sensor14'),
			'sensor15'=>$this->input->post('sensor15'),
			'sensor16'=>$this->input->post('sensor16'),
		);
		
		$this->db->insert('baterai',$data);
		$this->db->where('code_logger',$this->input->post('id_alat'))->update('temp_baterai',$data);
		$query_inf=$this->db->query('select serial_number from t_informasi where logger_id = "'.$this->input->post('id_alat').'"');
		foreach($query_inf->result() as $inf)
		{

			if($inf->serial_number != $this->input->post('sn'))
			{
				$updata_inf = array(
					'serial_number'=>$this->input->post('sn'),

				);
				$this->db->where('logger_id', $this->input->post('id_alat'));
				$this->db->update('t_informasi', $updata_inf);
			}
		}
		
	}
	
	public function add_ipcam()
	{
		$tgl=GETDATE();
		$tanggal = $this->input->post('tanggal');
		$jam = $this->input->post('jam');
		$waktu = $tanggal.' '.$jam;

		$data = array (
			'code_logger'=>$this->input->post('id_alat'),
			//'user_id'=>$this->input->post('user_id'),
			'waktu'=>$waktu,
			'sensor1'=>$this->input->post('sensor1'),
			'sensor2'=>$this->input->post('sensor2'),
			'sensor3'=>$this->input->post('sensor3'),
			'sensor4'=>$this->input->post('sensor4'),
			'sensor5'=>$this->input->post('sensor5'),
			'sensor6'=>$this->input->post('sensor6'),
			'sensor7'=>$this->input->post('sensor7'),
			'sensor8'=>$this->input->post('sensor8'),
			'sensor9'=>$this->input->post('sensor9'),
			'sensor10'=>$this->input->post('sensor10'),
			'sensor11'=>$this->input->post('sensor11'),
			'sensor12'=>$this->input->post('sensor12'),
			'sensor13'=>$this->input->post('sensor13'),
			'sensor14'=>$this->input->post('sensor14'),
			'sensor15'=>$this->input->post('sensor15'),
			'sensor16'=>$this->input->post('sensor16'),
		);
		
		$this->db->insert('ipcam',$data);
		$this->db->where('code_logger',$this->input->post('id_alat'))->update('temp_ipcam',$data);
		$query_inf=$this->db->query('select serial_number from t_informasi where logger_id = "'.$this->input->post('id_alat').'"');
		foreach($query_inf->result() as $inf)
		{

			if($inf->serial_number != $this->input->post('sn'))
			{
				$updata_inf = array(
					'serial_number'=>$this->input->post('sn'),

				);
				$this->db->where('logger_id', $this->input->post('id_alat'));
				$this->db->update('t_informasi', $updata_inf);
			}
		}
		
	}
	
	public function add_awlr()
	{
		$tgl=GETDATE();
		$tanggal = $this->input->post('tanggal');
		$jam = $this->input->post('jam');
		$waktu = $tanggal.' '.$jam;

		$data = array (
			'code_logger'=>$this->input->post('id_alat'),
			//'user_id'=>$this->input->post('user_id'),
			'waktu'=>$waktu,
			'sensor1'=>$this->input->post('sensor1'),
			'sensor2'=>$this->input->post('sensor2'),
			'sensor3'=>$this->input->post('sensor3'),
			'sensor4'=>$this->input->post('sensor4'),
			'sensor5'=>$this->input->post('sensor5'),
			'sensor6'=>$this->input->post('sensor6'),
			'sensor7'=>$this->input->post('sensor7'),
			'sensor8'=>$this->input->post('sensor8'),
			'sensor9'=>$this->input->post('sensor9'),
			'sensor10'=>$this->input->post('sensor10'),
			'sensor11'=>$this->input->post('sensor11'),
			'sensor12'=>$this->input->post('sensor12'),
			'sensor13'=>$this->input->post('sensor13'),
			'sensor14'=>$this->input->post('sensor14'),
			'sensor15'=>$this->input->post('sensor15'),
			'sensor16'=>$this->input->post('sensor16'),
		);
		
		$this->db->insert('awlr',$data);
		$this->db->where('code_logger',$this->input->post('id_alat'))->update('temp_awlr',$data);
		
		$query_inf=$this->db->query('select serial_number from t_informasi where logger_id = "'.$this->input->post('id_alat').'"');
		foreach($query_inf->result() as $inf)
		{

			if($inf->serial_number != $this->input->post('sn'))
			{
				$updata_inf = array(
					'serial_number'=>$this->input->post('sn'),

				);
				$this->db->where('logger_id', $this->input->post('id_alat'));
				$this->db->update('t_informasi', $updata_inf);
			}
		}
		
	}
	
	function tgl_awgc()
	{
		$date = date_create($this->input->post('tgl'));
		$tgl = date_format($date, "Y-m-d");
		$this->session->set_userdata('tgl_awgc', $tgl);
		redirect('datamasuk/data_awgc');
	}

	public function add_awgc()
	{
		$tgl = GETDATE();
		$tanggal = $this->input->post('tanggal');
		$jam = $this->input->post('jam');
		$waktu = $tanggal . ' ' . $jam;

		$data = array(
			'code_logger' => $this->input->post('id_alat'),
			//'user_id'=>$this->input->post('user_id'),
			'waktu' => $waktu,
			'sensor1' => $this->input->post('sensor1'),
			'sensor2' => $this->input->post('sensor2'),
			'sensor3' => $this->input->post('sensor3'),
			'sensor4' => $this->input->post('sensor4'),
			'sensor5' => $this->input->post('sensor5'),
			'sensor6' => $this->input->post('sensor6'),
			'sensor7' => $this->input->post('sensor7'),
			'sensor8' => $this->input->post('sensor8'),
			'sensor9' => $this->input->post('sensor9'),
			'sensor10' => $this->input->post('sensor10'),
			'sensor11' => $this->input->post('sensor11'),
			'sensor12' => $this->input->post('sensor12'),
			'sensor13' => $this->input->post('sensor13'),
			'sensor14' => $this->input->post('sensor14'),
			'sensor15' => $this->input->post('sensor15'),
			'sensor16' => $this->input->post('sensor16'),
			'sensor17' => $this->input->post('sensor17'),
			'sensor18' => $this->input->post('sensor18'),
			'sensor19' => $this->input->post('sensor19'),
			'sensor20' => $this->input->post('sensor20'),
			'sensor21' => $this->input->post('sensor21'),
			'sensor22' => $this->input->post('sensor22'),
			'sensor23' => $this->input->post('sensor23'),
			'sensor24' => $this->input->post('sensor24'),
			'sensor25' => $this->input->post('sensor25'),
			'sensor26' => $this->input->post('sensor26'),
			'sensor27' => $this->input->post('sensor27'),
			'sensor28' => $this->input->post('sensor28'),
			'sensor29' => $this->input->post('sensor29'),
			'sensor30' => $this->input->post('sensor30'),
			'sensor31' => $this->input->post('sensor31'),
			'sensor32' => $this->input->post('sensor32'),
			'sensor33' => $this->input->post('sensor33'),
			'sensor34' => $this->input->post('sensor34'),
			'sensor35' => $this->input->post('sensor35'),
			'sensor36' => $this->input->post('sensor36'),
			'sensor37' => $this->input->post('sensor37'),
			'sensor38' => $this->input->post('sensor38'),
			'sensor39' => $this->input->post('sensor39'),
			'sensor40' => $this->input->post('sensor40'),
			'sensor41' => $this->input->post('sensor41'),
			'sensor42' => $this->input->post('sensor42'),
			'sensor43' => $this->input->post('sensor43'),
			'sensor44' => $this->input->post('sensor44'),
			'sensor45' => $this->input->post('sensor45'),
			'sensor46' => $this->input->post('sensor46'),
			'sensor47' => $this->input->post('sensor47'),
			'sensor48' => $this->input->post('sensor48'),
			'sensor49' => $this->input->post('sensor49'),
			'sensor50' => $this->input->post('sensor50'),
			'sensor51' => $this->input->post('sensor51'),
			'sensor52' => $this->input->post('sensor52'),
			'sensor53' => $this->input->post('sensor53'),
			'sensor54' => $this->input->post('sensor54'),
			'sensor55' => $this->input->post('sensor55'),
			'sensor56' => $this->input->post('sensor56'),
			'sensor57' => $this->input->post('sensor57'),
			'sensor58' => $this->input->post('sensor58'),
		);

		$data_temp = $this->db->where('code_logger', $this->input->post('id_alat'))->get('temp_awgc')->row();
		$level_gate = $this->db->where('id_logger', $this->input->post('id_alat'))->get('t_pintu')->result_array();

		foreach ($level_gate as $key => $vl) {
			$sensor_level = $vl['sensor_level'];
			$nilai = $data_temp->$sensor_level;
			$nilai_masuk = $data[$sensor_level];
			if ($nilai_masuk != $nilai) {
				if ($nilai_masuk < $nilai) {
					$status_pintu = 'Tutup';
				} else {
					$status_pintu = 'Buka';
				}
				$log_data = [
					'id_logger' => $this->input->post('id_alat'),
					'id_pintu' => $vl['id_pintu'],
					'metode' => 'Manual - ' . $status_pintu,
					'dari' => $nilai,
					'ke' => $nilai_masuk,
					'datetime' => date('Y-m-d H:i:s'),
				];
				//$this->db->insert('log_kontrol', $log_data);
			}
		}

		$send = [];
		$this->m_inputdata->add_awgc($data);
		$this->m_inputdata->update_tempawgc($this->input->post('id_alat'), $data);

		foreach ($level_gate as $key => $vl) {
			$sensor_level = $vl['sensor_level'];
			$nilai = $data_temp->$sensor_level;
			$send[] = [
				'id_logger' => $this->input->post('id_alat'),
				'id_pintu' => $vl['id_pintu'],
				'elevasi' => $nilai
			];
		}

		$set_temp = $this->db->where('id_logger',$this->input->post('id_alat'))->get('set_tempkontrol')->result_array();
		$stts_kontrol = $this->db->where('id_logger',$this->input->post('id_alat'))->get('status_kontrol')->row();
		$wkt = $stts_kontrol->waktu;
		$dateTime = new DateTime($wkt);
		$menit_kontrol = $dateTime->format('H:i');

		$dateTime2 = new DateTime($waktu);
		$menit_masuk = $dateTime2->format('H:i');


		$nilai = [];
		foreach($set_temp as $key=>$vl){
			$sensor = $vl['sensor_kontrol'];
			$nilai_kontrol = $data[$sensor];

			if($nilai_kontrol == '0'){
				$send_db = [
					'status'=>$nilai_kontrol,
					'set_value'=>'0'
				];
				$this->db->where('id_logger',$this->input->post('id_alat'));
				$this->db->where('sensor_kontrol',$sensor);
				$this->db->update('set_tempkontrol',$send_db);
			}
			$nilai[] = $nilai_kontrol;
		}
		$def = 0;
		foreach($nilai as $v) {
			if($v != '0'){
				$def = $v;
			}
		}
		if($def == 0){
			$send_db2 = [
				'status_kontrol'=>0,
				'session_id'=>'0',
			];
			$this->db->where('id_logger',$this->input->post('id_alat'));
			$this->db->update('status_kontrol',$send_db2);
			$send_kontrol = [
				'status_kontrol'=>0,
				'id_logger'=>$this->input->post('id_alat'),
				'session_id'=>'0',
			];
		}else{
			$send_db2 = [
				'status_kontrol'=>1,
				'session_id'=>$this->session->session_id,
			];
			$this->db->where('id_logger',$this->input->post('id_alat'));
			$this->db->update('status_kontrol',$send_db2);
			$send_kontrol = [
				'status_kontrol'=>1,
				'id_logger'=>$this->input->post('id_alat'),
				'session_id'=>$this->session->session_id,
			];
		}

		echo json_encode($nilai);

		$send2 = array(
			'id_logger' => $this->input->post('id_alat'),
			'waktu' => $waktu,
		);

		$server = 'mqtt.beacontelemetry.com';
		$port = 8883;
		$username = 'userlog';
		$password = 'b34c0n';
		$client_id = 'bemqtt-tes';
		$ca = "/etc/ssl/certs/ca-bundle.crt";
		$mqtt = new phpMQTT($server, $port, $client_id, $ca);
		if ($mqtt->connect(true, NULL, $username, $password)) {
			$mqtt->publish('awgc-' . $this->input->post('id_alat'), json_encode($send2), 0, false);
			$mqtt->publish('kontrol_pintu-'. $this->input->post('id_alat'), json_encode($send_kontrol), 0, false);
			$mqtt->close();
		} else {
			echo "Time out!\n";
		}

		$query_inf=$this->db->query('select serial_number from t_informasi where logger_id = "'.$this->input->post('id_alat').'"');
		foreach($query_inf->result() as $inf)
		{

			if($inf->serial_number != $this->input->post('sn'))
			{
				$updata_inf = array(
					'serial_number'=>$this->input->post('sn'),

				);
				$this->db->where('logger_id', $this->input->post('id_alat'));
				$this->db->update('t_informasi', $updata_inf);
			}
		}

	}

	public function add_awgc_json()
	{
		$json = json_decode(file_get_contents('php://input'), true);

		if (!$json || empty($json['id_alat']) || empty($json['hari']) || empty($json['jam'])) {
			echo json_encode(array('status' => 'error', 'message' => 'Format JSON tidak valid, wajib ada id_alat, hari, dan jam'));
			return;
		}

		$id_alat = $json['id_alat'];
		$waktu = $json['hari'] . ' ' . $json['jam'];
		if (strtotime($waktu) === false) {
			echo json_encode(array('status' => 'error', 'message' => 'Format hari/jam tidak valid'));
			return;
		}

		// Logger format baru hanya mengirim 50 sensor (objek {nama, nilai, satuan}).
		// Empat sensor kesehatan logger dipetakan ke kolom lama yang sudah
		// dibaca dashboard (parameter_sensor & status SD), sisanya 1:1.
		$map = array(
			'sensor47' => 'sensor55', // Status_SD
			'sensor48' => 'sensor56', // Humi_Logger  -> Humidity_Logger
			'sensor49' => 'sensor57', // Batt_Logger  -> Battery_Logger
			'sensor50' => 'sensor58', // Temp_Logger  -> Temperature_Logger
		);

		$data = array(
			'code_logger' => $id_alat,
			'waktu' => $waktu,
		);
		for ($i = 1; $i <= 58; $i++) {
			$data['sensor' . $i] = 0;
		}
		for ($i = 1; $i <= 50; $i++) {
			$key = 'sensor' . $i;
			$kolom = isset($map[$key]) ? $map[$key] : $key;
			if (isset($json[$key]['nilai']) && is_numeric($json[$key]['nilai'])) {
				$data[$kolom] = (float) $json[$key]['nilai'];
			}
		}

		$this->m_inputdata->add_awgc($data);
		$this->m_inputdata->update_tempawgc($id_alat, $data);

		// evaluasi status kontrol per pintu, flow sama dengan add_awgc
		$set_temp = $this->db->where('id_logger', $id_alat)->get('set_tempkontrol')->result_array();
		$nilai = array();
		foreach ($set_temp as $vl) {
			$sensor = $vl['sensor_kontrol'];
			$nilai_kontrol = isset($data[$sensor]) ? $data[$sensor] : 0;
			if ($nilai_kontrol == '0') {
				$send_db = array(
					'status' => '0',
					'set_value' => '0',
				);
				$this->db->where('id_logger', $id_alat);
				$this->db->where('sensor_kontrol', $sensor);
				$this->db->update('set_tempkontrol', $send_db);
			}
			$nilai[] = $nilai_kontrol;
		}

		$def = 0;
		foreach ($nilai as $v) {
			if ($v != '0') {
				$def = $v;
			}
		}

		$stts_kontrol = $this->db->where('id_logger', $id_alat)->get('status_kontrol')->row();
		if ($def == 0) {
			$send_db2 = array(
				'status_kontrol' => 0,
				'session_id' => '0',
			);
		} else {
			// endpoint ini dipanggil device, bukan operator login,
			// jadi session_id pemegang kontrol tidak diubah
			$send_db2 = array(
				'status_kontrol' => 1,
				'session_id' => $stts_kontrol ? $stts_kontrol->session_id : '0',
			);
		}
		$this->db->where('id_logger', $id_alat);
		$this->db->update('status_kontrol', $send_db2);

		$send_kontrol = array_merge($send_db2, array('id_logger' => $id_alat));

		$send2 = array(
			'id_logger' => $id_alat,
			'waktu' => $waktu,
		);

		$server = 'mqtt.beacontelemetry.com';
		$port = 8883;
		$username = 'userlog';
		$password = 'b34c0n';
		$client_id = 'bemqtt-awgcjson';
		$ca = "/etc/ssl/certs/ca-bundle.crt";
		$mqtt = new phpMQTT($server, $port, $client_id, $ca);
		$mqtt_status = 'timeout';
		if ($mqtt->connect(true, NULL, $username, $password)) {
			$mqtt->publish('awgc-' . $id_alat, json_encode($send2), 0, false);
			$mqtt->publish('kontrol_pintu-' . $id_alat, json_encode($send_kontrol), 0, false);
			$mqtt->close();
			$mqtt_status = 'terkirim';
		}

		echo json_encode(array(
			'status' => 'success',
			'id_logger' => $id_alat,
			'waktu' => $waktu,
			'mqtt' => $mqtt_status,
		));
	}

	public function sesi_loggerawgc()
	{
		$this->session->set_userdata('log_awgc', $this->input->post('logger_id'));
		redirect('datamasuk/data_awgc');
	}

	public function tes_mqtt (){
		$server = 'mqtt.beacontelemetry.com';
		$port = 8883;
		$username = 'userlog';
		$password = 'b34c0n';
		$client_id = 'bemqtt-tes';
		$ca = "/etc/ssl/certs/ca-bundle.crt";
		$mqtt = new phpMQTT($server, $port, $client_id, $ca);
		$send_kontrol = ['awdwad'=>'adwd'];
		if ($mqtt->connect(true, NULL, $username, $password)) {
			$mqtt->publish('tesmqtt', json_encode($send_kontrol), 0, false);
			$mqtt->close();
		} else {
			echo "Time out!\n";
		}

	}
}
