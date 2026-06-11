<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Beranda extends CI_Controller
{
	function __construct()
	{
		parent::__construct();

		$this->load->model('m_dashboard');
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

	public function index()
	{
		if ($this->session->userdata('logged_in')) {
			$kat2 = $this->db->order_by('urut','asc')->get('kategori_logger')->result();
			$garansi_habis = [];
			$ipcam = json_decode(file_get_contents('https://leuwigoong.beacontelemetry.com/ipcam/get_all_photo.php'),true);
			foreach ($kat2 as $key => $kt) {
				$tabel = $kt->temp_data;
				$kt->logger = $this->m_dashboard->get_logger2($kt->id_katlogger);
				if($kt->id_katlogger == '1'){
					foreach ($kt->logger as $log) {
						$id_logger = $log->id_logger;

						$data_logger = $this->m_dashboard->data($id_logger, $tabel);

						$data_pintu = $this->db->where('id_logger', $id_logger)->get('t_pintu')->result_array();

						foreach ($data_logger as $dt) {
							$cek_perbaikan = $this->db->get_where('t_perbaikan', array('id_logger' => $id_logger))->row();
							$waktu = $dt->waktu;
							$log->waktu = $dt->waktu;
							$awal = date('Y-m-d H:i', (mktime(date('H') - 1)));

							######### cek status koneksi ######
							if ($waktu >= $awal) {
								$log->color = "green";
								$log->status_logger = "Koneksi Terhubung";
							} else {
								$log->color = "dark";
								$log->status_logger = "Koneksi Terputus";
							}

							if ($cek_perbaikan) {
								$log->color = "warning";
								$log->status_logger = "Perbaikan";
							}

							if ($dt->sensor13 == '1') {
								$log->sdcard = 'OK';
							} else {
								$log->sdcard = 'Bermasalah';
							}
							$log->data_pintu = $data_pintu;
							foreach ($log->data_pintu as $key => $sw) {								
								$nilai_param = $this->db->where('code_logger', $id_logger)->get('temp_awgc')->row();
								$sn_pintu = $sw['sensor_level'];
								$param_level = $this->db->where('id_pintu',$sw['id_pintu'])->where('kolom_sensor', $sn_pintu)->get('parameter_pintu')->row();

								$phase_r = $sw['r'];
								$phase_s = $sw['s'];
								$phase_t = $sw['t'];
								$log->data_pintu[$key]['satuan_level'] = $param_level->satuan;
								$log->data_pintu[$key]['batas_atas'] = $sw['batas_atas'];
								$log->data_pintu[$key]['elevasi'] = $nilai_param->$sn_pintu;
								$log->data_pintu[$key]['r'] = $nilai_param->$phase_r;
								$log->data_pintu[$key]['s'] = $nilai_param->$phase_s;
								$log->data_pintu[$key]['t'] = $nilai_param->$phase_t;

								$get = 'tabel=awgc&id_param=' . $param_level->id_param . '&id_logger=' . $id_logger . '&jenis=pintu';
								$log->data_pintu[$key]['link'] = $get;
							}

							foreach ($this->m_dashboard->parameter($id_logger) as $param) {

								$kolom = $param->kolom_sensor;

								$get = 'tabel=' . $kt->tabel . '&id_param=' . $param->id_param . '&id_logger=' . $id_logger . '&jenis=logger';
								if($param->nama_parameter == 'Q_Floodway_1' and $id_logger == '10349'){
									$dta = $this->debitPintu1($dt->$kolom);
								}elseif($param->nama_parameter == 'Q_Floodway_2' and $id_logger == '10349'){
									$dta = $this->debitPintu2($dt->$kolom);
								}elseif($param->nama_parameter == 'Q_Floodway_3' and $id_logger == '10349'){
									$dta = $this->debitPintu3($dt->$kolom);
								}elseif($param->nama_parameter == 'Debit_Gabungan' and $id_logger == '10349'){
									$dta = $this->debitGabungan($dt->$kolom);
								}elseif($param->nama_parameter == 'Q_Scouring' and $id_logger == '10349'){
									$dta = $this->debitScouring($dt->$kolom);
								} else{
									$dta = $dt->$kolom;
								} 
								$log->parameter = $param->nama_parameter;

								$log->param[] = array(
									'nama_parameter' => $param->nama_parameter,
									'icon_sensor' => $param->icon_sensor,
									'nilai' => number_format($dta, 3,'.',''),
									'link' => base_url() . $kt->controller . '/set_sensordash?' . $get,
									'satuan' => $param->satuan
								);
							}
						}
					}
				}else{
					foreach ($kt->logger as $log) {
						$id_logger = $log->id_logger;
						$cek = isset($ipcam[$id_logger]);
						$ipcam_link = '';
						if($cek){
							$ipcam_link = $ipcam[$id_logger];
						}
						$data_logger = $this->m_dashboard->data($id_logger, $tabel);
						if($kt->nama_kategori == 'IPCAM'){
							$log->image = $ipcam_link;
						}
						foreach ($data_logger as $dt) {
							$cek_perbaikan = $this->db->get_where('t_perbaikan', array('id_logger' => $id_logger))->row();
							$waktu = $dt->waktu;
							$log->waktu = $dt->waktu;
							$awal = date('Y-m-d H:i', (mktime(date('H') - 1)));

							if ($waktu >= $awal) {
								$log->color = "green";
								$log->status_logger = "Koneksi Terhubung";
							} else {
								$log->color = "dark";
								$log->status_logger = "Koneksi Terputus";
							}

							if ($cek_perbaikan) {
								$log->color = "warning";
								$log->status_logger = "Perbaikan";
							}

							if ($dt->sensor13 == '1') {

								$log->sdcard = 'OK';
							} else {
								$log->sdcard = 'Bermasalah';
							}

							foreach ($this->m_dashboard->parameter($id_logger) as $param) {

								$kolom = $param->kolom_sensor;

								$get = 'tabel=' . $kt->tabel . '&id_param=' . $param->id_param . '&id_logger=' . $id_logger . '&jenis=logger';
								$dta = $dt->$kolom;
								$log->parameter = $param->nama_parameter;
								$log->param[] = array(
									'nama_parameter' => $param->nama_parameter,
									'icon_sensor' => $param->icon_sensor,
									'nilai' => number_format($dta, 2,'.',''),
									'link' => base_url() . $kt->tabel . '/set_sensordash?' . $get,
									'satuan' => $param->satuan
								);
							}
						}
					}
				}

			}
			$data['garansi_habis'] = $garansi_habis;

			$data['data_konten'] = $kat2;
			$data['konten'] = 'konten/back/v_beranda2';
			$this->load->view('template_admin/site', $data);
		} else {
			redirect('login');
		}
	}
	
	public function beranda2()
	{
		if ($this->session->userdata('logged_in')) {
			$kat2 = $this->db->order_by('urut','asc')->get('kategori_logger')->result();
			$garansi_habis = [];
			$ipcam = json_decode(file_get_contents('https://leuwigoong.beacontelemetry.com/ipcam/get_all_photo.php'),true);
			foreach ($kat2 as $key => $kt) {
				$tabel = $kt->temp_data;
				$kt->logger = $this->m_dashboard->get_logger2($kt->id_katlogger);
				if($kt->id_katlogger == '1'){
					foreach ($kt->logger as $log) {
						$id_logger = $log->id_logger;

						$data_logger = $this->m_dashboard->data($id_logger, $tabel);

						$data_pintu = $this->db->where('id_logger', $id_logger)->get('t_pintu')->result_array();

						foreach ($data_logger as $dt) {
							$cek_perbaikan = $this->db->get_where('t_perbaikan', array('id_logger' => $id_logger))->row();
							$waktu = $dt->waktu;
							$log->waktu = $dt->waktu;
							$awal = date('Y-m-d H:i', (mktime(date('H') - 1)));

							######### cek status koneksi ######
							if ($waktu >= $awal) {
								$log->color = "green";
								$log->status_logger = "Koneksi Terhubung";
							} else {
								$log->color = "dark";
								$log->status_logger = "Koneksi Terputus";
							}

							if ($cek_perbaikan) {
								$log->color = "warning";
								$log->status_logger = "Perbaikan";
							}

							if ($dt->sensor13 == '1') {
								$log->sdcard = 'OK';
							} else {
								$log->sdcard = 'Bermasalah';
							}
							$log->data_pintu = $data_pintu;
							foreach ($log->data_pintu as $key => $sw) {								
								$nilai_param = $this->db->where('code_logger', $id_logger)->get('temp_awgc')->row();
								$sn_pintu = $sw['sensor_level'];
								$param_level = $this->db->where('id_pintu',$sw['id_pintu'])->where('kolom_sensor', $sn_pintu)->get('parameter_pintu')->row();

								$phase_r = $sw['r'];
								$phase_s = $sw['s'];
								$phase_t = $sw['t'];
								$log->data_pintu[$key]['satuan_level'] = $param_level->satuan;
								$log->data_pintu[$key]['batas_atas'] = $sw['batas_atas'];
								$log->data_pintu[$key]['elevasi'] = $nilai_param->$sn_pintu;
								$log->data_pintu[$key]['r'] = $nilai_param->$phase_r;
								$log->data_pintu[$key]['s'] = $nilai_param->$phase_s;
								$log->data_pintu[$key]['t'] = $nilai_param->$phase_t;

								$get = 'tabel=awgc&id_param=' . $param_level->id_param . '&id_logger=' . $id_logger . '&jenis=pintu';
								$log->data_pintu[$key]['link'] = $get;
							}

							foreach ($this->m_dashboard->parameter($id_logger) as $param) {

								$kolom = $param->kolom_sensor;

								$get = 'tabel=' . $kt->tabel . '&id_param=' . $param->id_param . '&id_logger=' . $id_logger . '&jenis=logger';
								if($param->nama_parameter == 'Q_Floodway_1' and $id_logger == '10349'){
									$dta = $this->debitPintu1($dt->$kolom);
								}elseif($param->nama_parameter == 'Q_Floodway_2' and $id_logger == '10349'){
									$dta = $this->debitPintu2($dt->$kolom);
								}elseif($param->nama_parameter == 'Q_Floodway_3' and $id_logger == '10349'){
									$dta = $this->debitPintu3($dt->$kolom);
								}elseif($param->nama_parameter == 'Debit_Gabungan' and $id_logger == '10349'){
									$dta = $this->debitGabungan($dt->$kolom);
								}elseif($param->nama_parameter == 'Q_Scouring' and $id_logger == '10349'){
									$dta = $this->debitScouring($dt->$kolom);
								} else{
									$dta = $dt->$kolom;
								} 
								$log->parameter = $param->nama_parameter;

								$log->param[] = array(
									'nama_parameter' => $param->nama_parameter,
									'icon_sensor' => $param->icon_sensor,
									'nilai' => number_format($dta, 3,'.',''),
									'link' => base_url() . $kt->controller . '/set_sensordash?' . $get,
									'satuan' => $param->satuan
								);
							}
						}
					}
				}else{
					foreach ($kt->logger as $log) {
						$id_logger = $log->id_logger;
						$cek = isset($ipcam[$id_logger]);
						$ipcam_link = '';
						if($cek){
							$ipcam_link = $ipcam[$id_logger];
						}
						$data_logger = $this->m_dashboard->data($id_logger, $tabel);
						if($kt->nama_kategori == 'IPCAM'){
							$log->image = $ipcam_link;
						}
						foreach ($data_logger as $dt) {
							$cek_perbaikan = $this->db->get_where('t_perbaikan', array('id_logger' => $id_logger))->row();
							$waktu = $dt->waktu;
							$log->waktu = $dt->waktu;
							$awal = date('Y-m-d H:i', (mktime(date('H') - 1)));

							if ($waktu >= $awal) {
								$log->color = "green";
								$log->status_logger = "Koneksi Terhubung";
							} else {
								$log->color = "dark";
								$log->status_logger = "Koneksi Terputus";
							}

							if ($cek_perbaikan) {
								$log->color = "warning";
								$log->status_logger = "Perbaikan";
							}

							if ($dt->sensor13 == '1') {

								$log->sdcard = 'OK';
							} else {
								$log->sdcard = 'Bermasalah';
							}

							foreach ($this->m_dashboard->parameter($id_logger) as $param) {

								$kolom = $param->kolom_sensor;

								$get = 'tabel=' . $kt->tabel . '&id_param=' . $param->id_param . '&id_logger=' . $id_logger . '&jenis=logger';
								$dta = $dt->$kolom;
								$log->parameter = $param->nama_parameter;
								$log->param[] = array(
									'nama_parameter' => $param->nama_parameter,
									'icon_sensor' => $param->icon_sensor,
									'nilai' => number_format($dta, 2,'.',''),
									'link' => base_url() . $kt->tabel . '/set_sensordash?' . $get,
									'satuan' => $param->satuan
								);
							}
						}
					}
				}

			}
			$data['garansi_habis'] = $garansi_habis;

			$data['data_konten'] = $kat2;
			$data['konten'] = 'konten/back/v_beranda3';
			$this->load->view('template_admin/site', $data);
		} else {
			redirect('login');
		}
	}

	public function mqtt_send () {
		$kat2 = $this->m_dashboard->kategori_logger();
		foreach ($kat2 as $key => $kt) {
			$tabel = $kt->temp_data;
			$kt->logger = $this->m_dashboard->get_logger2($kt->id_katlogger);
			foreach ($kt->logger as $log) {
				$id_logger = $log->id_logger;
				$data_logger = $this->m_dashboard->data($id_logger, $tabel);
				$data_pintu = $this->db->where('id_logger', $id_logger)->get('t_pintu')->result_array();

				foreach ($data_logger as $dt) {
					$cek_perbaikan = $this->db->get_where('t_perbaikan', array('id_logger' => $id_logger))->row();
					$waktu = $dt->waktu;
					$log->waktu = $dt->waktu;
					$awal = date('Y-m-d H:i', (mktime(date('H') - 1)));

					######### cek status koneksi ######
					if ($waktu >= $awal) {
						$log->color = "green";
						$log->status_logger = "Koneksi Terhubung";
					} else {
						$log->color = "dark";
						$log->status_logger = "Koneksi Terputus";
					}

					if ($cek_perbaikan) {
						$log->color = "warning";
						$log->status_logger = "Perbaikan";
					}

					if ($dt->sensor13 == '1') {

						$log->sdcard = 'OK';
					} else {
						$log->sdcard = 'Bermasalah';
					}
					$rerata = 0;
					$log->data_pintu = $data_pintu;
					foreach ($log->data_pintu as $key => $sw) {
						$nilai_param = $this->db->where('code_logger', $id_logger)->get('temp_awgc')->row();
						$sn_pintu = $sw['sensor_level'];
						$phase_r = $sw['r'];
						$phase_s = $sw['s'];
						$phase_t = $sw['t'];
						$log->data_pintu[$key]['batas_atas'] = $sw['batas_atas'];
						$log->data_pintu[$key]['elevasi'] = $nilai_param->$sn_pintu;
						$log->data_pintu[$key]['r'] = $nilai_param->$phase_r;
						$log->data_pintu[$key]['s'] = $nilai_param->$phase_s;
						$log->data_pintu[$key]['t'] = $nilai_param->$phase_t;
						if($sw['id_pintu'] != '217' and $sw['id_pintu'] != '216'){
							$rerata += $nilai_param->$sn_pintu;
						}
					}
					$avg_bukaan = $rerata/6/100 -0.5;
					foreach ($this->m_dashboard->parameter($id_logger) as $param) {

						$kolom = $param->kolom_sensor;

						$get = 'tabel=' . $kt->tabel . '&id_param=' . $param->id_param . '&id_logger=' . $id_logger . '&jenis=logger';
						$dta = $dt->$kolom;
						$log->parameter = $param->nama_parameter;
						if($log->parameter =='Debit_Intake'){
							$n =  number_format(1 * 0.8 * $avg_bukaan * 10.02 * pow((19.62 * 0.15),0.5), 3);
							$log->param[] = array(
								'nama_parameter' => $param->nama_parameter,
								'nilai' => ($n < 0) ? '0' : $n,
								'link' => base_url() . $kt->controller . '/set_sensordash?' . $get,
								'satuan' => 'm³/det'
							);
						}else{
							$log->param[] = array(
								'nama_parameter' => $param->nama_parameter,
								'nilai' => number_format($dta, 2),
								'link' => base_url() . $kt->controller . '/set_sensordash?' . $get,
								'satuan' => $param->satuan
							);
						}

					}
				}
			}
		}
		echo json_encode($kat2[0]->logger[0]);
	}
}
