<div>
    {{-- Header Card --}}
    <div class="bg-gray-900 rounded-2xl border border-gray-800 p-6 text-center">
        <div class="text-4xl mb-2">🏸</div>
        <h1 class="text-2xl font-bold tracking-tight">Scoreboard Publik</h1>
        <p class="text-sm text-gray-500 mt-2">Tanpa turnamen, tanpa tim. Langsung main &amp; catat skor.</p>
    </div>

    {{-- Flash Messages --}}
    @if (session('message'))
        <div class="mt-4 px-4 py-3 bg-emerald-900/50 border border-emerald-700 rounded-xl text-emerald-200 text-sm">
            ✅ {{ session('message') }}
        </div>
    @endif

    {{-- Create Form --}}
    <div class="mt-4 bg-gray-900 rounded-2xl border border-gray-800 p-6">
        <h2 class="font-semibold text-lg mb-1">Mulai Scoreboard</h2>
        <p class="text-sm text-gray-500 mb-4">Isi nama pemain (boleh dikosongkan), lalu pilih format.</p>
        <form wire:submit="create" class="flex flex-col gap-3">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs text-gray-500 mb-1 block">Pemain Kiri</label>
                    <input
                        wire:model="nameA"
                        type="text"
                        placeholder="Pemain 1"
                        maxlength="60"
                        class="w-full px-4 py-3.5 bg-gray-800 border border-gray-700 rounded-xl text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-base"
                    >
                </div>
                <div>
                    <label class="text-xs text-gray-500 mb-1 block">Pemain Kanan</label>
                    <input
                        wire:model="nameB"
                        type="text"
                        placeholder="Pemain 2"
                        maxlength="60"
                        class="w-full px-4 py-3.5 bg-gray-800 border border-gray-700 rounded-xl text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-base"
                    >
                </div>
            </div>
            @error('nameA') <span class="text-xs text-red-400 -mt-1">{{ $message }}</span> @enderror
            @error('nameB') <span class="text-xs text-red-400 -mt-1">{{ $message }}</span> @enderror

            <div>
                <label class="text-xs text-gray-500 mb-1 block">Format</label>
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" wire:click="$set('gamesToWin', 1)"
                            class="py-3.5 rounded-xl border font-semibold transition-all text-base
                            {{ $gamesToWin === 1 ? 'bg-emerald-600 border-emerald-500 text-white' : 'bg-gray-800 border-gray-700 text-gray-400 hover:bg-gray-700' }}">
                        Best of 1
                    </button>
                    <button type="button" wire:click="$set('gamesToWin', 2)"
                            class="py-3.5 rounded-xl border font-semibold transition-all text-base
                            {{ $gamesToWin === 2 ? 'bg-emerald-600 border-emerald-500 text-white' : 'bg-gray-800 border-gray-700 text-gray-400 hover:bg-gray-700' }}">
                        Best of 3
                    </button>
                </div>
                <p class="text-[11px] text-gray-600 mt-1.5">
                    {{ $gamesToWin === 1 ? 'Main 1 game, siapa menang langsung selesai.' : 'Main maksimal 3 game, pemenang best of 3.' }}
                </p>
            </div>
            @error('gamesToWin') <span class="text-xs text-red-400 -mt-1">{{ $message }}</span> @enderror

            <button type="submit"
                    class="w-full py-4 bg-emerald-600 hover:bg-emerald-500 active:scale-[0.98] text-white font-bold rounded-xl transition-all text-base">
                ▶ Mulai Scoreboard
            </button>
        </form>
    </div>

    <p class="text-center text-xs text-gray-700 mt-6">
        🏸 Badminton Fun Match &middot; score.jawakoentji.my.id
    </p>
</div>
