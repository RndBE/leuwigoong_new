
<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Analisa extends CI_Controller
{

	function __construct()
	{
		parent::__construct();

		$this->load->model('m_analisa');
	}

	public function index()
	{
		if ($this->session->userdata('logged_in')) {
			$this->load->library('googlemaps');
			// BAru
			$kategori = array();
			$query_kategori = $this->db->query('select * from kategori_logger');
			//$klasifikasi
			foreach ($query_kategori->result()  as $kat) {
				$tabel = $kat->tabel;
				$tabel_temp = $kat->temp_data;
				$content = array();
				$bidang = $this->session->userdata['bidang'];
				if ($this->session->userdata['leveluser'] == 'admin' or $this->session->userdata['leveluser'] == 'user') {
					$query_lokasilogger = $this->db->query("select * from t_logger inner join t_lokasi ON t_logger.lokasi_logger=t_lokasi.idlokasi where kategori_log='$kat->id_katlogger'");
				} else {
					$query_lokasilogger = $this->db->query("select * from t_logger inner join t_lokasi ON t_logger.lokasi_logger=t_lokasi.idlokasi where kategori_log='$kat->id_katlogger' and t_logger.bidang='$bidang' ");
				}

				foreach ($query_lokasilogger->result() as $loklogger) {
					$id_logger = $loklogger->id_logger;

					$parameter = array();
					$query_data = $this->db->query('select * from ' . $tabel_temp . ' where code_logger="' . $id_logger . '"');
					$level = $this->db->where('id_logger',$id_logger)->get('t_pintu')->result_array();
					foreach ($query_data->result() as $dt) {
						$waktu = $dt->waktu;
						$awal=date('Y-m-d H:i',(mktime(date('H')-1)));

						if ($waktu >= $awal) {

							$icon_marker = base_url() . 'pin_marker/' . $kat->controller . '-hijau.png';
							$status = '<p style="color:green;margin-bottom:0px">Koneksi Terhubung</p>';
							$statlog = 'aman';
							$statuspantau = "Tingkat Status Belum Diatur";
							$anim = " ";
						} else {
							$icon_marker = base_url() . 'pin_marker/' . $kat->controller . '-hitam.png';
							$status = '<p style="color:red;margin-bottom:0px">Koneksi Terputus</p>';
							$statlog = 'off';
							$statuspantau = "-";
							$anim = "BOUNCE";
						}
					}
					// create marker for each province
					$pintu = '';
					foreach($level as $key =>$vl){
						$lv = $vl['sensor_level'];
						$elev_cm = round( $query_data->row()->$lv *110/100);
						$pintu .= '<div class="col-6"><div class="card py-2 text-center mb-2"><h4 class="mb-0 fw-normal">'.$vl["nama_pintu"].'</h4><h3 class="mb-0"> '. $query_data->row()->$lv.' cm </h3></div></div>' ;
					}
					$marker['position'] = $loklogger->latitude . ',' . $loklogger->longitude;

					$content =
						"<h3 class='mt-3' style='color:#333;'><strong>" . $loklogger->nama_logger . "</strong></h3>" .
						"<table style='color:#333;' class='table card-table table-striped'>" .
						"<tbody>" .
						"<tr>" .
						"<td>Nama Pos </td><td>: </td> <td>" . $loklogger->nama_lokasi . "</td>" .
						"</tr>" .
						"<td>Waktu </td><td>: </td> <td>" . $waktu . "</td>" .
						"</tr>" .
						"<tr>" .
						"<td>Latitude </td><td>: </td> <td>" . $loklogger->latitude . "</td>" .
						"</tr>" .
						"<tr>" .
						"<td>Longitude </td><td>: </td> <td>" . $loklogger->longitude . "</td>" .
						"</tr>" .
						"<tr>" .
						"<td>Status Logger</td><td>:</td> <td>" . $status . "</td>" .
						"</tr>" .
						
						"</tbody>" .
						"</table>" .
						"<div class='row mt-3 justify-content-center' style='max-width:340px'>".
						$pintu.
						
						"</div><div class='col-md-12 mt-3'> <center>" . anchor($kat->controller . '/set_sensordash?tabel=awgc&id_logger=' . $id_logger . '&jenis=logger', "<strong> Lihat Data </strong> ") . " </center></div>";

					$marker['infowindow_content'] = $content;
					$marker['title'] = $loklogger->nama_lokasi;
					$marker['icon'] = $icon_marker;
					$marker['animation'] = $anim;
					$marker['category'] = $kat->controller;
					$marker['category_group'] = $kat->controller . '_' . $statlog;
					$marker['icon_scaledSize'] = '25,33';

					$this->googlemaps->add_marker($marker);
				}
			}
			//$data['dt_sensor']=$dataSensor;
			$config['center'] = '-6.626915, 106.829139';
			//	$config['zoom'] = $this->session->userdata('zoom'); //zoom value
			$config['zoom'] = "17";
			$this->googlemaps->initialize($config);
			$data['map'] = $this->googlemaps->create_map();
			$data['konten'] = "konten/back/v_analisa";
			$this->load->view('template_admin/site', $data);
		} else {
			redirect('login');
		}
	}

	function combologger()
	{
		$set = explode(',', $this->input->post('id_logger'));
		$idlogger = $set[0];
		$controller = $set[1];
		$tabel = $set[2];

		redirect($controller . '/set_sensordash?tabel=awgc&id_logger=' . $idlogger . '&jenis=logger');
	}
}
