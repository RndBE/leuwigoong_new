<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Debit helper — Bendung Leuwigoong
 *
 * Sumber data : tabel rating resmi "ele.v (1).xlsx" (sheet FLOOD GATE &
 * SCOURING SLUICE GATE).
 *
 * Rumus dasar pada Excel : Q = C · a · B · √(2 · g · h)
 *   C = 0.65 (koefisien debit)
 *   B = 12.50 m (Flood Gate) / 5.00 m (Scouring Sluice Gate)
 *   a = bukaan pintu (m), g = 9.81 m/s², h = tinggi tekan (m)
 *
 * Nilai-nilai tabel di bawah disalin APA ADANYA dari hasil perhitungan
 * Excel (skenario muka air = mercu 687.75 + bukaan) dan dipakai lewat
 * lookup + interpolasi linear — TIDAK dihitung ulang dengan rumus —
 * supaya angka yang tampil identik dengan tabel rating resmi.
 *
 * Input fungsi = TMA BENDUNG dalam meter (sensor1 logger 10349, rentang
 * data 0 s/d ±8 m). Pada skenario Excel (muka air = mercu 687.75 + a),
 * TMA relatif di atas mercu setara dengan bukaan a, sehingga tabel
 * di-indeks langsung oleh nilai TMA tersebut (0.000 s/d 5.750).
 *
 * ATURAN: TMA bendung 0 (atau negatif) berarti tidak ada aliran —
 * semua debit floodway dikembalikan 0.
 *
 * Catatan konsistensi (terverifikasi terhadap Excel):
 *   - Q pintu-2 identik dengan pintu-1 (kolom D = E, dimensi pintu sama),
 *     sehingga keduanya memakai satu tabel yang sama.
 *   - Debit Gabungan = Q pintu-1 + Q pintu-2 + Q pintu-3 + Q scouring.
 *   - Titik a = 0.000 diambil dari baris 50 Excel: floodway = 0,
 *     gabungan/scouring = 168.20 (aliran saat muka air tepat di mercu).
 * Input di luar rentang tabel di-clamp ke nilai ujung tabel.
 */

if (!function_exists('debit_normalisasi')) {
	/** Normalisasi input sensor: "1,25" / "1.25" / angka → float. */
	function debit_normalisasi($x)
	{
		return floatval(str_replace(',', '.', trim((string) $x)));
	}
}

if (!function_exists('debit_interpolasi')) {
	/**
	 * Interpolasi linear pada tabel rating [x, Q] yang sudah terurut naik.
	 * Input berupa string "1,25" / "1.25" / angka; di-clamp ke ujung tabel.
	 */
	function debit_interpolasi(array $table, $x)
	{
		$x = debit_normalisasi($x);
		$n = count($table);

		if ($x <= $table[0][0]) return $table[0][1];
		if ($x >= $table[$n - 1][0]) return $table[$n - 1][1];

		for ($i = 0; $i < $n - 1; $i++) {
			$x1 = $table[$i][0];     $y1 = $table[$i][1];
			$x2 = $table[$i + 1][0]; $y2 = $table[$i + 1][1];

			if ($x >= $x1 && $x <= $x2) {
				return $y1 + (($y2 - $y1) * ($x - $x1) / ($x2 - $x1));
			}
		}

		return null;
	}
}

if (!function_exists('debit_pintu1')) {
	/**
	 * Q Floodway pintu 1 (m³/s) dari TMA bendung (m).
	 * Excel kolom B → D ("Q x 1 Gate"). Dipakai juga oleh pintu 2
	 * karena kolom E identik dengan kolom D.
	 */
	function debit_pintu1($tma_bendung)
	{
		// Aturan: TMA bendung 0 → tidak ada aliran, debit floodway 0.
		if (debit_normalisasi($tma_bendung) <= 0) return 0;

		static $table = [
			[0.000,0.0],
			[0.050,0.497],[0.100,0.994],[0.150,1.491],[0.200,1.988],[0.250,2.485],
			[0.300,4.236],[0.350,4.942],[0.400,5.648],[0.450,6.354],[0.500,7.06],
			[0.550,9.54],[0.600,10.40],[0.650,11.27],[0.700,12.14],[0.750,13.01],
			[0.800,16.08],[0.850,17.18],[0.900,18.09],[0.950,19.09],[1.000,20.10],
			[1.050,23.67],[1.100,24.80],[1.150,25.93],[1.200,27.06],[1.250,28.19],
			[1.300,32.23],[1.350,33.46],[1.400,34.70],[1.450,35.94],[1.500,37.19],
			[1.550,41.66],[1.600,43.00],[1.650,44.35],[1.700,45.69],[1.750,47.04],
			[1.800,51.90],[1.850,53.34],[1.900,54.78],[1.950,56.22],[2.000,57.67],
			[2.250,69.06],[2.500,81.17],[2.750,93.97],[3.000,107.44],[3.250,121.57],
			[3.500,136.34],[3.750,151.73],[4.000,167.73],[4.250,184.33],[4.500,201.52],
			[4.750,219.30],[5.000,237.65],[5.250,256.56],[5.500,276.04],[5.750,296.08]
		];

		return debit_interpolasi($table, $tma_bendung);
	}
}

if (!function_exists('debit_pintu2')) {
	/**
	 * Q Floodway pintu 2 (m³/s) dari TMA bendung (m).
	 * Excel kolom B → E ("Q x 2 Gate") — identik dengan kolom D (pintu 1).
	 * Aturan TMA 0 → 0 ikut berlaku lewat debit_pintu1().
	 */
	function debit_pintu2($tma_bendung)
	{
		return debit_pintu1($tma_bendung);
	}
}

if (!function_exists('debit_pintu3')) {
	/**
	 * Q Floodway pintu 3 (m³/s) dari TMA bendung (m).
	 * Excel kolom B → F ("Q x 3 Gate").
	 */
	function debit_pintu3($tma_bendung)
	{
		// Aturan: TMA bendung 0 → tidak ada aliran, debit floodway 0.
		if (debit_normalisasi($tma_bendung) <= 0) return 0;

		static $table = [
			[0.000,0.0],
			[0.050,0.496],[0.100,0.992],[0.150,1.488],[0.200,1.984],[0.250,2.48],
			[0.300,4.218],[0.350,4.921],[0.400,5.624],[0.450,6.327],[0.500,7.03],
			[0.550,9.482],[0.600,10.34],[0.650,11.20],[0.700,12.06],[0.750,12.93],
			[0.800,15.95],[0.850,16.94],[0.900,17.94],[0.950,18.94],[1.000,19.94],
			[1.050,23.44],[1.100,24.56],[1.150,25.67],[1.200,26.79],[1.250,27.91],
			[1.300,31.84],[1.350,33.06],[1.400,34.28],[1.450,35.51],[1.500,36.74],
			[1.550,41.06],[1.600,42.38],[1.650,43.71],[1.700,45.03],[1.750,46.36],
			[1.800,51.05],[1.850,52.47],[1.900,53.89],[1.950,55.31],[2.000,56.73],
			[2.250,67.78],[2.500,79.50],[2.750,91.84],[3.000,104.78],[3.250,118.31],
			[3.500,132.39],[3.750,147.01],[4.000,162.16],[4.250,177.81],[4.500,193.97],
			[4.750,210.61],[5.000,227.72],[5.250,245.29],[5.500,263.32],[5.750,281.79]
		];

		return debit_interpolasi($table, $tma_bendung);
	}
}

if (!function_exists('debit_gabungan')) {
	/**
	 * Debit gabungan 3 Floodway + Scouring (m³/s) dari TMA bendung (m).
	 * Excel kolom B → G ("Remaks:Q 3FG+S.Gate") = D + E + F + K.
	 * TMA 0 → 168.20 sesuai Excel (komponen scouring tetap dihitung).
	 */
	function debit_gabungan($tma_bendung)
	{
		static $table = [
			[0.000,168.20],
			[0.050,172.49],[0.100,175.88],[0.150,179.27],[0.200,182.66],[0.250,186.45],
			[0.300,194.53],[0.350,198.55],[0.400,202.59],[0.450,206.60],[0.500,210.64],
			[0.550,220.67],[0.600,225.17],[0.650,231.61],[0.700,234.21],[0.750,238.76],
			[0.800,250.21],[0.850,255.32],[0.900,260.07],[0.950,264.99],[1.000,269.95],
			[1.050,282.86],[1.100,288.17],[1.150,293.47],[1.200,298.78],[1.250,304.10],
			[1.300,318.09],[1.350,323.69],[1.400,329.32],[1.450,334.96],[1.500,340.63],
			[1.550,355.57],[1.600,361.50],[1.650,367.45],[1.700,373.38],[1.750,379.34],
			[1.800,395.35],[1.850,401.57],[1.900,407.80],[1.950,414.02],[2.000,420.28],
			[2.250,463.11],[2.500,508.04],[2.750,554.58],[3.000,602.88],[3.250,652.66],
			[3.500,704.27],[3.750,757.47],[4.000,812.42],[4.250,868.58],[4.500,926.12],
			[4.750,985.31],[5.000,1045.92],[5.250,1107.93],[5.500,1171.21],[5.750,1235.76]
		];

		return debit_interpolasi($table, $tma_bendung);
	}
}

if (!function_exists('debit_scouring')) {
	/**
	 * Q Scouring Sluice Gate (m³/s) dari TMA bendung (m).
	 * Excel kolom I → K ("Q x S Gate").
	 * TMA 0 → 168.20 sesuai Excel (aliran saat muka air tepat di mercu).
	 */
	function debit_scouring($tma_bendung)
	{
		static $table = [
			[0.000,168.20],
			[0.050,171.00],[0.100,172.90],[0.150,174.80],[0.200,176.70],[0.250,179.00],
			[0.300,181.84],[0.350,183.75],[0.400,185.67],[0.450,187.58],[0.500,189.50],
			[0.550,192.11],[0.600,194.03],[0.650,195.95],[0.700,197.87],[0.750,199.80],
			[0.800,202.10],[0.850,204.02],[0.900,205.95],[0.950,207.87],[1.000,209.80],
			[1.050,212.08],[1.100,214.01],[1.150,215.94],[1.200,217.87],[1.250,219.80],
			[1.300,221.79],[1.350,223.71],[1.400,225.64],[1.450,227.57],[1.500,229.50],
			[1.550,231.19],[1.600,233.12],[1.650,235.04],[1.700,236.97],[1.750,238.90],
			[1.800,240.50],[1.850,242.42],[1.900,244.35],[1.950,246.27],[2.000,248.20],
			[2.250,257.20],[2.500,266.20],[2.750,274.80],[3.000,283.20],[3.250,291.20],
			[3.500,299.20],[3.750,307.00],[4.000,314.80],[4.250,322.10],[4.500,329.10],
			[4.750,336.10],[5.000,342.90],[5.250,349.50],[5.500,355.80],[5.750,361.80]
		];

		return debit_interpolasi($table, $tma_bendung);
	}
}

if (!function_exists('debit_by_elevasi')) {
	/**
	 * Debit gabungan (m³/s) dari ELEVASI muka air (m), bukan bukaan.
	 * Excel kolom C → G; baris di bawah mercu (685.75–687.75) ikut disertakan.
	 */
	function debit_by_elevasi($elevasi)
	{
		static $table = [
			[685.75,92.61],[685.80,94.50],[685.85,96.39],[685.90,98.28],[685.95,100.17],
			[686.00,102.06],[686.05,103.95],[686.10,105.84],[686.15,107.73],[686.20,109.62],
			[686.25,111.51],[686.30,113.40],[686.35,115.29],[686.40,117.18],[686.45,119.07],
			[686.50,120.96],[686.55,122.85],[686.60,124.75],[686.65,126.63],[686.70,128.52],
			[686.75,130.41],[686.80,132.30],[686.85,134.19],[686.90,136.08],[686.95,137.97],
			[687.00,139.86],[687.05,141.75],[687.10,143.64],[687.15,145.53],[687.20,147.42],
			[687.25,149.31],[687.30,151.20],[687.35,153.09],[687.40,154.98],[687.45,156.87],
			[687.50,158.76],[687.55,160.65],[687.60,162.54],[687.65,164.43],[687.70,166.32],
			[687.75,168.20],[687.80,172.49],[687.85,175.88],[687.90,179.27],[687.95,182.66],
			[688.00,186.45],[688.05,194.53],[688.10,198.55],[688.15,202.59],[688.20,206.60],
			[688.25,210.64],[688.30,220.67],[688.35,225.17],[688.40,231.61],[688.45,234.21],
			[688.50,238.76],[688.55,250.21],[688.60,255.32],[688.65,260.07],[688.70,264.99],
			[688.75,269.95],[688.80,282.86],[688.85,288.17],[688.90,293.47],[688.95,298.78],
			[689.00,304.10],[689.05,318.09],[689.10,323.69],[689.15,329.32],[689.20,334.96],
			[689.25,340.63],[689.30,355.57],[689.35,361.50],[689.40,367.45],[689.45,373.38],
			[689.50,379.34],[689.55,395.35],[689.60,401.57],[689.65,407.80],[689.70,414.02],
			[689.75,420.28],[690.00,463.11],[690.25,508.04],[690.50,554.58],[690.75,602.88],
			[691.00,652.66],[691.25,704.27],[691.50,757.47],[691.75,812.42],[692.00,868.58],
			[692.25,926.12],[692.50,985.31],[692.75,1045.92],[693.00,1107.93],[693.25,1171.21],
			[693.50,1235.76]
		];

		return debit_interpolasi($table, $elevasi);
	}
}
