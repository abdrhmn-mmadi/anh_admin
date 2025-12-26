<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex">

    <!-- Left Section -->
    <div class="w-full md:w-1/2 flex items-center justify-center bg-green-500">
        <div class="w-4/5 max-w-md bg-white p-8 rounded-lg shadow-lg">

            <!-- LOGO -->
            <div class="flex justify-center mb-4">
                <img src="/photos/logo.png" alt="Logo" class="h-16 w-auto" style="height: 150px;">
            </div>

            <!-- WELCOME TEXT -->
            <h2 class="text-3xl font-bold text-green-600 mb-6 text-center">
                Bienvenu chez ANH
            </h2>

            @if($errors->any())
                <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- FORM -->
            <form method="POST" action="/login">
                @csrf

                <div class="mb-4">
                    <label for="email" class="block text-gray-700 font-semibold mb-2">
                        Email
                    </label>
                    <input type="email" name="email" id="email" required
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-gray-700 font-semibold mb-2">
                        Password
                    </label>
                    <input type="password" name="password" id="password" required
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center text-gray-700">
                        <input type="checkbox" name="remember" class="mr-2">
                        Remember Me
                    </label>
                    <a href="/forgot-password" class="text-green-600 hover:underline text-sm">
                        Forgot Password?
                    </a>
                </div>

                <button type="submit"
                    class="w-full bg-green-600 text-white py-2 rounded-lg font-semibold hover:bg-green-700 transition">
                    Login
                </button>
            </form>
        </div>
    </div>

    <!-- Right Image -->
    <div class="hidden md:block w-1/2">
        <img src="/photos/anh.jpg" alt="Login Image" class="w-full h-full object-cover">
    </div>

</body>
</html>
