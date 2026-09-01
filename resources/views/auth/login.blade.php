<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Login - POS Apotek</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-100 h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-sm">
        <h1 class="text-xl font-semibold mb-1 text-center">POS Apotek</h1>
        <p class="text-sm text-gray-500 text-center mb-6">Silakan login untuk melanjutkan</p>

        @if ($errors->any())
        <div class="bg-red-50 text-red-600 text-sm p-3 rounded mb-4">
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm text-gray-700 mb-1" for="email">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm text-gray-700 mb-1" for="password">Password</label>
                <input type="password" name="password" id="password" required
                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <label class="flex items-center text-sm text-gray-600">
                <input type="checkbox" name="remember" class="mr-2"> Ingat saya
            </label>

            <button type="submit"
                class="w-full bg-blue-600 text-white rounded py-2 text-sm font-medium hover:bg-blue-700">
                Masuk
            </button>
        </form>
    </div>
</body>

</html>