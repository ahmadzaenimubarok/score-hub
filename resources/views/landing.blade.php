<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Score Hub — Skor badminton, tampil live</title>
    <meta name="description" content="Score Hub menampilkan skor badminton real-time di layar. Buka link, langsung main — tanpa aplikasi, tanpa akun. Turnamen dengan bracket otomatis.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .font-display { font-family: 'Archivo Black', 'Instrument Sans', ui-sans-serif, system-ui, sans-serif; }
        @keyframes rise { from { opacity: 0; transform: translateY(18px); } to { opacity: 1; transform: none; } }
        .rise { animation: rise .65s cubic-bezier(.22,.9,.32,1) both; }
        .rise-1 { animation-delay: .05s; }
        .rise-2 { animation-delay: .15s; }
        .rise-3 { animation-delay: .25s; }
        @keyframes livepulse { 0%, 100% { opacity: 1; } 50% { opacity: .35; } }
        .live-dot { animation: livepulse 1.5s infinite; }
    </style>
</head>
<body class="font-sans antialiased bg-[#0B0E0C] text-[#F2F6F3] min-h-dvh overflow-x-hidden">
<!--
THESIS: Landing ini membuktikan mekanisme produk — buka link, skor tampil live — dengan memamerkan scoreboard asli di viewport pertama; menolak hero SaaS default (gradient, icon cards, klaim abstrak).
OWN-WORLD: Panggung hitam arena malam; hijau court #4ADE80; garis lapangan putih tipis; angka tabular raksasa; badge LIVE amber. Body Instrument Sans, display Archivo Black.
STORY: Pengunjung paham dalam 3 detik bahwa skor bisa langsung tampil; lalu tahu turnamen juga ada; tindakan: buka scoreboard.
FIRST VIEWPORT: Wordmark kiri atas; H1 besar kiri dengan "live" hijau; dua CTA; mockup scoreboard nyata (panel hijau, kartu gelap, 21-17, LIVE) di kanan.
FORM: row-step ber-nomor, grid fitur text-led, bracket SVG, CTA penutup.
FINISH: unreviewed and undocumented is unfinished; this build ends with the finish review, the verdict, and DESIGN.md
-->

    {{-- NAV --}}
    <header class="max-w-6xl mx-auto px-5 sm:px-8 pt-6 flex items-center justify-between">
        <a href="/" class="font-display text-sm tracking-[0.18em] text-[#F2F6F3] no-underline">SCORE<span class="text-[#4ADE80]">HUB</span></a>
        <nav class="flex items-center gap-1 sm:gap-2">
            <a href="/s" class="px-3 py-2 rounded-lg text-sm text-[#9FB0A6] hover:text-[#F2F6F3] hover:bg-white/5 transition-colors no-underline">Scoreboard</a>
            <a href="/admin" class="px-3 py-2 rounded-lg text-sm text-[#9FB0A6] hover:text-[#F2F6F3] hover:bg-white/5 transition-colors no-underline">Turnamen</a>
        </nav>
    </header>

    {{-- HERO --}}
    <section class="relative max-w-6xl mx-auto px-5 sm:px-8 pt-14 sm:pt-20 pb-16 sm:pb-24">
        {{-- Garis lapangan dekoratif --}}
        <svg class="absolute -z-10 inset-0 w-full h-full opacity-[0.06]" viewBox="0 0 800 500" fill="none" stroke="white" stroke-width="1" aria-hidden="true">
            <rect x="40" y="40" width="720" height="420"/>
            <line x1="40" y1="250" x2="760" y2="250"/>
            <circle cx="400" cy="250" r="70"/>
            <line x1="400" y1="40" x2="400" y2="250"/>
        </svg>

        <div class="grid lg:grid-cols-2 gap-12 lg:gap-10 items-center">
            <div class="rise rise-1">
                <h1 class="font-display leading-[1.02] tracking-[-0.02em] text-[2.6rem] sm:text-6xl xl:text-7xl">
                    Skor lapangan,<br>
                    <span class="text-[#4ADE80]">tampil live.</span>
                </h1>
                <p class="mt-6 text-[#9FB0A6] text-base sm:text-lg leading-relaxed max-w-[46ch]">
                    Score Hub menampilkan skor badminton real-time di layar. Cukup buka link dari HP, langsung main — tanpa aplikasi, tanpa akun.
                </p>
                <div class="mt-8 flex flex-col sm:flex-row gap-3">
                    <a href="/s"
                       class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-[#4ADE80] hover:bg-[#5FE98F] active:scale-[0.98] text-[#0B0E0C] font-bold text-base rounded-xl transition-all no-underline">
                        Buka Scoreboard
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M5 12h14"/><path d="M12 5l7 7-7 7"/>
                        </svg>
                    </a>
                    <a href="/admin"
                       class="inline-flex items-center justify-center px-8 py-4 border border-white/15 hover:border-white/30 hover:bg-white/5 text-[#F2F6F3] font-semibold text-base rounded-xl transition-all no-underline">
                        Buat Turnamen
                    </a>
                </div>
            </div>

            {{-- Mockup scoreboard nyata --}}
            <div class="rise rise-2 relative">
                <div class="rounded-2xl bg-[#4ADE80] p-2.5 sm:p-3 shadow-[0_30px_90px_-25px_rgba(74,222,128,0.4)]">
                    <div class="bg-[#1e1e2e] rounded-xl px-4 py-4 sm:px-7 sm:py-6">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold tracking-wide text-amber-300 bg-amber-500/15 border border-amber-500/30 rounded-full px-2.5 py-1">GAME 2</span>
                            <span class="flex items-center gap-1.5 text-[10px] font-bold text-red-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500 live-dot"></span>LIVE
                            </span>
                        </div>
                        <div class="flex items-stretch mt-4">
                            <div class="flex-1 text-center">
                                <div class="font-display text-white text-6xl sm:text-7xl leading-none tabular-nums">21</div>
                                <div class="text-xs sm:text-sm font-bold text-[#22c55e] mt-2">TIM 1</div>
                            </div>
                            <div class="w-px bg-white/15 mx-3 sm:mx-5"></div>
                            <div class="flex-1 text-center">
                                <div class="font-display text-white text-6xl sm:text-7xl leading-none tabular-nums">17</div>
                                <div class="text-xs sm:text-sm font-bold text-[#22c55e] mt-2">TIM 2</div>
                            </div>
                        </div>
                        <div class="flex justify-center gap-1.5 mt-5">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                            <span class="w-2 h-2 rounded-full border border-white/40"></span>
                        </div>
                        <div class="text-center text-[9px] text-gray-500 mt-3 hidden sm:block">+1 ketuk · tahan untuk -1</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CARA PAKAI --}}
    <section class="max-w-6xl mx-auto px-5 sm:px-8 py-14 sm:py-20 border-t border-white/5">
        <h2 class="font-display text-2xl sm:text-4xl tracking-[-0.02em]">Tiga langkah, langsung main.</h2>
        <div class="mt-10">
            <div class="flex flex-col sm:flex-row sm:items-baseline gap-2 sm:gap-10 py-6 border-t border-white/8">
                <div class="font-display text-4xl text-white/15 shrink-0 w-16">1</div>
                <div class="flex-1">
                    <h3 class="text-lg sm:text-xl font-bold">Buka link</h3>
                    <p class="mt-1.5 text-[#9FB0A6] leading-relaxed">Dari HP siapa pun — <span class="text-[#F2F6F3]">score.jawakoentji.my.id/s</span>. Satu link untuk semua layar.</p>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-baseline gap-2 sm:gap-10 py-6 border-t border-white/8">
                <div class="font-display text-4xl text-white/15 shrink-0 w-16">2</div>
                <div class="flex-1">
                    <h3 class="text-lg sm:text-xl font-bold">Ketuk sisi tim</h3>
                    <p class="mt-1.5 text-[#9FB0A6] leading-relaxed">Ketuk untuk +1 poin, tahan untuk -1. Skor langsung tampil live di semua layar.</p>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-baseline gap-2 sm:gap-10 py-6 border-t border-white/8">
                <div class="font-display text-4xl text-white/15 shrink-0 w-16">3</div>
                <div class="flex-1">
                    <h3 class="text-lg sm:text-xl font-bold">Selesai, ulang</h3>
                    <p class="mt-1.5 text-[#9FB0A6] leading-relaxed">Best of 3 dengan pindah lapangan otomatis. Reset, main lagi.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- FITUR --}}
    <section class="max-w-6xl mx-auto px-5 sm:px-8 py-14 sm:py-20 border-t border-white/5">
        <h2 class="font-display text-2xl sm:text-4xl tracking-[-0.02em]">Kenapa Score Hub.</h2>
        <div class="mt-10 grid sm:grid-cols-2 gap-y-10 gap-x-14">
            <div class="border-t border-white/8 pt-6">
                <h3 class="text-lg font-bold">Satu link, semua lihat</h3>
                <p class="mt-2 text-[#9FB0A6] leading-relaxed">Setiap layar yang membuka link menampilkan skor yang sama, saat itu juga. Tidak perlu sinkronisasi apa pun.</p>
            </div>
            <div class="border-t border-white/8 pt-6">
                <h3 class="text-lg font-bold">Angka kebaca dari jauh</h3>
                <p class="mt-2 text-[#9FB0A6] leading-relaxed">Fullscreen landscape dengan angka raksasa dan kontras tinggi — terlihat dari seberang lapangan.</p>
            </div>
            <div class="border-t border-white/8 pt-6">
                <h3 class="text-lg font-bold">Bracket tersusun sendiri</h3>
                <p class="mt-2 text-[#9FB0A6] leading-relaxed">Turnamen single elimination: peserta daftar lewat link, bracket dan skor berjalan otomatis.</p>
            </div>
            <div class="border-t border-white/8 pt-6">
                <h3 class="text-lg font-bold">Tanpa akun, tanpa install</h3>
                <p class="mt-2 text-[#9FB0A6] leading-relaxed">Tidak ada signup, tidak ada aplikasi. Buka, main, selesai.</p>
            </div>
        </div>
    </section>

    {{-- TURNAMEN (bracket demo) --}}
    <section class="max-w-6xl mx-auto px-5 sm:px-8 py-14 sm:py-20 border-t border-white/5">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="font-display text-2xl sm:text-4xl tracking-[-0.02em]">Turnamen tanpa kertas.</h2>
                <p class="mt-5 text-[#9FB0A6] leading-relaxed max-w-[46ch]">Peserta daftar lewat link. Bracket tersusun otomatis. Skor mengalir dari lapangan ke papan — hasil pertandingan langsung mengisi bracket.</p>
                <a href="/admin" class="mt-7 inline-flex items-center gap-2 px-7 py-3.5 border border-white/15 hover:border-white/30 hover:bg-white/5 text-[#F2F6F3] font-semibold text-sm rounded-xl transition-all no-underline">
                    Buat Turnamen
                </a>
            </div>

            {{-- Bracket demo (konten ilustratif) --}}
            <div class="relative h-56 bg-[#101412] border border-white/8 rounded-2xl px-6 py-5">
                <svg class="absolute inset-0 w-full h-full" viewBox="0 0 440 224" preserveAspectRatio="none" fill="none" aria-hidden="true">
                    <path d="M120 22 H135 M120 78 H135 M135 22 V78 M135 50 H160" stroke="rgba(255,255,255,0.18)"/>
                    <path d="M120 134 H135 M120 190 H135 M135 134 V190 M135 162 H160" stroke="rgba(255,255,255,0.18)"/>
                    <path d="M280 50 H295 M280 162 H295 M295 50 V162 M295 106 H310" stroke="rgba(255,255,255,0.18)"/>
                </svg>
                <div class="absolute left-6 top-5 w-[120px] h-11 bg-[#151A17] border border-white/10 rounded-lg px-3 flex items-center text-xs text-[#F2F6F3]">Tim Alpha</div>
                <div class="absolute left-6 top-[61px] w-[120px] h-11 bg-[#151A17] border border-white/10 rounded-lg px-3 flex items-center text-xs text-[#F2F6F3]">Tim Beta</div>
                <div class="absolute left-6 top-[117px] w-[120px] h-11 bg-[#151A17] border border-white/10 rounded-lg px-3 flex items-center text-xs text-[#F2F6F3]">Tim Gamma</div>
                <div class="absolute left-6 top-[173px] w-[120px] h-11 bg-[#151A17] border border-white/10 rounded-lg px-3 flex items-center text-xs text-[#F2F6F3]">Tim Delta</div>
                <div class="absolute left-[160px] top-7 w-[120px] h-11 bg-[#1A211D] border border-white/15 rounded-lg px-3 flex items-center text-xs text-[#F2F6F3]">Pemenang Semi A</div>
                <div class="absolute left-[160px] top-[139px] w-[120px] h-11 bg-[#1A211D] border border-white/15 rounded-lg px-3 flex items-center text-xs text-[#F2F6F3]">Pemenang Semi B</div>
                <div class="absolute left-[310px] top-[89px] w-[130px] h-11 bg-[#4ADE80]/10 border border-[#4ADE80]/50 rounded-lg px-3 flex items-center text-xs font-bold text-[#4ADE80]">Champion</div>
                <span class="absolute bottom-2.5 right-4 text-[9px] tracking-wider text-white/25 uppercase">contoh bracket</span>
            </div>
        </div>
    </section>

    {{-- CLOSE CTA --}}
    <section class="max-w-6xl mx-auto px-5 sm:px-8 py-16 sm:py-24 border-t border-white/5 text-center">
        <h2 class="font-display text-3xl sm:text-5xl tracking-[-0.02em]">Langsung coba.</h2>
        <a href="/s" class="mt-8 inline-flex items-center justify-center gap-2 px-10 py-4 bg-[#4ADE80] hover:bg-[#5FE98F] active:scale-[0.98] text-[#0B0E0C] font-bold text-base rounded-xl transition-all no-underline">
            Buka Scoreboard
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M5 12h14"/><path d="M12 5l7 7-7 7"/>
            </svg>
        </a>
    </section>

    <footer class="max-w-6xl mx-auto px-5 sm:px-8 pb-10 pt-6 border-t border-white/5 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-[#6B7C72]">
        <span>Score Hub · Badminton Fun Match</span>
        <span>dibuat oleh Ahmad Zaeni Mubarok</span>
    </footer>

</body>
</html>
