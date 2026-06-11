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
		/*
		$status = $this->db->where('id_logger',$id_logger)->get('status_kontrol')->row();

		if($status->status_kontrol == $buffer){
			$send = [
				'waktu'=>$waktu
			];
			$this->db->where('id_logger',$id_logger);
			$this->db->update('status_kontrol',$send);
		}
		*/
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
		$kode_akses = $this->db->where('id_user', '7')->get('kode_akses')->row();

		$inp = md5($this->input->post('akses'));
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
			$data = $this->input->post('data');
			$s = [];
			$f = [];
			
			$temp = $this->db->where('code_logger', $this->session->userdata('idlogger'))->get('temp_awgc')->row();
			$sts = $this->db->where('id_logger', $this->session->userdata('idlogger'))->get('status_kontrol')->row();
			foreach($data as $key=>$v){
				$pintu = $this->db->where('id_pintu', $v['id_pintu'])->get('t_pintu')->row();
				$sensor_controller = $pintu->status_controller;
				$r_p = $pintu->r;
				$s_p = $pintu->s;
				$t_p = $pintu->t;
				$status_kontrol = $sts->status_kontrol;
				$nilai = $temp->$sensor_controller;
				if($nilai == '1' and $temp->$r_p == '1' and $temp->$s_p == '1' and $temp->$t_p == '1' and $status_kontrol == '0'){
					$s[] = [
						'id_pintu'=>$v['id_pintu'],
						'set_value'=>$v['elev'],
						'status'=>'1'
					];
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
				$status = [];
				if ($mqtt->connect(true, NULL, $username, $password)) {
					$mqtt->publish('kontrol_pintu-'.$this->session->userdata('idlogger'), json_encode($send_kontrol), 0, false);
					$mqtt->close();
				} else {
					echo "Time out!\n";
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
		/*
		if(!in_array("1" ,$nilai)) {
			$send_db2 = [
				'status_kontrol'=>'0'
			];
			$this->db->where('id_logger',$id_logger);
			$this->db->update('status_kontrol',$send_db2);
		}
		*/
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
		
		echo json_encode($list_pintu);
	}

	public function selesai () {
		$server = 'mqtt.beacontelemetry.com';
		$port = 8883;
		$username = 'userlog';
		$password = 'b34c0n';
		$client_id = 'bemqtt-tes';
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
	
	
	public function notif_aplikasi($data){		
		$a = '';
		foreach($data as $key=> $v){
			$nama_pintu = $this->db->where('id_pintu', $v['id_pintu'])->get('t_pintu')->row()->nama
			$a .= $v['id_pintu'];
		}
		$data = [
			"to" => '/topics/kontrol_cikeusik',
			"notification" => [
				"title" => 'AWGC Sedang Digunakan',
				"body" => "$a sedang beroperasi",
			],
		];
		
		$ch = curl_init();  // initialize curl handle
		$url = 'https://fcm.googleapis.com/fcm/send';
		curl_setopt($ch, CURLOPT_URL, $url); // set url to post to
		//curl_setopt($ch, CURLOPT_FAILonerror, 1); //Fail on error'=
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Authorization: key=AAAAHPc9_RQ:APA91bH390oEaIfeUN_7BLdOYTv_WTJFEMJeGez70GUuWruwgn6OuePPWNlMFJKEEM59-Kdkg00IQ1sbBr7Zrw4-xLG-WpiMVVVAmvH90i3fEcLqKtSp_-MfzkNbjfZw5Uw5n2RIZtCv',
			'Content-Type: application/json'
		));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); // return into a variable
		curl_setopt($ch, CURLOPT_POST, 1); // set POST method
		curl_setopt($ch, CURLOPT_POSTFIELDS, $dt); // add POST fields
		$result = curl_exec($ch); // run the whole process
		curl_close($ch);
	}
}