<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set New Password - Kingdom Recruitments</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#020611] text-white font-body selection:bg-kingdom-gold selection:text-black antialiased relative min-h-screen flex flex-col justify-between">
    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/stardust.png')] opacity-10 pointer-events-none z-0"></div>

    <main class="flex-grow flex items-center justify-center p-6 relative z-10">
        <div class="max-w-md w-full bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-8 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-kingdom-gold/20 rounded-full blur-[50px] -mr-10 -mt-10 pointer-events-none"></div>

            <h2 class="text-3xl font-bold mb-6">Create New Password</h2>

            @if ($errors->any())
                <div class="mb-4 bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-xl text-sm">
                    <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Email Address</label>
                    <input type="email" name="email" value="{{ $email ?? old('email') }}" required readonly
                        class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-gray-400 cursor-not-allowed focus:outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">New Password</label>
                    <input type="password" name="password" required autofocus
                        class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-kingdom-gold focus:ring-1 focus:ring-kingdom-gold transition-all">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Confirm New Password</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-kingdom-gold focus:ring-1 focus:ring-kingdom-gold transition-all">
                </div>

                <button type="submit" class="w-full bg-kingdom-gold hover:bg-yellow-400 text-black font-bold py-3 px-6 rounded-xl transition-all shadow-[0_0_15px_rgba(255,215,0,0.3)]">
                    Reset Password
                </button>
            </form>
        </div>
    </main>
</body>
</html>
