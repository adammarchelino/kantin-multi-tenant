<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Panel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen">
    <nav class="bg-gray-800 text-white p-4">Admin Nav</nav>
    <main class="p-6 overflow-x-auto">
        {{ $slot }}
    </main>
</body>
</html>