
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Analisa extends CI_Controller {

	function __construct() {
		parent::__construct();

		$this->load->model('m_analisa');
	}

	public function index()
	{
		if($this->session->userdata('logged_in'))
		{
			$this->load->library('googlemaps');
			$id_kategori = '1';
			$ktg = $this->db->get('kategori_logger')->result_array();
			$sensor_avw = [];
			$data['ktg_all'] = $this->db->get('kategori_logger')->result_array();
			$ipcam = json_decode(file_get_contents('https://leuwigoong.beacontelemetry.com/ipcam/get_all_photo.php'),true);
			
			foreach ($ktg  as $key=>$kat) {
				$tabel=$kat['temp_data'];
				if($kat['tabel'] == 'awgc'){
					$data_logger = $this->db->join('t_lokasi', 't_logger.lokasi_logger = t_lokasi.idlokasi')->where('kategori_log',$kat['id_katlogger'])->order_by('id_logger')->get('t_logger')->result_array();

					foreach ($data_logger as $k=>$log){
						$id_logger=$log['id_logger'];

						$temp_data = $this->db->where('code_logger',$id_logger)->get($tabel)->row();
						$awal=date('Y-m-d H:i',(mktime(date('H')-1)));
						if($temp_data->waktu >= $awal)
						{
							$color="green";
							$status_logger="Koneksi Terhubung";
						}
						else{
							$color="red";
							$status_logger="Koneksi Terputus";			
						}

						if($temp_data->sensor55 == '1' )
						{
							$sdcard='OK';
						}
						else{
							$sdcard='Bermasalah';
						}
						$param = $this->db->query("SELECT * FROM `t_pintu` WHERE id_logger = '$id_logger'")->result_array();

						foreach($param as $ky=>$val){
							$sensor_r = $val['r'];
							$sensor_s = $val['s'];
							$sensor_t = $val['t'];
							$elevasi = $val['sensor_level'];
							$param[$ky]['r'] = $temp_data->$sensor_r;
							$param[$ky]['s'] = $temp_data->$sensor_s;
							$param[$ky]['t'] = $temp_data->$sensor_t;
							$param[$ky]['elevasi'] = $temp_data->$elevasi;
						}
						$param_logger = $this->db->query("SELECT * FROM `parameter_sensor` WHERE logger_id = '$id_logger' and analisa = '0' ORDER BY CAST(SUBSTR(`kolom_sensor`,7) AS UNSIGNED)")->result_array();

						foreach($param_logger as $kyy => $val) {

							$get='tabel='.$kat['tabel'].'&id_param='.$val['id_param'].'&id_logger='.$id_logger;
							$kolom = $val['kolom_sensor'];
							$param_logger[$kyy]['nilai'] = $temp_data->$kolom;
							$param_logger[$kyy]['link'] = base_url() . $kat['controller'].'/set_sensordash?'.$get;
						}
						
						$ipcam_link = '';
						
						$ktg[$key]['logger'][$k] = [
							'id_logger'=>$id_logger,
							'nama_lokasi'=>$log['nama_lokasi'],
							'waktu'=>$temp_data->waktu,
							'color'=>$color,
							'status_logger'=>$status_logger,
							'status_sd'=>$sdcard,
							'sensor'=>$param,
							'param'=>$param_logger,
							'ipcam'=>$ipcam_link
						];

					}
				}else{
					$data_logger = $this->db->join('t_lokasi', 't_logger.lokasi_logger = t_lokasi.idlokasi')->where('kategori_log',$kat['id_katlogger'])->order_by('id_logger')->get('t_logger')->result_array();
					
					foreach ($data_logger as $k=>$log){
						$id_logger=$log['id_logger'];
						$temp_data = $this->db->where('code_logger',$id_logger)->get($tabel)->row();
						
						$awal=date('Y-m-d H:i',(mktime(date('H')-1)));
						if($temp_data->waktu >= $awal)
						{
							$color="green";
							$status_logger="Koneksi Terhubung";
						}
						else{
							$color="red";
							$status_logger="Koneksi Terputus";			
						}

						if($temp_data->sensor13 == '1' )
						{
							$sdcard='OK';
						}
						else{
							$sdcard='Bermasalah';
						}

						$param = $this->db->query("SELECT * FROM `parameter_sensor` WHERE logger_id = '$id_logger' ORDER BY CAST(SUBSTR(`kolom_sensor`,7) AS UNSIGNED)")->result_array();
						foreach($param as $ky => $val) {
							$get='tabel='.$kat['tabel'].'&id_param='.$val['id_param'];
							$kolom = $val['kolom_sensor'];
							$param[$ky]['nilai'] = $temp_data->$kolom;
							$param[$ky]['link'] = base_url() . $kat['controller'].'/set_sensordash?'.$get;
						}
						$cek = isset($ipcam[$id_logger]);
						
						$ipcam_link = '';
						if($cek){
							$ipcam_link = $ipcam[$id_logger];
						}
						$ktg[$key]['logger'][$k] = [
							'id_logger'=>$id_logger,
							'nama_lokasi'=>$log['nama_lokasi'],
							'waktu'=>$temp_data->waktu,
							'color'=>$color,
							'status_logger'=>$status_logger,
							'status_sd'=>$sdcard,
							'param'=>$param,
							'link'=>'',
							'short'=>'',
							'ipcam'=>$ipcam_link
						];
					}
				}

			}
			$dt = [];
			$tgl = []; 
			$gabung = [];
			$first = $ktg[0];
			$logger_first = $first['logger'][0];
			$data['param_first'] = $logger_first['param'];
			$param_first = $logger_first['param'][0];
			$query_analisa = $this->db->query("SELECT waktu, HOUR(waktu) as jam,DAY(waktu) as hari,MONTH(waktu) as bulan,YEAR(waktu) as tahun,avg(".$param_first['kolom_sensor'].") as rerata FROM ".$first['tabel']." where code_logger='" . $logger_first['id_logger'] . "' and waktu >= '".date('Y-m-d')." 00:00' and waktu <= '".date('Y-m-d')." 23:59' group by HOUR(waktu),DAY(waktu),MONTH(waktu),YEAR(waktu) order by waktu asc;")->result_array();
			foreach ($query_analisa as $datalog) {
				$dt[]= number_format($datalog['rerata'], 3,'.','');
				$tgl[] = $this->convertUTC($datalog['waktu']);
				$gabung[] = [
					'waktu'=>$datalog['waktu'],
					'data'=>number_format($datalog['rerata'], 3,'.','')
				];
			}
			$data['data_analisa'] = $dt;
			$data['waktu_analisa'] = $tgl;
			$data['gabung'] = $gabung;
			$data['data_konten']=$ktg;
			
			$data['sensor_avw'] = $sensor_avw;
			$kategori=array();
			$query_kategori=$this->db->query('select * from kategori_logger ');
			$marker = [];
			foreach ($query_kategori->result()  as $kat) {
				$tabel=$kat->tabel;
				$tabel_temp=$kat->temp_data;
				$content=array();
				$bidang = $this->session->userdata['bidang'];
				$query_lokasilogger=$this->db->query("select * from t_logger inner join t_lokasi ON t_logger.lokasi_logger=t_lokasi.idlokasi where kategori_log='$kat->id_katlogger'");
				foreach ($query_lokasilogger->result() as $loklogger){
					$id_logger=$loklogger->id_logger;
					$parameter=array();
					$id_param = $this->db->where('logger_id',$id_logger)->limit(1)->get('parameter_sensor')->row();
					$query_data=$this->db->query('select * from '.$tabel_temp.' where code_logger="'.$id_logger.'"');
					foreach ($query_data->result() as $dt){
						$waktu=$dt->waktu;
						$awal=date('Y-m-d H:i',(mktime(date('H')-1)));
						if($waktu >= $awal){
							$icon_marker=base_url().'pin_marker/baru/'.$kat->controller.'_on.png';
							$status='<p style="color:green;margin-bottom:0px">Koneksi Terhubung</p>';
							$statlog='Terhubung'; 

							$statuspantau = "Tingkat Status Belum Diatur";
							$anim=" ";
						}else{
							$icon_marker=base_url().'pin_marker/baru/'.$kat->controller.'_off.png';
							$status='<p style="color:red;margin-bottom:0px">Koneksi Terputus</p>';
							$statlog='Terputus';
							$statuspantau = "-";
							$anim="BOUNCE";
						}
						$status_sd = 'OK';
					}
					if($tabel == 'awgc'){
						$id_param2 = $this->db->where('logger_id',$id_logger)->limit(1)->get('parameter_sensor')->row();
						$link = base_url(). $kat->controller.'/set_sensordash?tabel=awgc&id_param='.$id_param2->id_param.'&id_logger='.$id_logger;
					}else{
						$link =  base_url(). $kat->tabel.'/set_sensorselect/'.$id_logger.'/'.$tabel;
					}
					if($id_param){
						$marker[] = [
							'id_kategori'=>$kat->id_katlogger,
							'id_logger'=>$loklogger->id_logger,
							'koneksi'=>$statlog,
							'status_sd'=>$status_sd,
							'latitude' => $loklogger->latitude,
							'longitude' => $loklogger->longitude,
							'nama_lokasi' => $loklogger->nama_lokasi,
							'icon' => $icon_marker,
							'id_param'=>$id_param->id_param,
							'link'=>$link,
						];
					}else{
						if($tabel == 'awgc'){
							$id_param = $this->db->where('logger_id',$id_logger)->limit(1)->get('parameter_sensor')->row();
							$marker[] = [
								'id_kategori'=>$kat->id_katlogger,
								'id_logger'=>$loklogger->id_logger,
								'koneksi'=>$statlog,
								'status_sd'=>$status_sd,
								'latitude' => $loklogger->latitude,
								'longitude' => $loklogger->longitude,
								'nama_lokasi' => $loklogger->nama_lokasi,
								'icon' => $icon_marker,
								'id_param'=>$id_param->id_param,
								'link'=>$link,
							];
						}else{
							$marker[] = [
								'id_kategori'=>$kat->id_katlogger,
								'id_logger'=>$loklogger->id_logger,
								'koneksi'=>$statlog,
								'status_sd'=>$status_sd,
								'latitude' => $loklogger->latitude,
								'longitude' => $loklogger->longitude,
								'nama_lokasi' => $loklogger->nama_lokasi,
								'icon' => $icon_marker,
								'id_param'=>506,
								'link'=>$link,
							];
						}
					}
				}
			}
			$data_cuaca = json_decode(file_get_contents('https://api.bmkg.go.id/publik/prakiraan-cuaca?adm4=32.05.06.2004'));
			$new_array = array_reverse($data_cuaca->data[0]->cuaca[0]);
			$awal=date('Y-m-d H:i');
			$cuaca = [];
			foreach($new_array as $key => $c){
				if($c->local_datetime <= $awal){
					$cuaca = $c;
					break;
				}
			}
			if(!$cuaca){
				$rev = array_reverse($new_array);
				$cuaca = $rev[0];
			}
			$data['cuaca'] = $cuaca;
			$data['marker'] = $marker;
			$config['center'] = '-7.188612103019861, 107.91183584230369'; 
			$config['zoom'] = "15";
			$data['konten']="peta_lokasi";
			$this->load->view('konten/back/peta_lokasi',$data);
		}
		else
		{
			redirect('login');
		}

	}

	public function temp_ajax() {
		$id_param = $this->input->get('id_param');
		$id_logger = $this->input->get('id_logger');
		$cek = $this->db->where('id_logger',$id_logger)->get('t_logger')->row();
		$list_sensor = [];
		$tgl= [];
		$dt= [];
		$gabung = [];
		if($cek->tabel == 'avw'){
			$avw_id = $this->db->where('id',$id_param)->get('parameter_avw')->row();
			$list_sensor = $this->db->where('logger_id',$id_logger)->get('sensor')->result_array();
			
			$list_param = $this->db->where('avw_id',$avw_id->avw_id)->get('parameter_avw')->result_array();
			$param = $this->db->join('sensor','sensor.id_avw = parameter_avw.avw_id')->join('t_logger','t_logger.id_logger = sensor.logger_id')->join('kategori_logger','kategori_logger.id_katlogger = t_logger.kategori_log')->join('t_lokasi','t_lokasi.idlokasi = t_logger.lokasi_logger')->where('parameter_avw.id',$id_param)->get('parameter_avw')->row();
			$query_analisa = $this->db->query("SELECT waktu, HOUR(waktu) as jam,DAY(waktu) as hari,MONTH(waktu) as bulan,YEAR(waktu) as tahun,avg(".$param->kolom.") as rerata FROM ".$param->tabel." where code_logger='" . $param->id_logger . "' and waktu >= '".date('Y-m-d')." 00:00' and waktu <= '".date('Y-m-d')." 23:59' group by HOUR(waktu),DAY(waktu),MONTH(waktu),YEAR(waktu) order by waktu asc;")->result_array();
		}else{
			$list_param = $this->db->where('logger_id',$id_logger)->get('parameter_sensor')->result_array();
			$param = $this->db->join('t_logger','t_logger.id_logger = parameter_sensor.logger_id')->join('kategori_logger','kategori_logger.id_katlogger = t_logger.kategori_log')->join('t_lokasi','t_lokasi.idlokasi = t_logger.lokasi_logger')->where('parameter_sensor.id_param',$id_param)->get('parameter_sensor')->row();
			
			$query_analisa = $this->db->query("SELECT waktu, HOUR(waktu) as jam,DAY(waktu) as hari,MONTH(waktu) as bulan,YEAR(waktu) as tahun,avg(".$param->kolom_sensor.") as rerata FROM ".$param->tabel." where code_logger='" . $param->id_logger . "' and waktu >= '".date('Y-m-d')." 00:00' and waktu <= '".date('Y-m-d')." 23:59' group by HOUR(waktu),DAY(waktu),MONTH(waktu),YEAR(waktu) order by waktu asc;")->result_array();
		}
		foreach ($query_analisa as $datalog) {
			$dt[]= number_format($datalog['rerata'], 3,'.','');
			$tgl[] = $this->convertUTC($datalog['waktu']);
			$gabung[] = [
				'waktu'=>$datalog['waktu'],
				'data'=>number_format($datalog['rerata'], 3,'.','')
			];
		}
		echo json_encode(
			[
				'selected'=>$id_param,
				'nama_lokasi' =>$param->nama_lokasi,
				'nama_parameter' =>$param->nama_parameter,
				'tanggal_analisa' =>$tgl,
				'data_analisa' =>$dt,
				'list_sensor' =>$list_sensor,
				'list_param' =>$list_param,
				'data_gabung' =>array_reverse($gabung)
			]
		);
	}

	public function temp_ajax2() {
		$id_param = $this->input->get('id_param');
		$param = $this->db->join('t_logger','t_logger.id_logger = parameter_sensor.logger_id')->join('kategori_logger','kategori_logger.id_katlogger = t_logger.kategori_log')->join('t_lokasi','t_lokasi.idlokasi = t_logger.lokasi_logger')->where('parameter_sensor.id_param',$id_param)->get('parameter_sensor')->row();
		$query_analisa = $this->db->query("SELECT waktu, HOUR(waktu) as jam,DAY(waktu) as hari,MONTH(waktu) as bulan,YEAR(waktu) as tahun,avg(".$param->kolom_sensor.") as rerata FROM ".$param->tabel." where code_logger='" . $param->id_logger . "' and waktu >= '".date('Y-m-d')." 00:00' and waktu <= '".date('Y-m-d')." 23:59' group by HOUR(waktu),DAY(waktu),MONTH(waktu),YEAR(waktu) order by waktu asc;")->result_array();
		$dt = [];
		$tgl = [];
		$gabung = [];
		foreach ($query_analisa as $datalog) {
			$nilai_new = $datalog['rerata'];
			$dt[]= number_format($nilai_new, 3,'.','');
			$tgl[] = $this->convertUTC($datalog['waktu']);
			$gabung[] = [
				'waktu'=>$datalog['waktu'],
				'data'=>number_format($nilai_new, 3,'.','')
			];
		}
		echo json_encode(
			[
				'nama_lokasi' =>$param->nama_lokasi,
				'nama_parameter' =>$param->nama_parameter,
				'tanggal_analisa' =>$tgl,
				'data_analisa' =>$dt,
				'data_gabung' =>array_reverse($gabung)
			]
		);
	}
	
	public function temp_avw() {
		$id_avw = $this->input->get('id_avw');
		$id = $this->input->get('id');
		$param = [];
		if($id){
			$id_param = $this->db->where('id',$id)->get('parameter_avw')->row_array();
			$param = $this->db->where('avw_id',$id_param['avw_id'])->get('parameter_avw')->result_array();
		}else{
			$param = $this->db->where('avw_id',$id_avw)->get('parameter_avw')->result_array();
			$id_param = $param[0];
		}
		
		$kolom_temp = 'sensor' . ((int) filter_var($id_param['kolom'], FILTER_SANITIZE_NUMBER_INT) + 1);
		
		$dataset = $this->db->where('avw_setid',$id_param['avw_id'])->get('dataset_avw')->row();
		
		$query_analisa = $this->db->query("SELECT waktu, HOUR(waktu) as jam,DAY(waktu) as hari,MONTH(waktu) as bulan,YEAR(waktu) as tahun,avg(".$id_param['kolom'].") as rerata, avg(".$kolom_temp.") as avg_temp FROM avw where code_logger='10318' and waktu >= '".date('Y-m-d')." 00:00' and waktu <= '".date('Y-m-d')." 23:59' group by HOUR(waktu),DAY(waktu),MONTH(waktu),YEAR(waktu) order by waktu asc;")->result_array();
		
		$a = $dataset->a;
		$b = $dataset->b;
		$c = $dataset->c;
		$tct = $dataset->tct;
		$r0 = $dataset->r0;
		$elevasi_puncak = $dataset->elevasi_puncak;
		$elevasi_sensor = $dataset->elevasi_sensor;
		$t0 = $dataset->t0;
		
		foreach ($query_analisa as $datalog) {
			$frekuensi = $datalog['rerata'];
			$temperature_sensor = $datalog['avg_temp'];
			$bagian_a = ($a * pow($frekuensi, 2)) + (($b * $frekuensi) + $c);
			$level =$bagian_a*10.017 + $elevasi_sensor;
			
			if($id_param['nama_parameter'] == 'Level_Freatik_Air'){									
				$nilai = number_format($level,2,'.','');
			}else{
				$nilai = number_format($frekuensi,2,'.','');
			}
			$dt[]= number_format($nilai, 3,'.','');
			$tgl[] = $this->convertUTC($datalog['waktu']);
			$gabung[] = [
				'waktu'=>$datalog['waktu'],
				'data'=>number_format($nilai, 3,'.','')
			];
		}
		echo json_encode(
			[
				'nama_lokasi' =>$id_param['avw_id'],
				'nama_parameter' =>$id_param['nama_parameter'],
				'tanggal_analisa' =>$tgl,
				'data_analisa' =>$dt,
				'list_param'=>$param,
				'data_gabung' =>array_reverse($gabung)
			]
		);
	}

	function convertUTC ($datetimeString) {
		$date = new DateTime($datetimeString, new DateTimeZone('UTC')); 
		$date->setTimezone(new DateTimeZone('Asia/Bangkok'));
		$isoDatetimeString = $date->format('Y-m-d\TH:i:sP'); 
		return $isoDatetimeString;
	}

	function combologger()
	{
		$set =explode(',',$this->input->post('id_logger'));
		$idlogger=$set[0];
		$controller=$set[1];
		$tabel=$set[2];

		redirect($controller.'/set_sensorselect/'.$idlogger.'/'.$tabel);
	}
}
