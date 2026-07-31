<?php

namespace App\Livewire;

use App\Models\Tournament;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.public')]
class PublicBracket extends Component
{
    public Tournament $tournament;
    public string $code;

    public function mount(string $code)
    {
        $this->code = $code;
        $this->tournament = Tournament::where('code', $code)
            ->with([
                'gameMatches' => fn ($q) => $q->orderBy('round')->orderBy('match_number'),
                'gameMatches.team1.members',
                'gameMatches.team2.members',
                'gameMatches.winner',
            ])
            ->firstOrFail();
    }

    public function render()
    {
        // Reload from DB so polling picks up score changes
        $this->tournament = $this->tournament->fresh();
        $this->tournament->load([
            'gameMatches' => fn ($q) => $q->orderBy('round')->orderBy('match_number'),
            'gameMatches.team1.members',
            'gameMatches.team2.members',
            'gameMatches.winner',
        ]);

        $bracketRounds = $this->tournament->gameMatches
            ->groupBy('round')
            ->sortKeys();

        $champion = null;
        if ($this->tournament->status === 'completed') {
            $finalMatch = $this->tournament->gameMatches()
                ->whereNull('next_match_id')
                ->where('status', 'completed')
                ->first();
            $champion = $finalMatch?->winner;
        }

        return view('livewire.public-bracket', [
            'bracketRounds' => $bracketRounds,
            'bracketLayout' => $this->bracketLayout($this->tournament->gameMatches),
            'champion' => $champion,
        ]);
    }

    /**
     * Hitung layout bracket dengan connector.
     *
     * Setiap match diposisikan absolut berdasarkan pusat feeder-nya:
     * - Round 1: compact vertikal (i * unit + H/2)
     * - Round berikutnya: pusat = rata-rata pusat 2 feeder (match 2i & 2i+1 di round
     *   sebelumnya). Jika salah satu feeder void (null vs null), pusat = feeder yang ada.
     *
     * Void match (tanpa tim sama sekali) disembunyikan — tidak dirender, tidak digambar.
     * Garis connector digambar per pasangan round: horizontal dari feeder ke titik
     * tengah gap, vertikal menghubungkan pasangan, horizontal ke match berikutnya.
     *
     * @param \Illuminate\Support\Collection $matches gameMatches ordered by round, match_number
     * @return array
     */
    private function bracketLayout($matches): array
    {
        $cardH = 132;
        $cardW = 224;   // = w-56
        $vGap = 16;
        $hGap = 64;
        $headerH = 40;

        $byRound = [];
        foreach ($matches as $m) {
            $byRound[$m->round][] = $m;
        }
        ksort($byRound);
        $roundNums = array_keys($byRound);

        $empty = ['rounds' => [], 'roundLeft' => [], 'tops' => [], 'lines' => [], 'width' => 0, 'height' => 0, 'cardH' => $cardH, 'headerH' => $headerH];
        if (empty($roundNums)) {
            return $empty;
        }

        $firstRound = $roundNums[0];
        $roundLeft = [];
        $centers = [];
        $tops = [];
        $maxCenter = 0;

        // Void match = slot yang tidak akan pernah terisi:
        // - Round 1: kedua slot kosong
        // - Round > 1: kedua feeder-nya void (recursive)
        // Match pending di ronde berikutnya (belum ada tim, tapi akan terisi) TETAP ditampilkan.
        $void = [];
        foreach ($roundNums as $idx => $r) {
            $void[$r] = [];
            $prev = $idx > 0 ? $roundNums[$idx - 1] : null;
            foreach ($byRound[$r] as $i => $m) {
                if ($prev === null) {
                    $void[$r][$i] = is_null($m->team1_id) && is_null($m->team2_id);
                } else {
                    $v1 = $void[$prev][2 * $i] ?? true;
                    $v2 = $void[$prev][2 * $i + 1] ?? true;
                    $void[$r][$i] = $v1 && $v2;
                }
            }
        }

        foreach ($roundNums as $idx => $r) {
            $roundLeft[$r] = ($r - $firstRound) * ($cardW + $hGap);
            $centers[$r] = [];
            $prev = $idx > 0 ? $roundNums[$idx - 1] : null;

            $compact = 0;
            foreach ($byRound[$r] as $i => $m) {
                if ($void[$r][$i]) {
                    continue; // void match — hidden
                }

                if ($prev === null) {
                    $c = $compact * ($cardH + $vGap) + $cardH / 2;
                } else {
                    $f1 = $centers[$prev][2 * $i] ?? null;
                    $f2 = $centers[$prev][2 * $i + 1] ?? null;
                    if ($f1 !== null && $f2 !== null) {
                        $c = ($f1 + $f2) / 2;
                    } elseif ($f1 !== null) {
                        $c = $f1;
                    } elseif ($f2 !== null) {
                        $c = $f2;
                    } else {
                        $c = $compact * ($cardH + $vGap) + $cardH / 2;
                    }
                }

                $centers[$r][$i] = $c;
                $tops[$m->id] = $headerH + $c - $cardH / 2;
                $maxCenter = max($maxCenter, $c);
                $compact++;
            }
        }

        // Connector lines (y dihitung relatif ke area card, ditambah headerH di akhir)
        $lines = [];
        $gapCenter = $hGap / 2;
        foreach ($roundNums as $idx => $r) {
            if ($idx === count($roundNums) - 1) {
                break; // round terakhir tidak punya next
            }
            $next = $roundNums[$idx + 1];

            foreach ($byRound[$next] as $j => $m) {
                $yc = $centers[$next][$j] ?? null;
                if ($yc === null) {
                    continue;
                }
                $f1 = $centers[$r][2 * $j] ?? null;
                $f2 = $centers[$r][2 * $j + 1] ?? null;

                if ($f1 !== null && $f2 !== null) {
                    // bracket penuh: dua feeder bertemu di tengah
                    $lines[] = [0, $f1, $gapCenter, $f1];
                    $lines[] = [0, $f2, $gapCenter, $f2];
                    $lines[] = [$gapCenter, min($f1, $f2), $gapCenter, max($f1, $f2)];
                    $lines[] = [$gapCenter, $yc, $hGap, $yc];
                } elseif ($f1 !== null) {
                    // bye: garis lurus dari feeder ke match berikutnya
                    $lines[] = [0, $f1, $hGap, $f1];
                } elseif ($f2 !== null) {
                    $lines[] = [0, $f2, $hGap, $f2];
                }
            }
        }

        foreach ($lines as &$ln) {
            $ln[1] += $headerH;
            $ln[3] += $headerH;
        }
        unset($ln);

        $width = (count($roundNums) - 1) * ($cardW + $hGap) + $cardW;
        $height = $headerH + $maxCenter + $cardH / 2;

        // Hanya round yang punya match non-void
        $visibleRounds = [];
        foreach ($roundNums as $r) {
            $vis = [];
            foreach ($byRound[$r] as $i => $m) {
                if (!$void[$r][$i]) {
                    $vis[] = $m;
                }
            }
            if (count($vis) > 0) {
                $visibleRounds[$r] = $vis;
            }
        }

        return [
            'rounds' => $visibleRounds,
            'roundLeft' => $roundLeft,
            'tops' => $tops,
            'lines' => $lines,
            'width' => (int) round($width),
            'height' => (int) round($height),
            'cardH' => $cardH,
            'headerH' => $headerH,
        ];
    }
}
