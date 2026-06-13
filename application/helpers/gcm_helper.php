<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Helper migrasi kontrol AWGC ke format GCM (Gate Control Module).
 *
 * Referensi: GCM_command_reference.md
 * Desain    : docs/superpowers/specs/2026-06-13-migrasi-gcm-kontrol-pintu-design.md
 *
 * Sumber kebenaran mapping = hasil "GCM GET" tiap logger (binding firmware):
 *   10349 -> id1=[5,1] id2=[6,1] id3=[7,1]            (Intake 1/2/3, slave 5/6/7)
 *   10350 -> id1=[1,1] id2=[2,1] id3=[3,1] id4=[4,1]  (Floodway 1-3 + Scouring, slave 1/2/3/4)
 *
 * mqtt_identifier lama "gcmN" (N = nomor slave) dipetakan ke
 * [id_logger tujuan, GCM module id 1..5].
 */

if ( ! function_exists('gcm_module_map'))
{
	/**
	 * @return array map mqtt_identifier => ['logger' => string, 'id' => int]
	 */
	function gcm_module_map()
	{
		return array(
			'gcm1' => array('logger' => '10350', 'id' => 1), // Floodway 1
			'gcm2' => array('logger' => '10350', 'id' => 2), // Floodway 2
			'gcm3' => array('logger' => '10350', 'id' => 3), // Floodway 3
			'gcm4' => array('logger' => '10350', 'id' => 4), // Scouring
			'gcm5' => array('logger' => '10349', 'id' => 1), // Intake 1
			'gcm6' => array('logger' => '10349', 'id' => 2), // Intake 2
			'gcm7' => array('logger' => '10349', 'id' => 3), // Intake 3
		);
	}
}

if ( ! function_exists('gcm_lookup'))
{
	/**
	 * Cari logger tujuan + GCM module id dari mqtt_identifier.
	 *
	 * @param  string $mqtt_identifier mis. "gcm5"
	 * @return array|null ['logger' => '10349', 'id' => 1] atau null jika tak dikenal
	 */
	function gcm_lookup($mqtt_identifier)
	{
		$map = gcm_module_map();
		$key = strtolower(trim((string) $mqtt_identifier));

		return isset($map[$key]) ? $map[$key] : NULL;
	}
}

if ( ! function_exists('gcm_topic'))
{
	/**
	 * Topik MQTT perintah server -> logger.
	 *
	 * @param  string|int $id_logger
	 * @return string mis. "sub_10349"
	 */
	function gcm_topic($id_logger)
	{
		return 'sub_'.$id_logger;
	}
}

if ( ! function_exists('gcm_gate_set_payload'))
{
	/**
	 * Bangun payload JSON GCM_GATE SET (gerak ke target posisi).
	 * `target` dikirim apa adanya (satuan cm/dm mengikuti nilai web).
	 *
	 * @param  int $gcm_id Module id 1..5
	 * @param  int $target Target posisi
	 * @return string JSON mis. {"GCM_GATE":{"cmd":"SET","id":1,"target":50}}
	 */
	function gcm_gate_set_payload($gcm_id, $target)
	{
		return json_encode(array(
			'GCM_GATE' => array(
				'cmd'    => 'SET',
				'id'     => (int) $gcm_id,
				'target' => (int) $target,
			),
		));
	}
}

if ( ! function_exists('gcm_gate_cmd_payload'))
{
	/**
	 * Bangun payload JSON GCM_GATE perintah motor manual.
	 * `cmd` "1" = open, "2" = close, "4" = stop (lihat GCM_command_reference.md).
	 * Dikirim sebagai string sesuai spesifikasi firmware.
	 *
	 * @param  int        $gcm_id Module id 1..5
	 * @param  string|int $cmd    "1" | "2" | "4"
	 * @return string JSON mis. {"GCM_GATE":{"cmd":"4","id":1}}
	 */
	function gcm_gate_cmd_payload($gcm_id, $cmd)
	{
		return json_encode(array(
			'GCM_GATE' => array(
				'cmd' => (string) $cmd,
				'id'  => (int) $gcm_id,
			),
		));
	}
}
