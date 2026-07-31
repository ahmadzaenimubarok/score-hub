<!DOCTYPE html>
<html lang="id" class="bg-gray-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>🏸 Pendaftaran — Badminton Fun Match</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased text-gray-100 min-h-screen bg-gray-950">
    <div class="max-w-xl mx-auto px-4 py-8">
        {{ $slot }}
    </div>
    @livewireScripts
</body>
</html>
