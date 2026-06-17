# Migrasi Kontrol Pintu AWGC ke Format GCM — Design / Spec

Tanggal: 2026-06-13
Status: disetujui (via tanya-jawab), siap implement.

## Latar belakang

Firmware logger AWGC Leuwigoong sudah memakai **format command GCM baru**
(lihat `GCM_command_reference.md`). Aplikasi web masih mengirim format lama
(`setting: multi_gcm/gcm/ews_onoff/tma`) ke topik bersama `AWGC_Garut_Copong`
dengan wrapper `set_<id_logger>`. Format lama tidak lagi diterima firmware →
perintah pintu tidak sampai. Tugas: sesuaikan pengiriman perintah ke format GCM
tanpa error.

## Keputusan (hasil klarifikasi dengan user)

1. **Transport baru (per-logger):**
   - Server → logger: topik `sub_{id_logger}` (mis. `sub_10349`).
   - Logger → server: topik `pub_{id_logger}` (tidak dipakai web saat ini).
   - Payload mentah (tanpa wrapper `set_<id>`).
2. **Binding modul (sumber kebenaran = GCM GET tiap logger):**
   - 10349: `id1=[5,1] id2=[6,1] id3=[7,1]` → 3 AWGC (slave 5/6/7).
   - 10350: `id1=[1,1] id2=[2,1] id3=[3,1] id4=[4,1]` → 4 AWGC (slave 1/2/3/4).
3. **Satuan target:** tetap kirim nilai level sekarang (cm Intake/Floodway, dm
   Scouring) apa adanya ke field `target`. Firmware yang konversi.
4. **STOP & deteksi selesai:** TIDAK berubah. Logger masih polling
   `GET /kontrol?idlogger=` dan menulis kolom `Status_AWGC` (sensor10/16/22/...).
   STOP tetap `set_tempkontrol.status=2`. Deteksi selesai tetap di
   `Datamasuk.php`.

   > **Update (firmware sudah tidak polling HTTP):** asumsi di atas tidak lagi
   > berlaku — `set_tempkontrol.status=2` saja tidak menghentikan pintu. STOP kini
   > juga publish `GCM_GATE cmd "4"` ke `sub_<id_logger>` (lihat
   > `Kontrol.php::stop_kontrol` + `publish_gcm_stop`, helper
   > `gcm_gate_cmd_payload`). Update DB tetap dipertahankan untuk lock UI + deteksi
   > selesai.
5. **EWS / horn:** hapus `ews_onoff` + `sleep(10)` manual dari web. Pre-warning
   horn jadi tanggung jawab firmware via `GCM_GATE_WARN`.
6. **TMA kalibrasi:** format baru belum diketahui → JANGAN migrasi dulu.
   `Awgc.php submit_kalibrasi` ditinggalkan apa adanya + diberi TODO.
7. **Scope:** semua pengirim perintah device — `Kontrol.php`, `Api.php`
   (`lanjut_kontrol`, `lanjut_kontrol2`), plus catatan TMA di `Awgc.php`.

## Mapping mqtt_identifier → (logger, GCM id)

| mqtt_identifier | Pintu       | id_logger (topik `sub_`) | GCM `id` |
|-----------------|-------------|--------------------------|----------|
| gcm1            | Floodway 1  | 10350                    | 1        |
| gcm2            | Floodway 2  | 10350                    | 2        |
| gcm3            | Floodway 3  | 10350                    | 3        |
| gcm4            | Scouring    | 10350                    | 4        |
| gcm5            | Intake 1    | 10349                    | 1        |
| gcm6            | Intake 2    | 10349                    | 2        |
| gcm7            | Intake 3    | 10349                    | 3        |

Map ini jadi sumber kebenaran untuk topik tujuan + module id, **independen dari
kolom `id_logger` di DB** yang inkonsisten (lihat Risiko).

## Bentuk payload baru

Per pintu, satu pesan ke `sub_<id_logger>`:

```json
{"GCM_GATE":{"cmd":"SET","id":1,"target":50}}
```

Multi-pintu = beberapa pesan `GCM_GATE` (module id berbeda) ke topik logger yang
sama.

## Perubahan per file

- **BARU `application/helpers/gcm_helper.php`** — `gcm_lookup($mqtt_identifier)`,
  `gcm_topic($id_logger)`, `gcm_gate_set_payload($gcm_id, $target)`.
- **`Kontrol.php::lanjut_kontrol`** — ganti payload `multi_gcm` → loop
  `GCM_GATE` ke `sub_<logger>`; hapus blok `ews_off`; pertahankan update DB
  (`set_tempkontrol`, `status_kontrol`), publish `kontrol_pintu-<id>` (lock UI),
  dan notif FCM.
- **`Api.php::lanjut_kontrol` & `lanjut_kontrol2`** — ganti payload `gcm` →
  `GCM_GATE` ke `sub_<logger>`; hapus `ews` + `sleep(10)` + publish ke topik
  typo `Awgc_garut_copong` + publish legacy `kontrol_pintu` (tanpa suffix).
  Fix bug: `$v->id_pintu`→`$row->id_pintu`, `$level_kontrol`→`$level`; guard
  `update_batch` saat array kosong.
- **`Awgc.php::submit_kalibrasi`** — tidak diubah perilakunya; tambah TODO bahwa
  TMA belum dimigrasi dan topik lama kemungkinan tidak lagi didengar firmware.

## Yang TIDAK diubah

`Datamasuk.php` (deteksi selesai), `stop_kontrol*` (STOP via DB),
`Api2.php`/`Kontrol2.php` (hanya update DB + lock UI, tidak kirim perintah
device), kredensial MQTT (tetap inline; refactor terpisah).

## Risiko / catatan terbuka

1. **Inkonsistensi DB:** backup `set_tempkontrol` menaruh Intake (214–216) di
   logger 10350, sedangkan binding GCM = 10349. Perintah pintu aman (pakai map),
   tapi alur STOP/selesai berbasis `id_logger` perlu diverifikasi terpisah.
2. **GCM_GATE_WARN** harus sudah dikonfigurasi di firmware agar horn pre-warning
   tetap berbunyi setelah EWS manual dihapus.
3. **TMA** kemungkinan tidak sampai ke firmware sampai formatnya ditentukan.
