# Score Hub — Design System (DESIGN.md)

> Ditulis dari dunia yang sudah dibangun (Juli 2026). Ground truth = kode, bukan niat.

## World
**"Arena broadcast"** — panggung hitam arena malam, hijau court, garis lapangan putih tipis, angka tabular raksasa. Semua surface berbagi bahasa ini: landing, scoreboard, bracket.

## Palette
| Role | Value | Catatan |
|---|---|---|
| Panggung (bg utama) | `#0B0E0C` | hitam bertinta hijau — "arena malam" |
| Panel | `#101412` / `#151A17` | card/tab |
| Kartu scoreboard | `#1e1e2e` | warisan dari scoreboard |
| Court (accent utama) | `#4ADE80` | hijau lapangan; tombol primer pakai ini dengan teks `#0B0E0C` |
| Court hover | `#5FE98F` | |
| LIVE / status | `#FBBF24` (amber), `#F87171` (red-400 dot) | badge & indikator live |
| Teks utama | `#F2F6F3` | |
| Teks sekunder | `#9FB0A6` | ditint dari hijau, bukan abu-abu murni |
| Garis | `white/5-15` hairline | elevation pakai border, bukan shadow |

## Type
- **Body**: Instrument Sans (self-host, `--font-sans`)
- **Display**: Archivo Black (Google Fonts) — font display hanya untuk headline & angka besar
- Tracking display: `-0.02em` (floor -0.04em)
- Numerals: `tabular-nums` untuk skor

## Motion
- Satu momen: **LIVE dot pulse** (1.5s infinite) — bahasa hidupnya app
- Entrance hero: fade-up sekali pakai (`rise`, ease-out, delay bertingkat) — sekali, bukan per-section

## Komponen
- **CTA primer**: `bg-[#4ADE80] text-[#0B0E0C] font-bold rounded-xl py-4` + icon panah SVG, `active:scale-[0.98]`
- **CTA sekunder**: border `white/15` hover `white/30`, rounded-xl
- **Chip status**: rounded-full, text 10px, border tinted (GAME 2 amber, MATCH SELESAI emerald)
- **Bracket**: node `bg-[#151A17] border-white/10 rounded-lg h-11`, connector line `rgba(255,255,255,0.18)`, champion `bg-[#4ADE80]/10 border-[#4ADE80]/50 text-[#4ADE80]`
- **Garis dekoratif**: SVG geometri lapangan (rect + net + circle) opacity 0.06 — hanya geometri, bukan ilustrasi
- Radius: 12–16px (rounded-xl/2xl). Pill hanya chip kecil.

## Elevation
Border hairline `white/5-15` sebagai deklarasi elevasi utama. Shadow hanya untuk mockup scoreboard (`0 30px 90px -25px rgba(74,222,128,0.4)`). Tidak ada hard offset shadow.

## Surface brief
| Surface | Mode | Catatan |
|---|---|---|
| Landing `/` | Persuade | Hero = mockup scoreboard nyata (prove, don't claim) |
| Scoreboard `/s` | Operate | Panel hijau + kartu `#1e1e2e`, angka min(35vmin, 16rem) |
| Admin `/admin` | Operate | — |

## Kontrak yang dijaga
- Tidak ada kicker/eyebrow di atas heading
- Tidak ada gradient text, glass dekoratif, icon tile rounded-square
- Tidak ada kartu icon+heading+text sebagai struktur
- Emoji tidak pernah jadi ikon — pakai SVG stroke konsisten
- Dark dipilih dari scene (arena indoor malam), bukan default kategori
