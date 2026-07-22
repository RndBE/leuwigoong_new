<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| Posisi marker menggunakan persentase terhadap gambar 3D.
| x = jarak dari kiri, y = jarak dari atas.
| Pakai id_logger untuk posisi paling presisi. name_rules dipakai sebagai fallback
| saat id logger belum dipetakan.
*/
$config['peta3d_marker_positions'] = array(
	'by_id' => array(
		'10349' => array('x' => 58.8, 'y' => 52.8), // Pos AWGC Intake
		'10350' => array('x' => 73.0, 'y' => 47.5), // Pos AWGC Scouring Floodway
		'10351' => array('x' => 39.2, 'y' => 54.0), // Pos Radio AP
		'10352' => array('x' => 31.2, 'y' => 61.2), // Pos IPCAM Rumah Jaga
		'10353' => array('x' => 48.6, 'y' => 52.5), // Pos IPCAM Intake
		'10354' => array('x' => 44.9, 'y' => 59.8), // Pos IPCAM Control Room
		'10355' => array('x' => 1.5, 'y' => 35.8), // Pos IPCAM Spillway
		'10356' => array('x' => 56.8, 'y' => 61.3), // Pos IPCAM Scouring Gate
		'10357' => array('x' => 76.3, 'y' => 67.4), // Pos IPCAM Floodway Gate
	),
	'name_rules' => array(
		'ipcam spillway' => array('x' => 1.5, 'y' => 35.8),
		'ipcam rumah jaga' => array('x' => 31.2, 'y' => 61.2),
		'radio ap' => array('x' => 39.2, 'y' => 54.0),
		'ipcam intake' => array('x' => 48.6, 'y' => 52.5),
		'ipcam control room' => array('x' => 44.9, 'y' => 59.8),
		'awgc intake' => array('x' => 58.8, 'y' => 52.8),
		'awlr hilir' => array('x' => 58.7, 'y' => 58.4),
		'ipcam scouring gate' => array('x' => 56.8, 'y' => 61.3),
		'scouring gate' => array('x' => 56.8, 'y' => 61.3),
		'awgc scouring floodway' => array('x' => 73.0, 'y' => 47.5),
		'scouring floodway' => array('x' => 73.0, 'y' => 47.5),
		'ipcam floodway gate' => array('x' => 76.3, 'y' => 67.4),
		'floodway gate' => array('x' => 76.3, 'y' => 67.4),
		'floodway dam' => array('x' => 82.2, 'y' => 52.5),
		'railway drains' => array('x' => 3.8, 'y' => 34.5),
		'floodway drains' => array('x' => 77.8, 'y' => 67.2),
	),
	'fallback' => array(
		array('x' => 42.0, 'y' => 55.0),
		array('x' => 48.0, 'y' => 55.0),
		array('x' => 54.0, 'y' => 53.0),
		array('x' => 60.0, 'y' => 49.0),
		array('x' => 66.0, 'y' => 45.0),
		array('x' => 72.0, 'y' => 42.0),
		array('x' => 78.0, 'y' => 52.0),
		array('x' => 82.0, 'y' => 62.0),
		array('x' => 36.0, 'y' => 63.0),
		array('x' => 7.0, 'y' => 37.0),
	),
);
