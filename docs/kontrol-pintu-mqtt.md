# Dokumentasi Kontrol Pintu via MQTT — AWGC Leuwigoong

Dokumen ini menjelaskan protokol MQTT yang dipakai aplikasi untuk mengontrol
pintu air (AWGC) dan memantau statusnya secara realtime.

## 1. Broker

| Item | Nilai |
|---|---|
| Host | `mqtt.beacontelemetry.com` |
| Port server-side (PHP, TLS) | `8883` — CA: `/etc/ssl/certs/ca-bundle.crt` |
| Port browser (WebSocket SSL, Paho JS) | `8083` |
| Username / Password | `userlog` / `b34c0n` (hardcoded di kode — sebaiknya dipindah ke config) |

## 2. Identitas pintu (`t_pintu.mqtt_identifier`)

Perintah ke device tidak memakai `id_pintu`, tapi `mqtt_identifier` (GCM = Gate
Control Module):

| GCM | Pintu | id_pintu | Logger |
|---|---|---|---|
| `gcm1` | Floodway 1 | 210 | 10350 |
| `gcm2` | Floodway 2 | 211 | 10350 |
| `gcm3` | Floodway 3 | 212 | 10350 |
| `gcm4` | Scouring | 213 | 10350 |
| `gcm5` | Intake 1 | 214 | 10349 |
| `gcm6` | Intake 2 | 215 | 10349 |
| `gcm7` | Intake 3 | 216 | 10349 |

## 3. Topik

### 3.1 `AWGC_Garut_Copong` — perintah ke device (server → logger)

Satu-satunya topik perintah. Device (logger AWGC) subscribe ke topik ini.
Format umum payload:

```json
{
  "set_<id_logger>": {
    "command": "set",
    "setting": "<jenis_perintah>",
    "data": <tergantung jenis>
  }
}
```

Jenis perintah (`setting`):

| `setting` | Fungsi | Format `data` | Sumber kode |
|---|---|---|---|
| `gcm` | Gerakkan **satu** pintu | `["<gcm>", <level>, "1", "0"]` (array datar) | `Api.php` `lanjut_kontrol()` |
| `multi_gcm` | Gerakkan **beberapa** pintu sekaligus | `[["<gcm>", <level>, 1, 0], ...]` (array of array) | `Kontrol.php` `lanjut_kontrol()` |
| `ews_onoff` | Nyalakan/matikan sirine EWS | `["1"]` = on, `["0"]` = off | `Kontrol.php`, `Api.php` |
| `tma` | Kalibrasi nilai TMA | `"<nilai>"` (string) | `Awgc.php` `submit_kalibrasi()` |

Elemen array `gcm`/`multi_gcm`: `[mqtt_identifier, set_point_level, 1, 0]` —
set point memakai satuan level pintu (`t_pintu.satuan_level`: cm untuk
Floodway/Intake, dm untuk Scouring). Elemen ke-3/ke-4 selalu `1`/`0` di kode
web (semantik persisnya mengikuti firmware logger).

Contoh — buka Floodway 1 ke 250 cm dan Floodway 2 ke 100 cm:

```json
{
  "set_10350": {
    "command": "set",
    "setting": "multi_gcm",
    "data": [["gcm1", 250, 1, 0], ["gcm2", 100, 1, 0]]
  }
}
```

Contoh — matikan sirine EWS logger 10350:

```json
{ "set_10350": { "command": "set", "setting": "ews_onoff", "data": ["0"] } }
```

Tes manual dari terminal (**hati-hati: ini menggerakkan pintu air sungguhan**):

```bash
mosquitto_pub -h mqtt.beacontelemetry.com -p 8883 --capath /etc/ssl/certs \
  -u userlog -P 'b34c0n' -t 'AWGC_Garut_Copong' \
  -m '{"set_10350":{"command":"set","setting":"multi_gcm","data":[["gcm1",250,1,0]]}}'
```

### 3.2 `kontrol_pintu-<id_logger>` — status lock kontrol (server → UI)

Browser halaman kontrol subscribe ke topik ini (WebSocket :8083) untuk
mengunci tombol kontrol saat ada operator lain yang sedang memegang kontrol.

```json
{ "status_kontrol": 1, "id_logger": "10350", "session_id": "<session operator>" }
```

- `status_kontrol = 1` → UI lain di-disable (`session_id` = pemegang kontrol)
- `status_kontrol = 0` → kontrol selesai/bebas

Dipublish oleh: `Kontrol.php`/`Api.php`/`Api2.php` `lanjut_kontrol*()` (saat
mulai), dan `Datamasuk.php` `add_awgc()`/`add_awgc_json()` (setiap data masuk,
hasil evaluasi sensor Status AWGC).

### 3.3 `awgc-<id_logger>` — notifikasi data masuk (server → UI)

Dipublish `Datamasuk.php` setiap kali logger mengirim data:

```json
{ "id_logger": "10350", "waktu": "2026-06-12 20:22:00" }
```

UI yang menerimanya akan menarik ulang panel elevasi (`awgc/temp_ajax`) dan
status kontrol (`kontrol/status_kontrol`) via AJAX.

### 3.4 Topik lain

- `kontrol_pintu` (tanpa suffix id) — legacy, masih dipublish `Kontrol.php
  selesai()` dan `Api.php lanjut_kontrol*()`; tidak ada subscriber di kode web.
- `Awgc_garut_copong` (huruf kecil, `Api.php`) — kemungkinan typo; topik MQTT
  case-sensitive sehingga publish ini tidak sampai ke device.
- `tesmqtt`, `arduino-sample` — hanya untuk testing.

## 4. Alur kontrol lengkap (web: `kontrol_awgc3.php` + `Kontrol.php`)

```
Operator                    Server (PHP)                     Device (logger)
   |                            |                                |
   |-- POST lanjut_kontrol ---->|                                |
   |   (kode akses + daftar     |  validasi md5 kode_akses       |
   |    pintu + elevasi target) |  cek status_controller=1 dan   |
   |                            |  phase R/S/T=1 (temp_awgc)     |
   |                            |  set_tempkontrol: set_value,   |
   |                            |    status=1                    |
   |                            |  status_kontrol: 1+session_id  |
   |                            |-- MQTT kontrol_pintu-{id} ---->| (UI lain terkunci)
   |                            |-- MQTT AWGC_Garut_Copong ----->| multi_gcm → pintu bergerak
   |                            |-- MQTT AWGC_Garut_Copong ----->| ews_onoff ["0"] (10349 & 10350)
   |                            |  notif FCM ke aplikasi mobile  |
   |                            |                                |
   |                            |<====== data sensor tiap menit =| (datamasuk/add_awgc_json)
   |                            |  evaluasi kolom Status AWGC    |
   |                            |  (sensor10/16/22/28): ≠0 =     |
   |                            |  masih bergerak, 0 = selesai   |
   |<-- MQTT awgc-{id} ---------|                                |
   |<-- MQTT kontrol_pintu-{id}-|                                |
   |  UI poll status_kontrol;   |                                |
   |  saat kembali 0:           |                                |
   |-- POST selesai_kontrol --->|  insert log_kontrol            |
   |                            |  (metode Telemetry), notif FCM |
```

**STOP:** tombol STOP → `POST kontrol/stop_kontrol`. Dua hal terjadi:

1. `set_tempkontrol` di-set `status='2', set_value='0'` (lock UI + deteksi selesai).
2. Untuk tiap pintu, publish perintah stop GCM ke topik `sub_<id_logger>` (logger +
   module id dari mapping `gcm_helper`, bukan kolom `id_logger` DB):

   ```json
   {"GCM_GATE":{"cmd":"4","id":1}}
   ```

   `cmd "4"` = motor stop (register 15 = 4 → standby), aksi safety langsung tanpa
   menunggu warning. Lihat `GCM_command_reference.md`.

> Catatan: firmware **tidak lagi polling HTTP** `GET kontrol?idlogger=<id>`, jadi
> update DB saja tidak menghentikan pintu — perintah `GCM_GATE cmd "4"` di atas yang
> benar-benar menstop motor. Endpoint polling lama masih ada tetapi tidak dipakai
> firmware. Kode status `set_tempkontrol`: `0` = idle/selesai, `1` = perintah jalan,
> `2` = stop diminta.

## 5. Endpoint HTTP terkait

| Endpoint | Fungsi |
|---|---|
| `POST kontrol/lanjut_kontrol` | Mulai kontrol multi-pintu dari web (kode akses + `data[]`) |
| `POST kontrol/stop_kontrol` | Stop: set status=2 di `set_tempkontrol` + publish `GCM_GATE cmd "4"` ke `sub_<logger>` |
| `GET kontrol?idlogger=<id>` | Polling status/set point per pintu (legacy; firmware tidak lagi pakai) |
| `GET kontrol/status_kontrol?id_logger=<id>` | Status kontrol untuk UI |
| `POST kontrol/selesai_kontrol/<id>` | Catat log_kontrol setelah operasi selesai |
| `POST api/lanjut_kontrol`, `api/lanjut_kontrol2` | Varian kontrol satu pintu (mobile/API; `setting: gcm`, plus nyalakan EWS `["1"]` + `sleep(10)` sebelum kirim perintah) |
| `POST api2/lanjut_kontrol`, `api2/lanjut_kontrol2` | Varian mobile lama: hanya update DB + publish `kontrol_pintu-{id}` (tanpa perintah `AWGC_Garut_Copong`) |
| `POST awgc/submit_kalibrasi` | Kirim kalibrasi TMA (`setting: tma`) |

## 6. Tabel database yang terlibat

- `t_pintu` — definisi pintu: `mqtt_identifier`, sensor level/phase/status, batas atas-bawah, satuan.
- `set_tempkontrol` — set point & status perintah per pintu (`id_logger`, `id_pintu`, `set_value`, `status`, `sensor_kontrol`).
- `status_kontrol` — lock kontrol per logger (`status_kontrol`, `session_id`, `waktu`).
- `log_kontrol` — riwayat operasi (metode Manual/Telemetry, dari→ke, sistem).
- `kode_akses` — hash md5 kode akses operator untuk otorisasi kontrol.

## 7. Catatan & peringatan

1. **Kredensial MQTT dan kode perintah ada di banyak file** (`Kontrol.php`,
   `Api.php`, `Api2.php`, `Awgc.php`, `Datamasuk.php`) — kalau ganti password
   broker, semua harus diubah.
2. Siapa pun yang memegang kredensial broker bisa mem-publish perintah pintu —
   amankan kredensial ini; jangan dipakai untuk klien monitoring biasa.
3. `Api.php lanjut_kontrol()` menyalakan sirine EWS lalu `sleep(10)` sebelum
   mengirim perintah pintu khusus logger 10350; `lanjut_kontrol2()` melakukan
   hal yang sama untuk 10349 — perilaku tidak konsisten, perlu dicek mana yang
   benar.
4. Publish ke topik `Awgc_garut_copong` (huruf kecil) di `Api.php` tampak
   sebagai duplikat typo dan tidak akan diterima device.
