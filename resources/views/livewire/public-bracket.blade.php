<div>
    {{-- Header --}}
    <div class="text-center mb-8 mt-4">
        <h1 class="text-4xl font-bold tracking-tight">🏸 {{ $tournament->name }}</h1>
        <div class="mt-2 flex items-center justify-center gap-3">
            <span class="text-xs px-2.5 py-1 rounded-full font-medium
                @if($tournament->status === 'draft') bg-gray-700 text-gray-400
                @elseif($tournament->status === 'ongoing') bg-amber-900/50 text-amber-300 border border-amber-700
                @else bg-emerald-900/50 text-emerald-300 border border-emerald-700
                @endif
            ">
                {{ $tournament->status === 'ongoing' ? 'LIVE' : ($tournament->status === 'completed' ? 'SELESAI' : 'Akan Datang') }}
            </span>
            @if($tournament->status === 'ongoing')
                <span class="flex items-center gap-1.5 text-xs text-red-400">
                    <span class="inline-block w-2 h-2 bg-red-500 rounded-full polling-indicator"></span>
                    Live
                </span>
            @endif
        </div>
    </div>

    {{-- Champion Banner --}}
    @if($champion)
        <div class="text-center mb-8 p-6 bg-gradient-to-r from-amber-900/30 via-emerald-900/30 to-amber-900/30 border border-amber-700/50 rounded-2xl">
            <p class="text-sm text-amber-400 uppercase tracking-widest mb-1">Juara</p>
            <h2 class="text-3xl font-bold text-emerald-300">🏆 {{ $champion->name }}</h2>
        </div>
    @endif

    {{-- Bracket --}}
    @if($tournament->gameMatches->count() > 0 && count($bracketLayout['rounds']) > 0)
        <div class="overflow-x-auto pb-8">
            <div class="relative mx-auto"
                 style="width: {{ $bracketLayout['width'] }}px; height: {{ $bracketLayout['height'] }}px;">

                {{-- Round headers --}}
                @foreach ($bracketLayout['rounds'] as $round => $matches)
                    <div class="absolute top-0 flex items-center justify-center"
                         style="left: {{ $bracketLayout['roundLeft'][$round] }}px; width: 224px; height: {{ $bracketLayout['headerH'] }}px;">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider text-center">
                            Ronde {{ $round }}
                            @if($loop->last)<span class="text-emerald-500 ml-1">🏆</span>@endif
                        </h3>
                    </div>
                @endforeach

                {{-- Match cards --}}
                @foreach ($bracketLayout['rounds'] as $round => $matches)
                    <div class="absolute top-0" style="left: {{ $bracketLayout['roundLeft'][$round] }}px;">
                        @foreach ($matches as $match)
                            <div class="absolute w-56 flex flex-col justify-center p-3 bg-gray-900 border rounded-lg
                                        {{ $match->status === 'ongoing' ? 'border-amber-700' : ($match->status === 'completed' ? 'border-emerald-900' : 'border-gray-800') }}"
                                 style="top: {{ $bracketLayout['tops'][$match->id] }}px; height: {{ $bracketLayout['cardH'] }}px;">
                                {{-- Team 1 --}}
                                <div class="flex items-center justify-between {{ $match->isTeam1Winner() ? 'text-emerald-400 font-semibold' : ($match->team1 ? 'text-gray-300' : 'text-gray-700') }}">
                                    <span class="text-sm truncate">
                                        @if($match->team1)
                                            {{ $match->team1->name }}
                                            @if($match->team1->members->isNotEmpty())
                                                <span class="text-xs text-gray-500 ml-1">({{ $match->team1->membersList() }})</span>
                                            @endif
                                        @elseif($match->isBye())
                                            <span class="text-yellow-700">BYE</span>
                                        @else
                                            —
                                        @endif
                                    </span>
                                    <span class="text-sm font-mono ml-2">{{ $match->status !== 'pending' ? $match->score1 : '' }}</span>
                                </div>
                                {{-- VS --}}
                                <div class="text-xs text-gray-700 my-1 text-center">VS</div>
                                {{-- Team 2 --}}
                                <div class="flex items-center justify-between {{ $match->isTeam2Winner() ? 'text-emerald-400 font-semibold' : ($match->team2 ? 'text-gray-300' : 'text-gray-700') }}">
                                    <span class="text-sm truncate">
                                        @if($match->team2)
                                            {{ $match->team2->name }}
                                            @if($match->team2->members->isNotEmpty())
                                                <span class="text-xs text-gray-500 ml-1">({{ $match->team2->membersList() }})</span>
                                            @endif
                                        @elseif($match->isBye())
                                            <span class="text-yellow-700">BYE</span>
                                        @else
                                            —
                                        @endif
                                    </span>
                                    <span class="text-sm font-mono ml-2">{{ $match->status !== 'pending' ? $match->score2 : '' }}</span>
                                </div>
                                @if($match->status === 'ongoing')
                                    <a href="{{ route('public.scoreboard', ['code' => $tournament->code, 'gameMatch' => $match->id]) }}"
                                       class="block mt-2 text-xs py-1 bg-blue-600/30 hover:bg-blue-600/50 text-blue-300 rounded text-center transition-colors">
                                        🏸 Scoreboard
                                    </a>
                                @endif
                                @if($match->status === 'completed' && $match->winner)
                                    <div class="mt-1.5 text-xs {{ $match->isBye() ? 'text-yellow-700' : 'text-emerald-600' }} text-center">
                                        @if($match->isBye())
                                            ↪ {{ $match->winner->name }} (BYE)
                                        @else
                                            ✓ {{ $match->winner->name }}
                                        @endif
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
                              stroke="#52525b" stroke-width="2" stroke-linecap="round"/>
                    @endforeach
                </svg>

            </div>
        </div>
    @else
        <div class="text-center py-20 text-gray-600">
            <p class="text-6xl mb-4">🏸</p>
            <p>Bracket belum tersedia.</p>
        </div>
    @endif

    <div class="text-center text-xs text-gray-700 mt-8 pb-8">
        <a href="https://github.com/ahmadzaenimubarok/score-hub" target="_blank" class="hover:text-gray-500 transition-colors">🐙 GitHub</a>
        &middot; Badminton Fun Match &middot; score.jawakoentji.my.id
    </div>

    {{-- Livewire polling — live selama ada match yg berjalan --}}
    @if($tournament->status !== 'completed' && $tournament->gameMatches->contains(fn($m) => $m->status === 'ongoing'))
        <div wire:poll.3000ms="$refresh"></div>
    @endif
</div>
