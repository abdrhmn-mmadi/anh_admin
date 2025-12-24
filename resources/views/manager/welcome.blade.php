<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex">

    <!-- Sidebar -->
    <aside class="w-64 bg-green-600 text-white flex flex-col">
        <div class="p-6 text-center font-bold text-xl border-b border-green-500">
            Admin Panel
        </div>
        <nav class="flex-1 p-4 space-y-2">
            <a href="#" class="block py-2 px-4 rounded hover:bg-green-500 transition">Dashboard</a>
            <a href="#" class="block py-2 px-4 rounded hover:bg-green-500 transition">Users</a>
            <a href="#" class="block py-2 px-4 rounded hover:bg-green-500 transition">Reports</a>
            <a href="#" class="block py-2 px-4 rounded hover:bg-green-500 transition">Settings</a>
        </nav>
    </aside>

    <!-- Main content -->
    <main class="flex-1 p-8">
        <header class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Welcome, Manager!</h1>
            <button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">Logout</button>
        </header>

        <!-- Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
                <h2 class="text-xl font-semibold mb-2">Users</h2>
                <p class="text-gray-600">Manage your users</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
                <h2 class="text-xl font-semibold mb-2">Reports</h2>
                <p class="text-gray-600">View detailed reports</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
                <h2 class="text-xl font-semibold mb-2">Settings</h2>
                <p class="text-gray-600">Configure your system</p>
            </div>
        </div>

        <!-- Chart / Placeholder -->
        <div class="mt-8 bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-semibold mb-4">Overview</h2>
            <div class="h-48 bg-gray-200 flex items-center justify-center text-gray-500">
                Chart Placeholder
            </div>
        </div>
    </main>

</body>
</html>
