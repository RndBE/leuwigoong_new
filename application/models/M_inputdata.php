<?php
class M_inputdata extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		$this->db = $this->load->database('default', true);
	}

	function view_awgc($log_id)
	{
		$awal = date('Y-m-d H:i', (mktime(0, 0, 0, date('m'), date('d'), date('Y'))));
		$this->db->select('*');
		$this->db->where('code_logger', $log_id);
		$this->db->where('waktu >=', $awal);
		$this->db->order_by('waktu', 'desc');
		$query = $this->db->get('awgc');
		return $query;
	}

	function add_awgc($data)
	{
		$this->db->insert('awgc', $data);
		return;
	}
	function update_tempawgc($idlogger, $data)
	{
		$this->db->where('code_logger', $idlogger);
		$this->db->update('temp_awgc', $data);
		return;
	}
}
