<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Login - POS Apotek</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-gradient-to-br from-sky-100 via-blue-50 to-indigo-100 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="rounded-2xl border border-slate-200 bg-white/90 p-8 shadow-xl shadow-sky-100 backdrop-blur-sm">
            <div class="mb-6 text-center">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-600 text-2xl font-bold text-white shadow-lg shadow-blue-200">
                    P
                </div>
                <h1 class="text-2xl font-bold text-slate-900">POS Apotek</h1>
                <p class="mt-2 text-sm text-slate-500">Silakan login untuk melanjutkan</p>
            </div>

            @if ($errors->any())
                <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-600">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700" for="email">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                        class="form-input h-11 border-slate-200 bg-slate-50 text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700" for="password">Password</label>
                    <input type="password" name="password" id="password" required
                        class="form-input h-11 border-slate-200 bg-slate-50 text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                </div>

                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    Ingat saya
                </label>

                <button type="submit"
                    class="btn-primary h-11 w-full rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-sm font-semibold shadow-lg shadow-blue-200 transition hover:from-blue-700 hover:to-indigo-700">
                    Masuk
                </button>
            </form>
        </div>
    </div>
</body>

</html>