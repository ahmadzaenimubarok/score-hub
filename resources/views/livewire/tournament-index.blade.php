<div>
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold">Turnamen</h1>
        <div class="text-sm text-gray-400">
            {{ $tournaments->total() }} total
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session('message'))
        <div class="mb-6 px-4 py-3 bg-emerald-900/50 border border-emerald-700 rounded-lg text-emerald-200 text-sm">
            {{ session('message') }}
        </div>
    @endif

    {{-- Create Form --}}
    <form wire:submit="create" class="mb-8 flex gap-3">
        <input
            wire:model="newName"
            type="text"
            placeholder="Nama turnamen baru..."
            class="flex-1 px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-lg text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
        >
        <button
            type="submit"
            class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-medium rounded-lg transition-colors"
        >
            + Buat
        </button>
    </form>

    {{-- Tournament List --}}
    <div class="space-y-3">
        @forelse ($tournaments as $t)
            <a
                href="{{ route('tournaments.show', $t) }}"
                class="block px-6 py-4 bg-gray-800 border border-gray-700 rounded-lg hover:border-gray-600 transition-colors group"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold group-hover:text-emerald-400 transition-colors">{{ $t->name }}</h3>
                        <p class="text-sm text-gray-500 mt-0.5">
                            {{ $t->participants_count ?? 0 }} peserta
                            &middot; {{ $t->teams_count ?? 0 }} tim
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs px-2.5 py-1 rounded-full font-medium
                            @if($t->status === 'draft') bg-gray-700 text-gray-400
                            @elseif($t->status === 'ongoing') bg-amber-900/50 text-amber-300 border border-amber-700
                            @else bg-emerald-900/50 text-emerald-300 border border-emerald-700
                            @endif
                        ">
                            {{ $t->status }}
                        </span>
                        <span class="text-gray-600 group-hover:text-gray-400">→</span>
                    </div>
                </div>
            </a>
        @empty
            <div class="text-center py-16 text-gray-500">
                <p class="text-5xl mb-4">🏸</p>
                <p>Belum ada turnamen. Buat yang pertama!</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $tournaments->links() }}
    </div>
</div>
