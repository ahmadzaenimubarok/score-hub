<div class="min-h-dvh flex flex-col items-center justify-center px-6 py-8">
    {{-- Header Card --}}
    <div class="w-full max-w-md bg-gray-900 rounded-2xl border border-gray-800 p-6 text-center">
        <div class="text-5xl mb-3">🏸</div>
        <h1 class="text-2xl font-bold tracking-tight">Scoreboard Publik</h1>
        <p class="text-sm text-gray-500 mt-2">Tanpa turnamen, tanpa tim.<br>Langsung main &amp; catat skor.</p>

        {{-- Flash Messages --}}
        @if (session('message'))
            <div class="mt-4 px-4 py-3 bg-emerald-900/50 border border-emerald-700 rounded-xl text-emerald-200 text-sm">
                ✅ {{ session('message') }}
            </div>
        @endif

        {{-- Single create action (no form) --}}
        <div class="mt-6 bg-gray-800/60 rounded-xl border border-gray-700 p-4 text-left">
            <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-3">
                    <span class="w-9 h-9 flex items-center justify-center rounded-lg bg-emerald-600/20 text-emerald-400 font-bold">1</span>
                    <span class="text-sm font-semibold text-gray-200">Tim 1</span>
                </div>
                <span class="text-2xl text-gray-500 font-bold">vs</span>
                <div class="flex items-center gap-3">
                    <span class="text-sm font-semibold text-gray-200">Tim 2</span>
                    <span class="w-9 h-9 flex items-center justify-center rounded-lg bg-emerald-600/20 text-emerald-400 font-bold">2</span>
                </div>
            </div>
            <p class="text-[11px] text-gray-600 mt-2 text-center">Best of 3 &middot; 21 poin &middot; rally point</p>
        </div>

        <button wire:click="create"
                class="mt-6 w-full py-4 bg-emerald-600 hover:bg-emerald-500 active:scale-[0.98] text-white font-bold rounded-xl transition-all text-base">
            ▶ Buat Scoreboard
        </button>
    </div>

    <p class="text-center text-xs text-gray-700 mt-6">
        🏸 Badminton Fun Match &middot; score.jawakoentji.my.id
    </p>
</div>
