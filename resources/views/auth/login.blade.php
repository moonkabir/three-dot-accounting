<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - {{ $settings['shop_name'] ?? 'Three Dot Accounting' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 min-h-screen flex items-center justify-center p-4 antialiased text-slate-800">
    <div class="w-full max-w-md">
        <!-- Logo & Header -->
        <div class="text-center mb-8">
            <div class="inline-flex w-16 h-16 rounded-2xl bg-gradient-to-tr from-indigo-500 to-violet-500 items-center justify-center font-black text-white text-2xl shadow-xl shadow-indigo-500/30 mb-3">
                3•
            </div>
            <h1 class="text-2xl font-black text-white tracking-tight">{{ $settings['shop_name'] ?? 'Three Dot Accounting' }}</h1>
            <p class="text-sm text-indigo-300 mt-1">{{ $settings['shop_tagline'] ?? 'Shop Inventory, Sales & Accounting System' }}</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-2xl shadow-2xl p-8 border border-slate-100">
            <div class="mb-6">
                <h2 class="text-xl font-bold text-slate-900">Sign in to your account</h2>
                <p class="text-xs text-slate-500 mt-1">Manage inventory, bills, customers & vendors</p>
            </div>

            <!-- Flash Error -->
            @if ($errors->any())
                <div class="mb-5 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-medium">
                    {{ $errors->first() }}
                </div>
            @endif

            @if (session('info'))
                <div class="mb-5 p-3.5 rounded-xl bg-blue-50 border border-blue-200 text-blue-700 text-xs font-medium">
                    {{ session('info') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email', 'admin@threedot.com') }}" required autofocus
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-all shadow-sm"
                        placeholder="you@example.com">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label for="password" class="block text-xs font-semibold text-slate-700">Password</label>
                    </div>
                    <input type="password" id="password" name="password" value="password" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-all shadow-sm"
                        placeholder="••••••••">
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center gap-2 cursor-pointer text-slate-600">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                        <span>Remember me for 30 days</span>
                    </label>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-semibold text-sm shadow-lg shadow-indigo-600/30 transition-all flex items-center justify-center gap-2">
                        <span>Sign In to Dashboard</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </div>
            </form>

            <!-- Quick Demo Credentials Fill -->
            <div class="mt-6 pt-5 border-t border-slate-100 text-center">
                <div class="text-xs text-slate-400 font-medium mb-2.5">Demo Credentials (Pre-filled):</div>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-200 text-slate-600 text-xs">
                    <span>Email: <strong class="text-slate-800">admin@threedot.com</strong></span>
                    <span class="text-slate-300">•</span>
                    <span>Pass: <strong class="text-slate-800">password</strong></span>
                </div>
            </div>

            <div class="mt-4 text-center">
                <span class="text-xs text-slate-500">Need a new shop account?</span>
                <a href="{{ route('register') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700 ml-1">Create an account</a>
            </div>
        </div>

        <div class="text-center mt-6 text-xs text-slate-400">
            &copy; {{ date('Y') }} {{ $settings['shop_name'] ?? 'Three Dot Accounting' }}. All rights reserved.
        </div>
    </div>
</body>
</html>

