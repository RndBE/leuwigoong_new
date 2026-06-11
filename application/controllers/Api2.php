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
		$pintu = $this->db->join('parameter_pintu','parameter_pintu.id_pintu=log_kontrol.id_pintu')->join('t_pintu','t_pintu.id_pintu=log_kontrol.id_pintu')->where('parameter_pintu.id_param',$id_sensor)->like('datetime',$tanggal)->get('log_kontrol')->result_array();
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

						$icon_marker = $kat->controller . '_on';
					} else {
						$koneksi = 'Koneksi Terputus';
						$kn = 'Off';
						$icon_marker = $kat->controller . '_off';
					}
				}

				$data[] = array(
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
		$kategori = $this->db->query("SELECT * FROM t_logger join kategori_logger on kategori_logger.id_katlogger = t_logger.kategori_log");
		foreach ($kategori->result() as $kat) {
			if($kat->controller == 'awgc'){
				$query_lokasi = $this->db->where('kategori_log',$kat->id_katlogger)->get('t_logger')->row();
				$dataMenu[] = array(
					'id_kategori' => $kat->id_katlogger,
					'menu' => $kat->nama_kategori,
					'id_logger' => $query_lokasi->id_logger,
					'controller' => $kat->controller,
					'tabel' => $kat->tabel,
					'icon' => $kat->icon_app,
					'temp_tabel' => $kat->temp_data,
					'id_logger' => $kat->id_logger
				);
			}else{
				$dataMenu[] = array(
					'id_kategori' => $kat->id_katlogger,
					'menu' => $kat->nama_kategori,
					'id_logger' => '',
					'controller' => $kat->controller,
					'tabel' => $kat->tabel,
					'icon' => $kat->icon_app,
					'temp_tabel' => $kat->temp_data,
					'id_logger' => $kat->id_logger
				);
			}

		}
		echo json_encode($dataMenu);
	}


	public function notif_versi()
	{
		$versi = '1.1.0';
		echo json_encode(array('versi' => $versi, 'link' => 'https://pusdajatim.monitoring4system.com/unduh/pusda_jatim_1.3.1.apk'));
	}

	public function notif_versi_ios()
	{
		$versi = '1.3.2';
		echo json_encode(array('versi' => $versi, 'link' => 'https://pusdajatim.monitoring4system.com/unduh/pusda_jatim_1.2.0.apk'));
	}


	function lokasi()
	{
		$tabel = $this->input->get('tabel');
		$id_logger = $this->input->get('id_logger');
		$dataLokasi = array();
		$query_lokasi = $this->db->where('id_logger',$id_logger)->get('t_pintu')->result_array();
		$status_kontrol = $this->db->where('id_logger',$id_logger)->get('status_kontrol')->row();
		$nama_lokasi = $this->db->where('id_logger',$id_logger)->join('t_lokasi','t_lokasi.idlokasi = t_logger.lokasi_logger')->get('t_logger')->row();
		$cek = $this->db->where('code_logger', $id_logger)->get($tabel)->row();
		$date_now = date('Y:m:d H:i:s');
		$date = date('Y-m-d H:i:s', strtotime('-1 hour', strtotime($date_now)));
		if ($cek->waktu > $date) {
			$status = 'On';
		} else {
			$status = 'Off';
		}
		foreach($query_lokasi as $key => $val) {
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
				'id_pintu'=>$val['id_pintu'],
				'nama_pintu'=>$val['nama_pintu'],
				'level_pintu'=>$cek->$sensor_level,
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
	}

	function data_pintu () {
		$tabel = $this->input->get('tabel');
		$id_logger = $this->input->get('id_logger');
		$id_pintu = $this->input->get('id_pintu');
		$dataLokasi = array();
		$query_lokasi = $this->db->where('id_logger',$id_logger)->where('id_pintu',$id_pintu)->get('t_pintu')->row();
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
			'level_pintu'=>$cek->$sensor_level,
			'r'=>$cek->$r,
			's'=>$cek->$s,
			't'=>$cek->$t,
			'jenis_pintu'=>$query_lokasi->jenis,
		];

		$parameter = $this->db->where('logger_id',$id_logger)->where('analisa','0')->get('parameter_sensor')->result_array();

		$data = [
			'waktu'=>$cek->waktu,
			'data_pintu'=>$dataLokasi,
		];

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
		echo json_encode($data);
	}

	function dtakhir()
	{
		$idlog = $this->input->get('idlogger'); 
		$tabel = $this->input->get('tabel');
		$data_terakhir = array();
		$data_logger = $this->db->join('t_lokasi', 't_logger.lokasi_logger = t_lokasi.idlokasi')->where('t_logger.id_logger', $idlog)->get('t_logger')->row();
		$query_perbaikan = $this->db->query('select * from t_perbaikan where id_logger="' . $idlog . '" ');
		if ($query_perbaikan->num_rows() == null) {
			$qparam = $this->db->query("SELECT * FROM parameter_sensor where logger_id='" . $idlog . "'");
			foreach ($qparam->result() as $sensor) {
				$kolom = $sensor->kolom_sensor;
				$kolom2 = $sensor->kolom_acuan;
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
				'data_terakhir' => $data_terakhir
			);
			echo json_encode($data_akhir);
		}
	}

	function analisapertanggal()
	{
		$idlogger = $this->input->get('idlogger');
		$idsensor = $this->input->get('idsensor');
		$tabel = $this->input->get('tabel');
		$tanggal = $this->input->get('tanggal');
		
		$data = array();
		$min = array();
		$max = array();
		
		$jenis = $this->input->get('jenis');
		if($jenis == 'awgc'){
			$qparam = $this->db->query("SELECT * FROM parameter_pintu where id_param='" . $idsensor . "'");
		}else{
			$qparam = $this->db->query("SELECT * FROM parameter_sensor where id_param='" . $idsensor . "'");
		}
		
		foreach ($qparam->result() as $param) {
			if ($param->tipe_graf == 'column') {
				$namaSensor = 'Akumulasi_' . $param->nama_parameter;
				$select = 'sum(' . $param->kolom_sensor . ')as ' . $namaSensor;
			} else {
				$namaSensor = 'Rerata_' . $param->nama_parameter;
				$select = 'avg(' . $param->kolom_sensor . ')as ' . $namaSensor;
			}
			$sensor = $param->kolom_sensor;
			$satuan = $param->satuan;
			$namaparameter = $param->nama_parameter;
		}
		$query_data = $this->db->query("SELECT waktu," . $select . ",min(" . $sensor . ") as min,max(" . $sensor . ") as max FROM " . $tabel . " where code_logger='" . $idlogger . "' and waktu >= '" . $tanggal . " 00:00' and waktu <= '" . $tanggal . " 23:59' group by HOUR(waktu),DAY(waktu),MONTH(waktu),YEAR(waktu);");
		$hsl = $query_data->result();

		foreach ($hsl as $datalog) {
			$waktu[] = date('Y-m-d H', strtotime($datalog->waktu)) . ":00";
			$data[] = number_format($datalog->$namaSensor, 2);
			$min[] = number_format($datalog->min, 2);
			$max[] = number_format($datalog->max, 2);
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

	function analisaperbulan()
	{
		$idlogger = $this->input->get('idlogger');
		$idsensor = $this->input->get('idsensor');
		$tabel = $this->input->get('tabel');
		$tanggal = $this->input->get('tanggal');

		$data = array();
		$min = array();
		$max = array();
		$waktu = [];
		$jenis = $this->input->get('jenis');
		if($jenis == 'awgc'){
			$qparam = $this->db->query("SELECT * FROM parameter_pintu where id_param='" . $idsensor . "'");
		}else{
			$qparam = $this->db->query("SELECT * FROM parameter_sensor where id_param='" . $idsensor . "'");
		}
		
		foreach ($qparam->result() as $param) {
			
			if ($param->tipe_graf == 'column') {
				$namaSensor = 'Akumulasi_' . $param->nama_parameter;
				$select = 'sum(' . $param->kolom_sensor . ')as ' . $namaSensor;
			} else {
				$namaSensor = 'Rerata_' . $param->nama_parameter;
				$select = 'avg(' . $param->kolom_sensor . ')as ' . $namaSensor;
			}
			$sensor = $param->kolom_sensor;
			$satuan = $param->satuan;
			$namaparameter = $param->nama_parameter;
		}
		$query_data = $this->db->query("SELECT waktu,DATE(waktu) as tanggal," . $select . ",min(" . $sensor . ") as min,max(" . $sensor . ") as max FROM " . $tabel . " where code_logger='" . $idlogger . "' and waktu >= '" . $tanggal . "-01 00:00' and waktu <= '" . $tanggal . "-31 23:59' group by DAY(waktu),MONTH(waktu),YEAR(waktu);");
		$dbt = 0;

		$hsl = $query_data->result();

		foreach ($hsl as $datalog) {
			$waktu[] = date('Y-m-d', strtotime($datalog->waktu));
			$data[] = number_format($datalog->$namaSensor, 2);
			$min[] = number_format($datalog->min, 2);
			$max[] = number_format($datalog->max, 2);
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
		$idlogger = $this->input->get('idlogger');
		$idsensor = $this->input->get('idsensor');
		$tabel = $this->input->get('tabel');
		$awal = $this->input->get('awal');
		$akhir = $this->input->get('akhir');

		$data = array();
		$min = array();
		$max = array();
		$waktu = [];
		$jenis = $this->input->get('jenis');
		if($jenis == 'awgc'){
			$qparam = $this->db->query("SELECT * FROM parameter_pintu where id_param='" . $idsensor . "'");
		}else{
			$qparam = $this->db->query("SELECT * FROM parameter_sensor where id_param='" . $idsensor . "'");
		}
		
		foreach ($qparam->result() as $param) {
			if ($param->tipe_graf == 'column') {
				$namaSensor = 'Akumulasi_' . $param->nama_parameter;
				$select = 'sum(' . $param->kolom_sensor . ')as ' . $namaSensor;
			} else {
				$namaSensor = 'Rerata_' . $param->nama_parameter;
				$select = 'avg(' . $param->kolom_sensor . ')as ' . $namaSensor;
			}
			$sensor = $param->kolom_sensor;
			if ($param->nama_parameter == 'Debit') {
				$namaSensor = 'Rerata_' . $param->nama_parameter;
				$select = 'avg(' . $param->kolom_acuan . ')as ' . $namaSensor;
				$sensor = $param->kolom_acuan;
			}
			$satuan = $param->satuan;
			$namaparameter = $param->nama_parameter;
		}
		$query_data = $this->db->query("SELECT waktu,DATE(waktu) as tanggal," . $select . ",min(" . $sensor . ") as min,max(" . $sensor . ") as max FROM " . $tabel . " where code_logger='" . $idlogger . "' and waktu >='" . $awal . "' and waktu <='" . $akhir . " 23:59:00' group by HOUR(waktu),DAY(waktu),MONTH(waktu),YEAR(waktu) order by waktu asc;");
		$dbt = 0;
		$hsl = $query_data->result();

		foreach ($hsl as $datalog) {
			$waktu[] = date('Y-m-d H', strtotime($datalog->waktu)) . ':00';
			$data[] = number_format($datalog->$namaSensor, 2);
			$min[] = number_format($datalog->min, 2);
			$max[] = number_format($datalog->max, 2);
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
		$idlogger = $this->input->get('idlogger');
		$idsensor = $this->input->get('idsensor');
		$tabel = $this->input->get('tabel');
		$tanggal = $this->input->get('tahun');

		$data = array();
		$min = array();
		$max = array();
		$dta_avg = array();
		$dta_min = array();
		$dta_max = array();

		$jenis = $this->input->get('jenis');
		if($jenis == 'awgc'){
			$qparam = $this->db->query("SELECT * FROM parameter_pintu where id_param='" . $idsensor . "'");
		}else{
			$qparam = $this->db->query("SELECT * FROM parameter_sensor where id_param='" . $idsensor . "'");
		}
		
		foreach ($qparam->result() as $param) {
			if ($param->tipe_graf == 'column') {
				$namaSensor = 'Akumulasi_' . $param->nama_parameter;
				$select = 'sum(' . $param->kolom_sensor . ')as ' . $namaSensor;
			} else {
				//$namaSensor='Rerata_'.$param->nama_parameter;
				$namaSensor = 'Rerata_' . $param->nama_parameter;
				$select = 'avg(' . $param->kolom_sensor . ')as ' . $namaSensor;
			}
			$sensor = $param->kolom_sensor;
			if ($param->nama_parameter == 'Debit') {
				$namaSensor = 'Rerata_' . $param->nama_parameter;
				$select = 'avg(' . $param->kolom_acuan . ')as ' . $namaSensor;
				$sensor = $param->kolom_acuan;
			}
			$satuan = $param->satuan;
			$namaparameter = $param->nama_parameter;
		}
		$query_data = $this->db->query("SELECT waktu,DATE(waktu) as tanggal,MONTH(waktu) as bulan," . $select . ",min(" . $sensor . ") as min,max(" . $sensor . ") as max FROM " . $tabel . " where code_logger='" . $idlogger . "' and waktu >= '" . $tanggal . "-01-01 00:00' and waktu <= '" . $tanggal . "-12-31 23:59' group by MONTH(waktu),YEAR(waktu);");
		$dbt = 0;
		foreach ($query_data->result() as $datalog) {
			$waktu[] = date('Y-m', strtotime($datalog->waktu));
			$data2[] = number_format($datalog->$namaSensor, 2);
			$min2[] = number_format($datalog->min, 2);
			$max2[] = number_format($datalog->max, 2);
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
		$query = $this->db->query('SELECT * from kategori_logger INNER JOIN t_logger on t_logger.kategori_log = kategori_logger.id_katlogger;');
		foreach ($query->result() as $code_l) {
			$tabel = $code_l->temp_data;
		}
		$status_sd = 'OK';
		$query_informasi = $this->db->query('SELECT * FROM t_informasi where logger_id="' . $idlogger . '"');
		foreach ($query_informasi->result() as $data) {
			$query_logger = $this->db->query('SELECT * FROM t_logger where id_logger="' . $idlogger . '"');
			foreach ($query_logger->result() as $logger) {
				$query_kategori = $this->db->query('SELECT * FROM kategori_logger where id_katlogger="' . $logger->kategori_log . '"');
				foreach ($query_kategori->result() as $kategori) {
					$query_ceksd = $this->db->query('SELECT sensor4, sensor54, sensor53  FROM ' . $kategori->temp_data . ' where code_logger="' . $idlogger . '" order by waktu desc limit 1');
					foreach ($query_ceksd->result() as $ceksd) {
						if ($ceksd->sensor54 == '1') {
							$status_sd = 'OK';
						} else {
							$status_sd = 'Terjadi Kesalahan';
						}

						if ($ceksd->sensor4 == '1') {
							$status_sensor = 'OK';
						} else {
							$status_sensor = 'Terjadi Kesalahan';
						}
						if ($ceksd->sensor53 == '1') {
							$status_mux = 'OK';
						} else {
							$status_mux = 'Terjadi Kesalahan';
						}
					}
				}
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
						  'Status Sensor', 'nilai' => $status_sensor),
					array('nama' =>
						  'Status MUX', 'nilai' => $status_mux),
					array('nama' =>
						  'Awal Kontrak', 'nilai' => $data->tgl_kontrak),
					array('nama' =>
						  'Akhir Garansi', 'nilai' => $data->garansi),
					array('nama' =>
						  'Logger Aktif', 'nilai' => $data->tgl_aktif),
					array('nama' =>
						  'No Seluler', 'nilai' => $data->nosell),
					array('nama' =>
						  'IMEI', 'nilai' => $data->imei),
					/*
					array('nama'=>
						  'Nama PIC','nilai'=>$data->nama_pic),
					array('nama'=>
						  'No PIC','nilai'=>$data->no_pic),
						  */
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
						  'Status Sensor', 'nilai' => $status_sensor),
					array('nama' =>
						  'Status MUX', 'nilai' => $status_mux),
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
					/*
					array('nama'=>
						  'Nama PIC','nilai'=>$data->nama_pic),
					array('nama'=>
						  'No PIC','nilai'=>$data->no_pic),
					*/
				);
			}
		}
		$data_terakhir = array(
			'data' => $data_informasi,
			//'elevasi'=>$data->elevasi
		);

		echo json_encode($data_terakhir);
	}
	
	public function lanjut_kontrol(){
		$kode_akses = $this->db->where('id_user', '7')->get('kode_akses')->row();
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
			$data = $this->input->post('daftar_kontrol');
			
			$encData = json_decode($data);
			$s = [];
			foreach($encData as $key=>$v){
				$s[] = [
					'id_pintu'=>$v->id_pintu,
					'set_value'=>$v->level,
					'status'=>'1'
				];
			}
			
			$send_kontrol = [
				'status_kontrol'=>'1',
				'id_logger'=>$this->input->post('id_logger'),
				'session_id'=>$this->session->session_id,
			];
			$this->db->update_batch('set_tempkontrol',$s, 'id_pintu'); 
			$this->db->where('id_logger',$this->input->post('id_logger'));
			$this->db->update('status_kontrol',$send_kontrol);
			$status = [];
			if ($mqtt->connect(true, NULL, $username, $password)) {
				$mqtt->publish('kontrol_pintu-'.$this->input->post('id_logger'), json_encode($send_kontrol), 0, false);
				$mqtt->close();
			} else {
				echo "Time out!\n";
			}

			echo json_encode(['status'=>'success']);
		}
		
	}
	
	public function lanjut_kontrol2(){
		$kode_akses = $this->db->where('id_user', '7')->get('kode_akses')->row();
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
			$data = $this->input->post('daftar_kontrol');
			$sts = $this->db->where('id_logger', $this->input->post('id_logger'))->get('status_kontrol')->row();
			$encData = json_decode($data);
			$s = [];
			foreach($encData as $key=>$v){
				$s[] = [
					'id_pintu'=>$v->id_pintu,
					'set_value'=>$v->level,
					'status'=>'1'
				];
			}
			
			$send_kontrol = [
				'status_kontrol'=>'1',
				'id_logger'=>$this->input->post('id_logger'),
				'session_id'=>$this->session->session_id,
			];
			
			$status = [];
			if($sts->status_kontrol == '0'){
				$this->db->update_batch('set_tempkontrol',$s, 'id_pintu'); 
				$this->db->where('id_logger',$this->input->post('id_logger'));
				$this->db->update('status_kontrol',$send_kontrol);
				if ($mqtt->connect(true, NULL, $username, $password)) {
					$mqtt->publish('kontrol_pintu-'.$this->input->post('id_logger'), json_encode($send_kontrol), 0, false);
					$mqtt->close();
				} else {
					echo "Time out!\n";
				}
				echo json_encode(['status'=>'success']);
			}else{
				echo json_encode(['status'=>'fail']);
			}
			
		}
		
	}
}
