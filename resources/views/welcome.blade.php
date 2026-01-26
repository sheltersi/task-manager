<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Task Manager') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-gradient-to-br from-gray-950 via-gray-900 to-gray-950 text-white">

    <!-- Top Nav -->
    <header class="max-w-7xl mx-auto px-6 py-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-2xl bg-white/10 flex items-center justify-center shadow">
                <span class="text-xl font-bold">✓</span>
            </div>
            <span class="text-lg font-semibold tracking-wide">
                {{ config('app.name', 'Task Manager') }}
            </span>
        </div>

        @if (Route::has('login'))
            <nav class="flex items-center gap-3">
                @auth
                    <a href="{{ url('/dashboard') }}"
                       class="px-4 py-2 rounded-xl bg-white text-gray-900 font-semibold hover:bg-gray-200 transition">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/15 border border-white/10 transition">
                        Log in
                    </a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                           class="px-4 py-2 rounded-xl bg-white text-gray-900 font-semibold hover:bg-gray-200 transition">
                            Get Started
                        </a>
                    @endif
                @endauth
            </nav>
        @endif
    </header>

    <!-- Hero -->
    <main class="max-w-7xl mx-auto px-6 py-16">
        <div class="grid lg:grid-cols-2 gap-12 items-center">

            <!-- Left -->
            <div>
                <p class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 border border-white/10 text-sm">
                    🚀 Internship Assessment Project
                </p>

                <h1 class="mt-6 text-4xl sm:text-5xl font-bold leading-tight">
                    Manage your tasks
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-pink-400">
                        effortlessly
                    </span>
                </h1>

                <p class="mt-5 text-gray-300 text-lg leading-relaxed">
                    A simple and functional task management system built with Laravel Jetstream,
                    authentication, and clean CRUD workflows.
                </p>

                <div class="mt-8 flex flex-col sm:flex-row gap-3">
                    @auth
                        <a href="{{ url('/dashboard') }}"
                           class="px-6 py-3 rounded-2xl bg-white text-gray-900 font-semibold hover:bg-gray-200 transition">
                            Go to Dashboard →
                        </a>
                    @else
                        <a href="{{ route('register') }}"
                           class="px-6 py-3 rounded-2xl bg-white text-gray-900 font-semibold hover:bg-gray-200 transition">
                            Create Account
                        </a>

                        <a href="{{ route('login') }}"
                           class="px-6 py-3 rounded-2xl bg-white/10 border border-white/10 hover:bg-white/15 transition">
                            Sign In
                        </a>
                    @endauth
                </div>

                <!-- Feature bullets -->
                <div class="mt-10 grid sm:grid-cols-2 gap-4">
                    <div class="p-5 rounded-2xl bg-white/5 border border-white/10">
                        <h3 class="font-semibold">🔐 Secure Authentication</h3>
                        <p class="text-sm text-gray-300 mt-1">
                            Register, login, logout with Jetstream.
                        </p>
                    </div>

                    <div class="p-5 rounded-2xl bg-white/5 border border-white/10">
                        <h3 class="font-semibold">✅ Task CRUD</h3>
                        <p class="text-sm text-gray-300 mt-1">
                            Create, edit, complete and delete tasks.
                        </p>
                    </div>

                    <div class="p-5 rounded-2xl bg-white/5 border border-white/10">
                        <h3 class="font-semibold">⚡ Fast & Simple UI</h3>
                        <p class="text-sm text-gray-300 mt-1">
                            Clean Blade + Tailwind templates.
                        </p>
                    </div>

                    <div class="p-5 rounded-2xl bg-white/5 border border-white/10">
                        <h3 class="font-semibold">📌 User-Owned Tasks</h3>
                        <p class="text-sm text-gray-300 mt-1">
                            Each user manages their own tasks securely.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Right -->
            <div class="relative">
                <div class="absolute -inset-6 blur-3xl opacity-30 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 rounded-full"></div>

                <div class="relative rounded-3xl bg-white/5 border border-white/10 p-6 shadow-xl">
                    <h2 class="text-lg font-semibold">Today</h2>
                    <p class="text-sm text-gray-300 mt-1">Your focus list</p>

                    <div class="mt-6 space-y-3">
                        <div class="flex items-center justify-between p-4 rounded-2xl bg-white/5 border border-white/10">
                            <div>
                                <p class="font-medium">Finish internship assessment</p>
                                <p class="text-xs text-gray-400">Priority: High • Due: Today</p>
                            </div>
                            <span class="text-xs px-3 py-1 rounded-full bg-green-500/20 text-green-300 border border-green-500/30">
                                Pending
                            </span>
                        </div>

                        <div class="flex items-center justify-between p-4 rounded-2xl bg-white/5 border border-white/10">
                            <div>
                                <p class="font-medium">Build Task CRUD</p>
                                <p class="text-xs text-gray-400">Priority: Medium • Due: Tomorrow</p>
                            </div>
                            <span class="text-xs px-3 py-1 rounded-full bg-yellow-500/20 text-yellow-300 border border-yellow-500/30">
                                In Progress
                            </span>
                        </div>

                        <div class="flex items-center justify-between p-4 rounded-2xl bg-white/5 border border-white/10">
                            <div>
                                <p class="font-medium">Push project to GitHub</p>
                                <p class="text-xs text-gray-400">Priority: Low • Due: This week</p>
                            </div>
                            <span class="text-xs px-3 py-1 rounded-full bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                                Planned
                            </span>
                        </div>
                    </div>

                    <div class="mt-6 text-xs text-gray-400">
                        Built with Laravel • Jetstream • Tailwind • MySQL
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- Footer -->
    <footer class="max-w-7xl mx-auto px-6 pb-10 text-sm text-gray-400">
        <div class="flex flex-col sm:flex-row justify-between gap-3 border-t border-white/10 pt-6">
            <p>© {{ date('Y') }} {{ config('app.name', 'Task Manager') }}. All rights reserved.</p>
            <p class="text-gray-500">Made by Shelter Sibanda</p>
        </div>
    </footer>

</body>
</html>
