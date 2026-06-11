<?php 
class Pengaturan extends CI_Controller {
	
	public function log_user() {
		$data = $this->db->get('log_user')->result_array();
		$browser = $this->agent->browser();
		$version = $this->agent->version();
		 $user_ip = $this->input->ip_address();
        
        // Display or use the IP address
        echo "User IP Address: " . $user_ip;
		$url = "http://ip-api.com/json/" . $user_ip;
		$response = file_get_contents($url);
		$location_data = json_decode($response, true);

		// Check if the response is valid
		if ($location_data && $location_data['status'] === 'success') {
			echo "IP Address: " . $location_data['query'] . "<br>";
			echo "City: " . $location_data['city'] . "<br>";
			echo "Region: " . $location_data['regionName'] . "<br>";
			echo "Country: " . $location_data['country'] . "<br>";
			echo "Latitude: " . $location_data['lat'] . "<br>";
			echo "Longitude: " . $location_data['lon'] . "<br>";
		} else {
			echo "Location data could not be retrieved.";
		}
		//echo json_encode($data);
		exit;
		$data['data_konten'] = $kat2;
		$data['konten'] = 'konten/back/log_user';
		$this->load->view('template_admin/site', $data);
	}
}