<!DOCTYPE html>
<html lang="id" class="bg-gray-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Badminton Fun Match — {{ $title ?? 'Admin' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased text-gray-100">
    <nav class="bg-gray-800 border-b border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center gap-4">
                    <a href="{{ route('tournaments.index') }}" class="text-xl font-bold tracking-tight">
                        🏸 Fun Match
                    </a>
                    <span class="text-sm text-gray-400">Admin</span>
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-400">
                    <span>score.jawakoentji.my.id</span>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{ $slot }}
    </main>

    @livewireScripts

    <div class="text-center text-xs text-gray-700 py-6 border-t border-gray-800 mt-8">
        <a href="https://github.com/ahmadzaenimubarok/score-hub" target="_blank" class="hover:text-gray-500 transition-colors">🐙 GitHub</a>
        &middot; Badminton Fun Match &middot; score.jawakoentji.my.id
    </div>
</body>
</html>
