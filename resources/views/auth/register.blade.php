<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - {{ $settings['shop_name'] ?? 'Three Dot Accounting' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 min-h-screen flex items-center justify-center p-4 antialiased text-slate-800">
    <div class="w-full max-w-md">
        <div class="text-center mb-6">
            <div class="inline-flex w-14 h-14 rounded-2xl bg-gradient-to-tr from-indigo-500 to-violet-500 items-center justify-center font-black text-white text-xl shadow-xl shadow-indigo-500/30 mb-2">
                3•
            </div>
            <h1 class="text-xl font-bold text-white tracking-tight">{{ $settings['shop_name'] ?? 'Three Dot Accounting' }}</h1>
            <p class="text-xs text-indigo-300">Register new shopkeeper or staff user</p>
        </div>

        <div class="bg-white rounded-2xl shadow-2xl p-8 border border-slate-100">
            <h2 class="text-lg font-bold text-slate-900 mb-1">Create Account</h2>
            <p class="text-xs text-slate-500 mb-5">Enter your details to access the shop dashboard</p>

            @if ($errors->any())
                <div class="mb-4 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">Full Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm shadow-sm"
                        placeholder="John Doe">
                </div>

                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm shadow-sm"
                        placeholder="john@example.com">
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">Password</label>
                    <input type="password" id="password" name="password" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm shadow-sm"
                        placeholder="At least 6 characters">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm shadow-sm"
                        placeholder="Repeat your password">
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm shadow-lg shadow-indigo-600/30 transition-all">
                        Register & Open Dashboard
                    </button>
                </div>
            </form>

            <div class="mt-5 text-center">
                <span class="text-xs text-slate-500">Already registered?</span>
                <a href="{{ route('login') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700 ml-1">Sign in here</a>
            </div>
        </div>
    </div>
</body>
</html>

