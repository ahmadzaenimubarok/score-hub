# Score Hub — Product Truth (PRODUCT.md)

> Sumber kebenaran produk. Klaim di sini harus benar-benar ada di aplikasi.

## Nama & satu kalimat
**Score Hub** (Badminton Fun Match) — web app live scoring badminton: buka link, skor tampil real-time di layar. Tanpa aplikasi, tanpa akun.

## Audience & skenario nyata
- Komunitas badminton / penyelenggara turnamen kecil
- Orang yang main rutin di lapangan, ingin skor dilihat semua orang
- Skenario: lapangan indoor, layar/HP di samping lapangan, cahaya terang, dibaca dari jarak jauh

## Mekanisme unik
- **Public scoreboard (`/s`)**: buka link → langsung scoreboard (Tim 1 vs Tim 2, best of 3). Semua yang membuka link melihat skor yang sama, live. Tanpa kode, tanpa form, tanpa login.
- **Turnamen**: single elimination bracket, registrasi via link (`/r/{kode}`), skor mengalir otomatis ke bracket, admin panel di `/admin`.

## Klaim yang boleh dipakai
- Buka link, langsung main (tanpa install/akun) ✓
- Skor tampil live di semua layar yang membuka link ✓
- Format badminton standar: rally point 21, menang selisih 2, cap 30 ✓
- Best of 1 / best of 3, pindah lapangan otomatis ✓
- Turnamen single elimination dengan bracket otomatis ✓
- Layar fullscreen landscape, angka besar ✓

## Klaim yang TIDAK boleh diinvent
- Jumlah user, jumlah turnamen, angka statistik apa pun
- Testimoni, harga, endpoint/klien
- Fitur yang belum ada

## Surface
| Surface | Route | Mode |
|---|---|---|
| Landing page | `/` | Persuade |
| Public scoreboard | `/s` | Operate |
| Bracket publik | `/t/{kode}` | Operate |
| Registrasi | `/r/{kode}` | Operate |
| Admin | `/admin` | Operate |
