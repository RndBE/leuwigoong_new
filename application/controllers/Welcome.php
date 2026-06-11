<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller
{

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$data = $this->db->join('t_lokasi', 't_logger.lokasi_logger=t_lokasi.idlokasi')->join('kategori_logger', 't_logger.kategori_log=kategori_logger.id_katlogger')->get('t_logger')->result_array();
		$date_now = date('Y-m-d H:i:s');
		$date = date('Y-m-d H:i:s', strtotime('-1 hour', strtotime($date_now)));
		foreach ($data as $key => $val) {
			$data[$key]['sumber'] = 'Ciliwung';
			$data[$key]['status'] = 'aktif';
			$waktu = $this->db->get_where($val['temp_data'], array('code_logger' => $val['id_logger']))->row();
			$data[$key]['waktu'] = $waktu->waktu;
			if ($waktu->waktu < $date) {
				$data[$key]['status'] = 'nonaktif';
			}
		}
		echo json_encode($data);
	}
	
	public function rst (){
		$data_rst = $this->db->get('t_pintu')->result_array();
		$temp = $this->db->where('code_logger','10213')->get('temp_awgc')->row();
	
		$status_rst = [];
		$pesan = [];
		array_push($pesan, 'Pintu RST OFF :\n\n');
		array_push($pesan, '*Pos AWGC Cikeusik* :\n');
		$a = 1;
		foreach($data_rst as $key => $val) {
			$r = $val['r'];
			$s = $val['s'];
			$t = $val['t'];
			if($temp->$r != '1' and $temp->$s != '1' and $temp->$t != '1'){
				if(!in_array($val['nama_pintu'],$status_rst)){
					$status_rst[] = $val['nama_pintu'];
					array_push($pesan, $val['nama_pintu'] .', GCM : OFF \n');
				}
			}
			if($temp->$r != '1' or $temp->$s != '1' or $temp->$t != '1'){
				if(!in_array($val['nama_pintu'],$status_rst)){
					$status_rst[] = $val['nama_pintu'];
					array_push($pesan, $val['nama_pintu'] .', GCM : Abnormal \n');
				}
				
			}
		}
		
		if($status_rst){
			$message = implode('', $pesan);
			$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_URL => '103.82.241.100:3000/client/sendMessage/beacon',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS => '{
  "chatId": "120363042735897956@g.us",
  "contentType": "string",
  "content": "'.$message.'"}',
				CURLOPT_HTTPHEADER => array(
					'x-api-key: ',
					'Content-Type: application/json'
				),
			));

			$response = curl_exec($curl);

			curl_close($curl);
			echo $response;
		}
		
		//echo json_encode($data_rst);
	}
}
