<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Peta3d extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->config('peta3d');
	}

	public function index()
	{
		if (!$this->session->userdata('logged_in')) {
			redirect('login');
		}

		$data['markers'] = $this->get_markers();
		$data['cuaca'] = $this->get_weather();
		$this->load->view('konten/back/peta_3d', $data);
	}

	public function markers()
	{
		if (!$this->session->userdata('logged_in')) {
			show_error('Unauthorized', 401);
		}

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($this->get_markers()));
	}

	public function save_marker_position()
	{
		if (!$this->session->userdata('logged_in')) {
			show_error('Unauthorized', 401);
		}

		$id_logger = $this->input->post('id_logger', TRUE);
		$x = $this->input->post('x', TRUE);
		$y = $this->input->post('y', TRUE);

		if (!$id_logger || !is_numeric($x) || !is_numeric($y)) {
			show_error('Data posisi tidak valid', 400);
		}

		$x = max(0, min(100, (float) $x));
		$y = max(0, min(100, (float) $y));
		$positions = $this->read_saved_positions();
		$positions[(string) $id_logger] = array(
			'x' => round($x, 2),
			'y' => round($y, 2),
		);

		$written = @file_put_contents($this->positions_file(), json_encode($positions, JSON_PRETTY_PRINT));
		if ($written === false) {
			show_error('Gagal menyimpan posisi marker', 500);
		}

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode(array(
				'success' => true,
				'id_logger' => (string) $id_logger,
				'x' => $positions[(string) $id_logger]['x'],
				'y' => $positions[(string) $id_logger]['y'],
			)));
	}

	private function get_markers()
	{
		$positions = $this->config->item('peta3d_marker_positions');
		$saved_positions = $this->read_saved_positions();
		if (!isset($positions['by_id']) || !is_array($positions['by_id'])) {
			$positions['by_id'] = array();
		}
		foreach ($saved_positions as $id_logger => $position) {
			if (isset($position['x']) && isset($position['y'])) {
				$positions['by_id'][(string) $id_logger] = array(
					'x' => (float) $position['x'],
					'y' => (float) $position['y'],
				);
			}
		}
		$markers = array();
		$index = 0;
		$categories = $this->db->order_by('urut', 'asc')->get('kategori_logger')->result();

		foreach ($categories as $category) {
			$loggers = $this->db
				->select('t_logger.*, t_lokasi.nama_lokasi, t_lokasi.latitude, t_lokasi.longitude')
				->from('t_logger')
				->join('t_lokasi', 't_logger.lokasi_logger = t_lokasi.idlokasi')
				->where('kategori_log', $category->id_katlogger)
				->order_by('id_logger', 'asc')
				->get()
				->result();

			foreach ($loggers as $logger) {
				if ((string) $logger->id_logger === '10359') {
					continue;
				}

				$temp = $this->get_latest_temp($category->temp_data, $logger->id_logger);
				$status = $this->get_status($temp, $category->tabel);
				$parameter = $this->get_primary_parameter($logger->id_logger, $category->tabel, $temp);
				$position = $this->resolve_position($logger, $positions, $index);

				$markers[] = array(
					'id_logger' => $logger->id_logger,
					'nama_lokasi' => $logger->nama_lokasi,
					'kategori' => strtoupper($category->controller),
					'controller' => $category->controller,
					'status' => $status['status'],
					'status_class' => $status['class'],
					'waktu' => $status['waktu'],
					'status_sd' => $status['sdcard'],
					'latitude' => $logger->latitude,
					'longitude' => $logger->longitude,
					'nilai' => $parameter['nilai'],
					'satuan' => $parameter['satuan'],
					'nama_parameter' => $parameter['nama'],
					'gate_values' => $this->get_awgc_gate_values($logger->id_logger, $category->tabel, $temp),
					'link' => $parameter['link'],
					'x' => $position['x'],
					'y' => $position['y'],
				);

				$index++;
			}
		}

		return $markers;
	}

	private function get_awgc_gate_values($id_logger, $table, $temp)
	{
		if ($table !== 'awgc' || !$temp) {
			return array();
		}

		$gates = $this->db
			->where('id_logger', $id_logger)
			->order_by('id_pintu', 'asc')
			->get('t_pintu')
			->result_array();

		$values = array();
		foreach ($gates as $gate) {
			$sensor = isset($gate['sensor_level']) ? $gate['sensor_level'] : '';
			$value = '-';
			if ($sensor && isset($temp->{$sensor})) {
				$value = is_numeric($temp->{$sensor})
					? number_format($temp->{$sensor}, 0, '.', '')
					: $temp->{$sensor};
			}

			$values[] = array(
				'nama_pintu' => isset($gate['nama_pintu']) ? $gate['nama_pintu'] : 'Pintu',
				'nilai' => $value,
				'satuan' => isset($gate['satuan_level']) && $gate['satuan_level'] ? $gate['satuan_level'] : 'cm',
			);
		}

		return $values;
	}

	private function get_latest_temp($table, $id_logger)
	{
		if (!$this->db->table_exists($table)) {
			return null;
		}

		return $this->db
			->where('code_logger', $id_logger)
			->order_by('waktu', 'desc')
			->limit(1)
			->get($table)
			->row();
	}

	private function get_status($temp, $table)
	{
		if (!$temp || !isset($temp->waktu)) {
			return array(
				'status' => 'Tidak Ada Data',
				'class' => 'offline',
				'waktu' => '-',
				'sdcard' => '-',
			);
		}

		$latest_time = strtotime($temp->waktu);
		$online = $latest_time !== false && $latest_time >= strtotime('-1 hour');
		$sd_sensor = $table === 'awgc' ? 'sensor55' : 'sensor13';

		return array(
			'status' => $online ? 'Online' : 'Offline',
			'class' => $online ? 'online' : 'offline',
			'waktu' => $temp->waktu,
			'sdcard' => isset($temp->$sd_sensor) && $temp->$sd_sensor == '1' ? 'OK' : 'Bermasalah',
		);
	}

	private function get_primary_parameter($id_logger, $table, $temp)
	{
		$param = $this->db
			->where('logger_id', $id_logger)
			->order_by('parameter_utama', 'desc')
			->order_by('id_param', 'asc')
			->limit(1)
			->get('parameter_sensor')
			->row();

		if (!$param) {
			return array(
				'nama' => '-',
				'nilai' => '-',
				'satuan' => '',
				'link' => base_url('analisa'),
			);
		}

		$value = '-';
		if ($temp && isset($param->kolom_sensor) && isset($temp->{$param->kolom_sensor})) {
			$value = is_numeric($temp->{$param->kolom_sensor})
				? number_format($temp->{$param->kolom_sensor}, 2, '.', '')
				: $temp->{$param->kolom_sensor};
		}

		return array(
			'nama' => str_replace('_', ' ', $param->nama_parameter),
			'nilai' => $value,
			'satuan' => $param->satuan,
			'link' => base_url($table . '/set_sensordash?tabel=' . $table . '&id_param=' . $param->id_param . '&id_logger=' . $id_logger . '&jenis=logger'),
		);
	}

	private function resolve_position($logger, $positions, $index)
	{
		$id_logger = (string) $logger->id_logger;
		if (isset($positions['by_id'][$id_logger])) {
			return $positions['by_id'][$id_logger];
		}

		$name = strtolower($logger->nama_lokasi);
		foreach ($positions['name_rules'] as $keyword => $position) {
			if (strpos($name, strtolower($keyword)) !== false) {
				return $position;
			}
		}

		$fallback = $positions['fallback'];
		return $fallback[$index % count($fallback)];
	}

	private function positions_file()
	{
		return APPPATH . 'config/peta3d_positions.json';
	}

	private function read_saved_positions()
	{
		$file = $this->positions_file();
		if (!file_exists($file)) {
			return array();
		}

		$content = @file_get_contents($file);
		if (!$content) {
			return array();
		}

		$data = json_decode($content, true);
		return is_array($data) ? $data : array();
	}

	private function get_weather()
	{
		$fallback = (object) array(
			't' => '-',
			'ws' => '-',
			'weather_desc' => 'Cuaca',
			'image' => base_url('image/water.png'),
		);

		$context = stream_context_create(array(
			'http' => array(
				'timeout' => 3,
			),
		));
		$response = @file_get_contents('https://api.bmkg.go.id/publik/prakiraan-cuaca?adm4=32.05.06.2004', false, $context);
		if (!$response) {
			return $fallback;
		}

		$data = json_decode($response);
		if (!$data || !isset($data->data[0]->cuaca[0])) {
			return $fallback;
		}

		$weather_list = array_reverse($data->data[0]->cuaca[0]);
		$now = date('Y-m-d H:i');
		foreach ($weather_list as $weather) {
			if (isset($weather->local_datetime) && $weather->local_datetime <= $now) {
				return $weather;
			}
		}

		return count($weather_list) ? $weather_list[count($weather_list) - 1] : $fallback;
	}
}
