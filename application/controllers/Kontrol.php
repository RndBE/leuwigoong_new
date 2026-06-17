<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Kontrol extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->library('PhpMQTT');
	}
	public function index () {
		$id_logger=  $this->input->get('idlogger');
		$buffer=  $this->input->get('buffer');
		$waktu= str_replace('%20',' ',$this->input->get('waktu'));
		$data = $this->db->where('id_logger',$id_logger)->get('set_tempkontrol')->result_array();
		$dt = [];
		$i = 1;
		$a = 1;
		foreach($data as $key=>$vl){
			$dt['p'.$i++] = $vl['status'];
			$dt['ps' . $a++] =$vl['set_value'];
		}
		echo json_encode($dt);
	}

	public function stop_kontrol () {
		$data = $this->input->post('data');
		foreach($data as $k => $dt){
			$send_kontrol = [
				'set_value'=>'0',
				'status'=>'2',
			];
			$this->db->where('id_pintu',$dt['id_pintu']);
			$this->db->update('set_tempkontrol',$send_kontrol);
		}
		echo json_encode(['status'=>'success']);
	}

	public function stop_kontrol2 () {
		$data = $this->input->post('data');
		$st = json_decode($data);
		foreach($st as $k => $dt){
			$send_kontrol = [
				'set_value'=>'0',
				'status'=>'2',
			];
			$this->db->where('id_pintu',$dt->id_pintu);
			$this->db->update('set_tempkontrol',$send_kontrol);
		}
		echo json_encode(['status'=>'success']);
	}

	public function index2 () {
		$id_logger=  $this->input->get('idlogger');
		$data = $this->db->where('id_logger',$id_logger)->get('set_tempkontrol')->result_array();
		$dt = [];
		foreach($data as $key=>$vl){
			$dt['pintu_'.$vl['id_pintu']] = $vl['status'];
			$dt['set_value_' . $vl['id_pintu']] =$vl['set_value'];
		}
		echo json_encode($dt);
	}

	public function lanjut_kontrol(){
		$this->load->helper('gcm');
		$kode_akses = $this->db->where('id_user', '7')->get('kode_akses')->row();

		$inp = md5($this->input->post('akses'));
		$server = 'mqtt.beacontelemetry.com';
		$port = 8883;
		$username = 'userlog';
		$password = 'b34c0n';
		$client_id = 'bemqtt-awgc-cilicis';
		$ca = "/etc/ssl/certs/ca-bundle.crt";
		$mqtt = new phpMQTT($server, $port, $client_id, $ca);
		if($kode_akses->kode_akses != $inp){
			echo json_encode(['status'=>'error']);
		}else{
			$data = $this->input->post('data');
			$s = [];
			$f = [];
			$items = [];
			$nama_pintu = '';
			$temp = $this->db->where('code_logger', $this->session->userdata('idlogger'))->get('temp_awgc')->row();
			foreach($data as $key=>$v){
				$pintu = $this->db->where('id_pintu', $v['id_pintu'])->get('t_pintu')->row();
				$gcm = $pintu->mqtt_identifier;
				$sensor_controller = $pintu->status_controller;
				$r_p = $pintu->r;
				$s_p = $pintu->s;
				$t_p = $pintu->t;
				$nilai = $temp->$sensor_controller;
				if($nilai == '1' and $temp->$r_p == '1' and $temp->$s_p == '1' and $temp->$t_p == '1'){
					$s[] = [
						'id_pintu'=>$v['id_pintu'],
						'set_value'=>$v['elev'],
						'status'=>'1'
					];
					$nama_pintu = $pintu->nama_pintu;
					$items[] = ['identifier'=>$gcm, 'target'=>(int)$v['elev']];

					$f[] = [
						'id_pintu'=>$v['id_pintu'],
						'elev_kontrol'=>$v['elev'],
						'nama_pintu'=>$v['nama_pintu'],
						'elev_asli'=>$v['elev_asli'],
						'status'=>'<i class="fa fa-check-circle ms-2 text-success"></i>'
					];
				}else{
					$f[] = [
						'id_pintu'=>$v['id_pintu'],
						'elev_kontrol'=>$v['elev'],
						'nama_pintu'=>$v['nama_pintu'],
						'elev_asli'=>$v['elev_asli'],
						'status'=>'<i class="fa fa-times-circle ms-2 text-danger"></i>'
					];
				}
			}
			if($s){
				$send_kontrol = [
					'status_kontrol'=>'1',
					'id_logger'=>$this->session->userdata('idlogger'),
					'session_id'=>$this->session->session_id,
				];
				$this->db->update_batch('set_tempkontrol',$s, 'id_pintu'); 
				$this->db->where('id_logger',$this->session->userdata('idlogger'));
				$this->db->update('status_kontrol',$send_kontrol);
				// Format GCM baru: satu pesan GCM_GATE per pintu ke topik sub_<id_logger>.
				// Logger + module id diambil dari mapping binding (gcm_helper), bukan
				// dari kolom id_logger DB. Pre-warning horn ditangani firmware
				// (GCM_GATE_WARN), tidak lagi via web (ews_onoff dihapus).
				$gcm_cmds = [];
				foreach ($items as $cmd) {
					$map = gcm_lookup($cmd['identifier']);
					if (!$map) { continue; } // identifier tak dikenal, lewati
					$gcm_cmds[] = [
						'topic'   => gcm_topic($map['logger']),
						'payload' => gcm_gate_set_payload($map['id'], $cmd['target']),
					];
				}

				if ($mqtt->connect(true, NULL, $username, $password)) {
					$mqtt->publish('kontrol_pintu-'.$this->session->userdata('idlogger'), json_encode($send_kontrol), 0, false);
					foreach ($gcm_cmds as $cmd) {
						$mqtt->publish($cmd['topic'], $cmd['payload'], 0);
					}

					$mqtt->close();
				} else {
					echo json_encode([
						'status' => 'fail',
						'message' => 'MQTT timeout'
					]);
				}
				try {
					$this->notif_aplikasi($nama_pintu);
				} catch (Exception $e) {
					log_message('error', 'Notif FCM (lanjut_kontrol) gagal, kontrol tetap lanjut: ' . $e->getMessage());
				}
				echo json_encode(['status'=>'success','data'=>json_encode($f)]);
			}else{
				echo json_encode(['status'=>'fail']);
			}
			
		}
	}

	public function status_kontrol () {
		$id_logger = $this->input->get('id_logger');
		$status = $this->db->where('id_logger',$id_logger)->get('status_kontrol')->row();
		$set_temp = $this->db->where('id_logger',$id_logger)->get('set_tempkontrol')->result_array();
		$nilai = [];
		foreach($set_temp as $k => $vl){
			$nilai[] = $vl['status'];
		}
		if($status->status_kontrol == '2' and in_array("1" ,$nilai)) {
			echo json_encode(['status_kontrol'=>'1']);
		}else{
			echo json_encode($status);
		}
	}


	public function respon_logger () {
		$id_logger = $this->input->get('id_logger');
		$up = [
			'status_kontrol'=>'2',
		];
		$this->db->where('id_logger',$id_logger);
		$sts = $this->db->update('status_kontrol',$up);
		if($sts){
			echo json_encode(['status'=>'success']);	
		}else{
			echo json_encode(['status'=>'error']);	
		}
	}

	public function operasi () {
		$id_logger = $this->input->get('id_logger');
		$up = [
			'status_kontrol'=>'0',
			'session_id'=>'0',
		];
		$this->db->where('id_logger',$id_logger);
		$sts = $this->db->update('status_kontrol',$up);
		if($sts){
			echo json_encode(['status'=>'success']);	
		}else{
			echo json_encode(['status'=>'error']);	
		}
	}
	
	public function selesai_kontrol ($id_logger) {
		$list_pintu = $this->input->post('list_pintu');
		foreach($list_pintu as $key=>$val){
			if($val['elev_asli'] < $val['elev']){
				$sistem = '1';
			}else{
				$sistem = '0';
			}
			$data = [
				'id_logger'=>$id_logger,
				'id_pintu'=>$val['id_pintu'],
				'metode'=>'Telemetry',
				'dari'=>$val['elev_asli'],
				'ke'=>$val['elev'],
				'datetime'=>date('Y-m-d H:i:s'),
				'sistem'=>$sistem
			];
			$this->db->insert('log_kontrol',$data);
		}
		try {
			$this->notif_aplikasi_selesai();
		} catch (Exception $e) {
			log_message('error', 'Notif FCM (selesai_kontrol) gagal, kontrol tetap lanjut: ' . $e->getMessage());
		}
		echo json_encode($list_pintu);
	}
	
	public function selesai_kontrol2 ($id_logger) {
		$list_pintu = json_decode($this->input->post('list_pintu'));
		foreach($list_pintu as $key=>$val){
			
			if($val->elev_asli < $val->elev){
				$sistem = '1';
			}else{
				$sistem = '0';
			}
			$data = [
				'id_logger'=>$id_logger,
				'id_pintu'=>$val->id_pintu,
				'metode'=>'Telemetry',
				'dari'=>$val->elev_asli,
				'ke'=>$val->elev,
				'datetime'=>date('Y-m-d H:i:s'),
				'sistem'=>$sistem
			];
			$this->db->insert('log_kontrol',$data);
		}
		try {
			$this->notif_aplikasi_selesai();
		} catch (Exception $e) {
			log_message('error', 'Notif FCM (selesai_kontrol) gagal, kontrol tetap lanjut: ' . $e->getMessage());
		}
		echo json_encode($list_pintu);
	}

	public function selesai () {
		$server = 'mqtt.beacontelemetry.com';
		$port = 8883;
		$username = 'userlog';
		$password = 'b34c0n';
		$client_id = 'bemqtt-awgc-cilicis';
		$ca = "/etc/ssl/certs/ca-bundle.crt";
		$mqtt = new phpMQTT($server, $port, $client_id, $ca);
		$id_logger = $this->input->get('id_logger');
		$up = [
			'status_kontrol'=>'0'
		];
		$this->db->where('id_logger',$id_logger);
		$sts = $this->db->update('status_kontrol',$up);
		if ($mqtt->connect(true, NULL, $username, $password)) {
			$mqtt->publish('kontrol_pintu', json_encode($up), 0, false);
			$mqtt->close();
		} else {
			echo "Time out!\n";
		}

		if($sts){
			echo json_encode(['status'=>'success']);	
		}else{
			echo json_encode(['status'=>'error']);	
		}
	}

	function kirim_riset () {
		$server = 'mqtt.beacontelemetry.com';
		$port = 8883;
		$username = 'userlog';
		$password = 'b34c0n';
		$client_id = 'bemqtt-tes';
		$ca = "/etc/ssl/certs/ca-bundle.crt";
		$mqtt = new phpMQTT($server, $port, $client_id, $ca);
		$send2 = 'AKWOKWOWKWO';
		if ($mqtt->connect(true, NULL, $username, $password)) {

			$mqtt->publish('arduino-sample', $send2, 0, false);
			$mqtt->close();
		} else {
			echo "Time out!\n";
		}
	}

	function receive_data () {
		$this->load->view('konten/back/v_setting');
	}

	function receive_data2 () {
		$this->load->view('konten/back/v_setting2');
	}
	
	function tes_token () {
		$serviceAccount = json_decode(@file_get_contents('https://leuwigoong.beacontelemetry.com/unduh/copong-783f8-cfe02d37fd4c.json'), true);
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
				'content' => http_build_query($data),
				'ignore_errors' => true
			]
		];

		$context = stream_context_create($options);
		$result = @file_get_contents($url, false, $context);

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
	}
	
	public function notif_aplikasi_selesai(){	
		$accessToken = $this->getAccessToken();
		$headers = [
			'Authorization: Bearer ' . $accessToken,
			'Content-Type: application/json'
		];

		$message = [
			"message" => [
				"topic"=> "kontrol_leuwigoong",
				"notification" => [
					"title" => 'AWGC Selesai Digunakan',
					"body" => "Kontrol Pintu Bisa Digunakan Kembali",
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
	}
}