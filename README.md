# Score Hub — Badminton Fun Match

Live scoreboard untuk turnamen badminton. Dibangun dengan Laravel + Livewire + Alpine.js.

## Fitur

- **Live Scoreboard** — update skor real-time dengan polling tiap 2 detik
- **Tap +1 / Long-press -1** — tambah atau kurangi skor dengan sentuhan
- **Fullscreen Mode** — orientasi landscape otomatis, layout card score dominan
- **Single / Double Elimination** — bracket otomatis dengan bye handling
- **Mobile-first** — dioptimalkan untuk HP, score besar dan jelas dari jauh
- **Dark Score Card** — kontras tinggi, mudah terbaca di lapangan

## Teknologi

- **Laravel 13** — backend
- **Livewire 4** — komponen interaktif real-time
- **Alpine.js** — interaksi frontend (fullscreen, long-press, cooldown)
- **Tailwind CSS** — styling utility-first
- **MySQL / Supabase** — database

## Persyaratan

- PHP 8.3+
- Composer
- Node.js 20+
- MySQL atau PostgreSQL (via Supabase)

## Instalasi

```bash
git clone git@github.com:ahmadzaenimubarok/score-hub.git
cd score-hub

composer install
npm install

cp .env.example .env
# sesuaikan database di .env

php artisan key:generate
php artisan migrate --seed
php artisan serve
```

## Struktur

```
app/
├── Livewire/
│   ├── Scoreboard.php        # Livewire komponen scoreboard
│   ├── TournamentIndex.php    # Daftar turnamen
│   ├── TournamentShow.php     # Detail turnamen + bracket
│   └── PublicBracket.php     # Bracket publik
├── Models/
│   ├── Tournament.php
│   ├── Participant.php
│   ├── Team.php
│   ├── TeamMember.php
│   └── GameMatch.php
resources/views/livewire/
│   ├── scoreboard.blade.php   # Scoreboard view (Alpine + Tailwind)
│   ├── tournament-index.blade.php
│   ├── tournament-show.blade.php
│   └── public-bracket.blade.php
```

## Penggunaan

1. Buat turnamen baru
2. Daftarkan tim (anggota otomatis terdaftar sebagai user)
3. Generate bracket
4. Klik "Mulai" pada pertandingan — scoreboard terbuka
5. Tap skor untuk +1, tahan untuk -1
6. Tombol fullscreen (⛶) untuk mode layar penuh landscape

## Lisensi

MIT
