<div>
    {{-- Header --}}
    <div class="flex items-start justify-between mb-6">
        <div>
            <a href="{{ route('tournaments.index') }}" class="text-sm text-gray-500 hover:text-gray-300 mb-1 inline-block">&larr; Kembali</a>
            <h1 class="text-3xl font-bold">{{ $tournament->name }}</h1>
            <div class="flex items-center gap-3 mt-1">
                <span class="text-xs px-2.5 py-1 rounded-full font-medium
                    @if($tournament->status === 'draft') bg-gray-700 text-gray-400
                    @elseif($tournament->status === 'ongoing') bg-amber-900/50 text-amber-300 border border-amber-700
                    @else bg-emerald-900/50 text-emerald-300 border border-emerald-700
                    @endif
                ">
                    {{ $tournament->status }}
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
        <div class="flex gap-2">
            @if($tournament->status === 'draft' && $tournament->teams->count() >= 2 && $tournament->gameMatches->count() > 0)
                <button wire:click="startTournament" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-medium rounded-lg transition-colors text-sm">
                    Mulai Turnamen
                </button>
            @endif
            @if($tournament->status !== 'draft')
                <button wire:click="resetTournament" wire:confirm="Reset turnamen? Semua data pertandingan akan hilang." class="px-4 py-2 bg-red-900/50 hover:bg-red-800 text-red-300 border border-red-800 rounded-lg transition-colors text-sm">
                    Reset
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
        <a href="{{ route('public.bracket', $tournament->code) }}" target="_blank" class="px-4 py-2.5 text-sm font-medium text-gray-500 hover:text-gray-300 ml-auto">
            Tampilan Publik &nearr;
        </a>
    </div>

    {{-- Tab: Participants --}}
    @if($tab === 'participants')
        <div>
            <form wire:submit="addParticipant" class="flex gap-3 mb-6">
                <input
                    wire:model="participantName"
                    type="text"
                    placeholder="Nama peserta..."
                    class="flex-1 px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-lg text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                >
                <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-medium rounded-lg transition-colors text-sm">
                    + Tambah
                </button>
            </form>

            <div class="space-y-2">
                @forelse ($tournament->participants as $p)
                    <div class="flex items-center justify-between px-4 py-3 bg-gray-800/50 rounded-lg">
                        <span>{{ $p->name }}</span>
                        <button wire:click="removeParticipant({{ $p->id }})" wire:confirm="Hapus {{ $p->name }}?" class="text-red-500 hover:text-red-400 text-sm">
                            Hapus
                        </button>
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
            @if($tournament->participants->count() >= 2)
                <button wire:click="generateTeams" class="mb-6 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-medium rounded-lg transition-colors text-sm">
                    🔄 Generate Tim (acak)
                </button>
            @else
                <p class="mb-6 text-sm text-gray-500">Minimal 2 peserta untuk generate tim.</p>
            @endif

            @if($tournament->teams->count() > 0 || $tournament->gameMatches->count() > 0)
                <button wire:click="generateBracket" class="mb-6 ml-3 px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-medium rounded-lg transition-colors text-sm">
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
                {{-- Bracket Display --}}
                <div class="overflow-x-auto pb-6">
                    <div class="flex gap-6 min-w-[600px]" style="min-height: {{ count($bracketRounds) * 120 + 100 }}px;">
                        @foreach ($bracketRounds as $round => $matches)
                            <div class="flex-shrink-0 w-56">
                                <h3 class="text-sm font-semibold text-gray-400 mb-4 uppercase tracking-wider">
                                    Ronde {{ $round }}
                                    @if($loop->last)<span class="text-emerald-400 ml-2">(Final)</span>@endif
                                </h3>
                                <div class="space-y-4">
                                    @foreach ($matches as $match)
                                        <div class="bg-gray-800 border border-gray-700 rounded-lg p-3 {{ $match->status === 'ongoing' ? 'border-amber-600' : '' }} {{ $match->status === 'completed' ? 'border-emerald-800' : '' }}">
                                            {{-- Team 1 --}}
                                            <div class="flex items-center justify-between {{ $match->isTeam1Winner() ? 'text-emerald-400 font-semibold' : ($match->team1 ? 'text-gray-200' : 'text-gray-600') }}">
                                                <span class="text-sm truncate">
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
                                                <span class="text-sm font-mono ml-2">{{ $match->status !== 'pending' ? $match->score1 : '' }}</span>
                                            </div>
                                            {{-- VS --}}
                                            <div class="text-xs text-gray-600 my-1 text-center">VS</div>
                                            {{-- Team 2 --}}
                                            <div class="flex items-center justify-between {{ $match->isTeam2Winner() ? 'text-emerald-400 font-semibold' : ($match->team2 ? 'text-gray-200' : 'text-gray-600') }}">
                                                <span class="text-sm truncate">
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
                                                <span class="text-sm font-mono ml-2">{{ $match->status !== 'pending' ? $match->score2 : '' }}</span>
                                            </div>

                                            {{-- Actions --}}
                                            @if($match->status === 'pending' && $match->team1_id && $match->team2_id)
                                                <button wire:click="startMatch({{ $match->id }})" class="mt-2 w-full text-xs py-1.5 bg-amber-600 hover:bg-amber-500 text-white rounded transition-colors">
                                                    Mulai
                                                </button>
                                            @endif

                                            @if($match->status === 'ongoing')
                                                <a href="{{ route('scoreboard.show', $match->id) }}"
                                                   class="block mt-2 w-full text-xs py-1.5 bg-blue-600 hover:bg-blue-500 text-white rounded transition-colors text-center">
                                                    🏸 Scoreboard
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
                                                        <button wire:click="saveScore" class="flex-1 text-xs py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded">Simpan</button>
                                                        <button wire:click="cancelEdit" class="flex-1 text-xs py-1.5 bg-gray-700 hover:bg-gray-600 text-gray-300 rounded">Batal</button>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
