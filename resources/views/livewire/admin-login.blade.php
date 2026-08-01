<div class="max-w-sm mx-auto">
    <form wire:submit="login" class="bg-gray-800 border border-gray-700 rounded-2xl p-6 sm:p-8 space-y-5 shadow-xl">
        <div class="text-center">
            <div class="text-4xl mb-2">🏸</div>
            <h1 class="text-xl font-bold tracking-tight">Login Admin</h1>
            <p class="text-sm text-gray-400 mt-1">Skor Cast</p>
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-300 mb-1.5">Email</label>
            <input type="email" id="email" wire:model="email" autocomplete="email" required
                   class="w-full rounded-xl bg-gray-900 border border-gray-700 px-4 py-3 text-gray-100
                          placeholder-gray-600 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/30
                          focus:outline-none transition-colors"
                   placeholder="admin@skorcast.online">
            @error('email')
                <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-300 mb-1.5">Password</label>
            <input type="password" id="password" wire:model="password" autocomplete="current-password" required
                   class="w-full rounded-xl bg-gray-900 border border-gray-700 px-4 py-3 text-gray-100
                          placeholder-gray-600 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/30
                          focus:outline-none transition-colors"
                   placeholder="••••••••">
            @error('password')
                <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        <label class="flex items-center gap-2 text-sm text-gray-400 select-none">
            <input type="checkbox" wire:model="remember"
                   class="w-4 h-4 rounded bg-gray-900 border-gray-700 text-emerald-500 focus:ring-emerald-500/30">
            Ingat saya
        </label>

        <button type="submit" wire:loading.attr="disabled"
                class="w-full h-12 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:scale-[0.98]
                       text-white font-semibold transition-all flex items-center justify-center gap-2">
            <span wire:loading.remove wire:target="login">Masuk</span>
            <span wire:loading wire:target="login">Memproses…</span>
        </button>
    </form>

    <p class="text-center text-xs text-gray-600 mt-6">
        Khusus admin & panitia. Scoreboard & bracket publik tetap bisa diakses tanpa login.
    </p>
</div>
