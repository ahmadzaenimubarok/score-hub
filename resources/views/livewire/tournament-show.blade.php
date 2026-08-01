<div>
    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4 mb-6">
        <div class="min-w-0">
            <a href="{{ route('tournaments.index') }}" class="text-sm text-gray-500 hover:text-gray-300 mb-1 inline-block">&larr; Kembali</a>
            <h1 class="text-3xl font-bold break-words">{{ $tournament->name }}</h1>
            <div class="flex flex-wrap items-center gap-3 mt-1">
                <span class="text-xs px-2.5 py-1 rounded-full font-medium
                    @if($tournament->status === 'archived') bg-gray-700/50 text-gray-500 border border-gray-600
                    @elseif($tournament->status === 'draft') bg-gray-700 text-gray-400
                    @elseif($tournament->status === 'ongoing') bg-amber-900/50 text-amber-300 border border-amber-700
                    @else bg-emerald-900/50 text-emerald-300 border border-emerald-700
                    @endif
                ">
                    @if($tournament->status === 'archived') Diarsipkan
                    @else {{ $tournament->status }}
                    @endif
                </span>
                <span class="text-sm text-gray-500">Kode publik: <code class="text-emerald-400 bg-gray-800 px-2 py-0.5 rounded">{{ $tournament->code }}</code></span>
                @if($tournament->status === 'draft')
                    <span class="text-sm text-gray-500 ml-2">
                        Format:
                        <select wire:change="setGamesFormat($event.target.value)" class="bg-gray-800 text-gray-200 border border-gray-700 rounded px-2 py-0.5 text-xs">
                            <option value="2" {{ $tournament->games_to_win === 2 ? 'selected' : '' }}>Best of 3</option>
                            <option value="1" {{ $tournament->games_to_win === 1 ? 'selected' : '' }}>1 Game</option>
                        </select>
                    </span>
                @endif
            </div>
        </div>
        <div class="flex flex-wrap gap-2"
             x-data="{ copied: false }">
            <button x-data="{ shared: false }" @click="
                const url = '{{ route('registration.show', $tournament->code) }}';
                if (navigator.share) {
                    navigator.share({
                        title: '{{ $tournament->name }}',
                        text: 'Daftar turnamen {{ $tournament->name }} 🏸',
                        url: url
                    }).catch(() => {});
                } else {
                    navigator.clipboard.writeText(url);
                    shared = true;
                    setTimeout(() => shared = false, 2000);
                }
            "
                    class="flex items-center gap-2 px-4 h-11 bg-emerald-900/40 hover:bg-emerald-800/60 text-emerald-300 border border-emerald-800 rounded-lg transition-colors"
                    :title="shared ? 'Tersalin!' : 'Bagikan halaman pendaftaran'">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                    <circle cx="8.5" cy="7" r="4"/>
                    <line x1="20" y1="8" x2="20" y2="14"/>
                    <line x1="23" y1="11" x2="17" y2="11"/>
                </svg>
                <span class="text-xs font-medium" x-text="shared ? 'Tersalin!' : 'Bagikan'"></span>
            </button>
            <button @click="
                navigator.clipboard.writeText('{{ route('public.bracket', $tournament->code) }}');
                copied = true;
                setTimeout(() => copied = false, 2000);
            "
                    class="relative w-11 h-11 flex items-center justify-center bg-gray-800 hover:bg-gray-700 text-gray-400 hover:text-white rounded-lg transition-colors"
                    :title="copied ? 'Tersalin!' : 'Salin tautan publik'">
                <svg x-show="!copied" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="18" cy="5" r="3"/>
                    <circle cx="6" cy="12" r="3"/>
                    <circle cx="18" cy="19" r="3"/>
                    <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/>
                    <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/>
                </svg>
                <svg x-show="copied" x-cloak class="w-5 h-5 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
            </button>
            @if($tournament->status === 'draft' && $tournament->teams->count() >= 2 && $tournament->gameMatches->count() > 0)
                <button wire:click="startTournament" class="px-4 h-11 bg-emerald-600 hover:bg-emerald-500 text-white font-medium rounded-lg transition-colors text-sm">
                    Mulai Turnamen
                </button>
            @endif
            @if($tournament->status !== 'draft' && $tournament->status !== 'archived')
                <button wire:click="resetTournament" wire:confirm="Reset turnamen? Semua data pertandingan akan hilang." class="px-4 h-11 bg-red-900/50 hover:bg-red-800 text-red-300 border border-red-800 rounded-lg transition-colors text-sm">
                    Reset
                </button>
            @endif
            @if($tournament->status === 'archived')
                <button wire:click="unarchiveTournament" wire:confirm="Kembalikan turnamen?" class="px-4 h-11 bg-emerald-600 hover:bg-emerald-500 text-white font-medium rounded-lg transition-colors text-sm">
                    Kembalikan
                </button>
            @else
                <button wire:click="archiveTournament" wire:confirm="Arsipkan turnamen?" class="px-4 h-11 bg-gray-700 hover:bg-gray-600 text-gray-300 border border-gray-600 rounded-lg transition-colors text-sm">
                    Arsipkan
                </button>
            @endif
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session('message'))
        <div class="mb-6 px-4 py-3 bg-emerald-900/50 border border-emerald-700 rounded-lg text-emerald-200 text-sm">
            {{ session('message') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-6 px-4 py-3 bg-red-900/50 border border-red-700 rounded-lg text-red-200 text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Tabs --}}
    <div class="flex gap-1 mb-6 border-b border-gray-700">
        <button wire:click="$set('tab', 'participants')" class="px-4 py-2.5 text-sm font-medium border-b-2 transition-colors
            {{ $tab === 'participants' ? 'border-emerald-500 text-emerald-400' : 'border-transparent text-gray-500 hover:text-gray-300' }}">
            Peserta
        </button>
        <button wire:click="$set('tab', 'teams')" class="px-4 py-2.5 text-sm font-medium border-b-2 transition-colors
            {{ $tab === 'teams' ? 'border-emerald-500 text-emerald-400' : 'border-transparent text-gray-500 hover:text-gray-300' }}">
            Tim
        </button>
        <button wire:click="$set('tab', 'bracket')" class="px-4 py-2.5 text-sm font-medium border-b-2 transition-colors
            {{ $tab === 'bracket' ? 'border-emerald-500 text-emerald-400' : 'border-transparent text-gray-500 hover:text-gray-300' }}">
            Bracket
        </button>
        <a href="{{ route('public.bracket', $tournament->code) }}" target="_blank"
           class="flex-none w-11 h-11 flex items-center justify-center text-gray-500 hover:text-gray-300 hover:bg-gray-800 rounded-lg transition-colors ml-auto"
           title="Tampilan Publik">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/>
                <polyline points="15 3 21 3 21 9"/>
                <line x1="10" y1="14" x2="21" y2="3"/>
            </svg>
        </a>
    </div>

    {{-- Tab: Participants --}}
    @if($tab === 'participants')
        <div>
            {{-- Estimasi waktu pertandingan --}}
            <div class="mb-6 rounded-xl border border-gray-700 bg-gray-800/40 p-4 sm:p-5">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold tracking-wider text-gray-500 uppercase">Estimasi total permainan</p>
                        <p class="mt-1 text-2xl sm:text-3xl font-bold text-emerald-400">{{ $estimate['totalLabel'] }}</p>
                        <p class="mt-1 text-sm text-gray-400">
                            {{ $estimate['teams'] }} tim · {{ $estimate['matches'] }} pertandingan · format {{ $estimate['formatLabel'] }}
                        </p>
                    </div>
                    <div class="flex flex-col gap-1.5 items-start sm:items-end">
                        <label class="text-xs text-gray-500">Jumlah lapangan</label>
                        <select wire:model.live="estimateCourts" class="bg-gray-800 text-gray-200 border border-gray-700 rounded-lg px-3 py-2 text-sm">
                            @foreach([1, 2, 3, 4] as $n)
                                <option value="{{ $n }}">{{ $n }} lapangan</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <p class="mt-3 text-xs text-gray-600">
                    ≈{{ $estimate['perMatch'] }} menit/pertandingan (termasuk ±{{ $estimate['break'] }} menit jeda) · bye langsung maju tanpa main
                </p>
                @if($estimate['teams'] < 2)
                    <p class="mt-2 text-xs text-amber-500/90">Tambahkan minimal 2 peserta untuk melihat estimasi.</p>
                @endif
            </div>

            @if($tournament->status === 'draft')
                <form wire:submit="addParticipant" class="flex gap-3 mb-6">
                    <input
                        wire:model="participantName"
                        type="text"
                        placeholder="Nama peserta..."
                        class="flex-1 h-11 px-4 bg-gray-800 border border-gray-700 rounded-lg text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    >
                    <button type="submit" class="px-5 h-11 bg-emerald-600 hover:bg-emerald-500 text-white font-medium rounded-lg transition-colors text-sm">
                        + Tambah
                    </button>
                </form>
            @endif

            <div class="space-y-2">
                @forelse ($tournament->participants as $p)
                    <div class="flex items-center justify-between px-4 py-3 bg-gray-800/50 rounded-lg">
                        <span>{{ $p->name }}</span>
                        @if($tournament->status === 'draft')
                            <button wire:click="removeParticipant({{ $p->id }})" wire:confirm="Hapus {{ $p->name }}?" class="text-red-500 hover:text-red-400 text-sm">
                                Hapus
                            </button>
                        @endif
                    </div>
                @empty
                    <p class="text-center py-8 text-gray-500">Belum ada peserta. Tambahkan minimal 2 peserta.</p>
                @endforelse
            </div>

            <div class="mt-4 text-sm text-gray-500">
                Total: {{ $tournament->participants->count() }} peserta
            </div>
        </div>
    @endif

    {{-- Tab: Teams --}}
    @if($tab === 'teams')
        <div>
            @if($tournament->participants->count() >= 2 && $tournament->status === 'draft')
                <button wire:click="generateTeams" class="mb-6 h-11 px-5 bg-emerald-600 hover:bg-emerald-500 text-white font-medium rounded-lg transition-colors text-sm">
                    🔄 Generate Tim (acak)
                </button>
            @elseif($tournament->participants->count() < 2)
                <p class="mb-6 text-sm text-gray-500">Minimal 2 peserta untuk generate tim.</p>
            @endif

            @if(($tournament->teams->count() > 0 || $tournament->gameMatches->count() > 0) && $tournament->status === 'draft')
                <button wire:click="generateBracket" class="mb-6 ml-3 h-11 px-5 bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-300 border border-emerald-700 font-medium rounded-lg transition-colors text-sm">
                    🏆 Generate Bracket
                </button>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @forelse ($tournament->teams as $team)
                    <div class="px-4 py-3 bg-gray-800/50 rounded-lg border border-gray-700">
                        <h4 class="font-semibold text-emerald-400">{{ $team->name }}</h4>
                        <p class="text-sm text-gray-400 mt-1">
                            {{ $team->members->pluck('name')->join(' & ') }}
                        </p>
                    </div>
                @empty
                    <p class="text-center py-8 text-gray-500 col-span-full">Belum ada tim. Generate tim dari peserta yang sudah ditambahkan.</p>
                @endforelse
            </div>
        </div>
    @endif

    {{-- Tab: Bracket --}}
    @if($tab === 'bracket')
        <div>
            @if($tournament->gameMatches->count() === 0)
                <div class="text-center py-12 text-gray-500">
                    <p class="text-4xl mb-3">🏸</p>
                    <p>Generate tim & bracket terlebih dahulu.</p>
                </div>
            @else
                {{-- Bracket Display (connector layout) --}}
                <div class="overflow-x-auto pb-6">
                    <div class="relative mx-auto"
                         style="width: {{ $bracketLayout['width'] }}px; height: {{ $bracketLayout['height'] }}px;">

                        {{-- Round headers --}}
                        @foreach ($bracketLayout['rounds'] as $round => $matches)
                            <div class="absolute top-0 flex items-center justify-center"
                                 style="left: {{ $bracketLayout['roundLeft'][$round] }}px; width: 224px; height: {{ $bracketLayout['headerH'] }}px;">
                                <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider text-center">
                                    Ronde {{ $round }}
                                    @if($loop->last)<span class="text-emerald-400 ml-2">(Final)</span>@endif
                                </h3>
                            </div>
                        @endforeach

                        {{-- Match cards --}}
                        @foreach ($bracketLayout['rounds'] as $round => $matches)
                            <div class="absolute top-0" style="left: {{ $bracketLayout['roundLeft'][$round] }}px;">
                                @foreach ($matches as $match)
                                    <div class="absolute w-56 flex flex-col justify-center p-3 bg-gray-800 border rounded-lg
                                                {{ $match->status === 'ongoing' ? 'border-amber-600' : ($match->status === 'completed' ? 'border-emerald-800' : 'border-gray-700') }}"
                                         style="top: {{ $bracketLayout['tops'][$match->id] }}px; height: {{ $bracketLayout['cardH'] }}px;">
                                        {{-- Team 1 --}}
                                        <div class="flex items-center justify-between {{ $match->isTeam1Winner() ? 'text-emerald-400 font-semibold' : ($match->team1 ? 'text-gray-200' : 'text-gray-600') }}">
                                            <span class="text-sm truncate min-w-0 flex-1">
                                                @if($match->team1)
                                                    {{ $match->team1->name }}
                                                    @if($match->team1->members->isNotEmpty())
                                                        <span class="text-xs text-gray-500 ml-1">({{ $match->team1->membersList() }})</span>
                                                    @endif
                                                @elseif($match->isBye())
                                                    <span class="text-yellow-600">BYE</span>
                                                @else
                                                    —
                                                @endif
                                            </span>
                                            <span class="text-sm font-mono ml-2 flex-none">{{ $match->status !== 'pending' ? $match->score1 : '' }}</span>
                                        </div>
                                        {{-- VS --}}
                                        <div class="text-xs text-gray-600 my-1 text-center">VS</div>
                                        {{-- Team 2 --}}
                                        <div class="flex items-center justify-between {{ $match->isTeam2Winner() ? 'text-emerald-400 font-semibold' : ($match->team2 ? 'text-gray-200' : 'text-gray-600') }}">
                                            <span class="text-sm truncate min-w-0 flex-1">
                                                @if($match->team2)
                                                    {{ $match->team2->name }}
                                                    @if($match->team2->members->isNotEmpty())
                                                        <span class="text-xs text-gray-500 ml-1">({{ $match->team2->membersList() }})</span>
                                                    @endif
                                                @elseif($match->isBye())
                                                    <span class="text-yellow-600">BYE</span>
                                                @else
                                                    —
                                                @endif
                                            </span>
                                            <span class="text-sm font-mono ml-2 flex-none">{{ $match->status !== 'pending' ? $match->score2 : '' }}</span>
                                        </div>

                                        {{-- Actions --}}
                                        @if($match->status === 'pending' && $match->team1_id && $match->team2_id && $tournament->status === 'ongoing')
                                            <button wire:click="startMatch({{ $match->id }})" class="mt-2 w-full h-10 text-sm font-medium bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg transition-colors">
                                                Mulai
                                            </button>
                                        @endif

                                        @if($match->status === 'ongoing')
                                            <a href="{{ route('scoreboard.show', $match->id) }}"
                                               class="block mt-2 w-full h-10 flex items-center justify-center gap-1.5 text-sm font-medium bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-300 border border-emerald-700/60 rounded-lg transition-colors">
                                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                                                Scoreboard
                                            </a>
                                        @endif

                                        @if($match->status === 'completed' && $match->winner)
                                            <div class="mt-2 text-xs {{ $match->isBye() ? 'text-yellow-500' : 'text-emerald-500' }} text-center">
                                                @if($match->isBye())
                                                    ↪ {{ $match->winner->name }} (BYE)
                                                @else
                                                    ✓ {{ $match->winner->name }} menang
                                                @endif
                                            </div>
                                        @endif

                                        {{-- Edit Score Modal inline --}}
                                        @if($updatingMatchId === $match->id)
                                            <div class="mt-3 p-3 bg-gray-900 rounded-lg border border-gray-600">
                                                <div class="flex gap-2 items-center mb-2">
                                                    <div class="flex-1">
                                                        <label class="text-xs text-gray-400">{{ $match->team1->name ?? 'Team 1' }}</label>
                                                        <input wire:model="score1" type="number" min="0" class="w-full px-2 py-1 bg-gray-800 border border-gray-700 rounded text-sm text-center">
                                                    </div>
                                                    <span class="text-gray-500 text-xs mt-5">:</span>
                                                    <div class="flex-1">
                                                        <label class="text-xs text-gray-400">{{ $match->team2->name ?? 'Team 2' }}</label>
                                                        <input wire:model="score2" type="number" min="0" class="w-full px-2 py-1 bg-gray-800 border border-gray-700 rounded text-sm text-center">
                                                    </div>
                                                </div>
                                                <div class="flex gap-2">
                                                    <button wire:click="saveScore" class="flex-1 h-10 text-sm font-medium bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg">Simpan</button>
                                                    <button wire:click="cancelEdit" class="flex-1 h-10 text-sm font-medium bg-gray-700 hover:bg-gray-600 text-gray-300 rounded-lg">Batal</button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endforeach

                        {{-- Connectors --}}
                        <svg class="absolute top-0 left-0 pointer-events-none"
                             width="{{ $bracketLayout['width'] }}" height="{{ $bracketLayout['height'] }}"
                             viewBox="0 0 {{ $bracketLayout['width'] }} {{ $bracketLayout['height'] }}">
                            @foreach ($bracketLayout['lines'] as $line)
                                <line x1="{{ $line[0] }}" y1="{{ $line[1] }}"
                                      x2="{{ $line[2] }}" y2="{{ $line[3] }}"
                                      stroke="{{ $line[4] ?? false ? '#10b981' : '#52525b' }}"
                                      stroke-width="2" stroke-linecap="round"/>
                            @endforeach
                        </svg>

                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
