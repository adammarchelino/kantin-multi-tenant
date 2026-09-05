<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tenant Panel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex">
    <aside class="w-64 bg-gray-900 text-white p-4">
        Sidebar Tenant
    </aside>
    <main class="flex-1 p-6">
        {{ $slot }}
    </main>
</body>
</html>