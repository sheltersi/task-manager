<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="TaskFlow - Organize, track, and collaborate on tasks effortlessly">
    <title>{{ config('app.name', 'TaskFlow') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-white text-gray-900 overflow-x-hidden">

    <!-- Animated Background -->
    <div class="fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute -top-[40%] -right-[20%] w-[800px] h-[800px] rounded-full bg-gradient-to-br from-blue-400/20 via-purple-400/10 to-pink-400/20 blur-3xl animate-pulse"></div>
        <div class="absolute -bottom-[30%] -left-[10%] w-[600px] h-[600px] rounded-full bg-gradient-to-tr from-emerald-400/15 via-cyan-400/10 to-blue-400/15 blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
    </div>

    <!-- Navigation -->
    <header class="fixed top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-lg border-b border-gray-100/50">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <a href="/" class="flex items-center gap-3 group">
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl blur opacity-30 group-hover:opacity-50 transition-opacity"></div>
                    <div class="relative h-11 w-11 rounded-2xl bg-gradient-to-br from-blue-600 to-purple-600 flex items-center justify-center shadow-lg shadow-blue-600/20">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                </div>
                <span class="text-xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">
                    {{ config('app.name', 'TaskFlow') }}
                </span>
            </a>

            @if (Route::has('login'))
                <nav class="flex items-center gap-3">
                    @auth
                        <a href="{{ url('/dashboard') }}"
                           class="px-5 py-2.5 rounded-xl bg-gray-900 text-white font-medium hover:bg-gray-800 transition-all shadow-lg shadow-gray-900/20 hover:shadow-xl hover:shadow-gray-900/30 hover:-translate-y-0.5">
                            Go to Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="px-5 py-2.5 rounded-xl bg-white text-gray-700 font-medium border border-gray-200 hover:border-gray-300 hover:bg-gray-50 transition-all">
                            Sign In
                        </a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                               class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 text-white font-medium hover:from-blue-700 hover:to-purple-700 transition-all shadow-lg shadow-blue-600/30 hover:shadow-xl hover:shadow-blue-600/40 hover:-translate-y-0.5">
                                Get Started Free
                            </a>
                        @endif
                    @endauth
                </nav>
            @endif
        </div>
    </header>

    <!-- Hero Section -->
    <main class="pt-32 pb-20 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="text-center max-w-4xl mx-auto mb-20">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-blue-50 border border-blue-100 text-sm font-medium text-blue-700 mb-6">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    Boost your productivity
                </div>

                <h1 class="text-5xl md:text-6xl lg:text-7xl font-bold tracking-tight leading-[1.1] mb-6">
                    Organize your work,
                    <span class="relative">
                        <span class="relative z-10 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 bg-clip-text text-transparent">
                            achieve more
                        </span>
                        <svg class="absolute -bottom-2 left-0 w-full h-3 text-blue-600/20" viewBox="0 0 200 12" preserveAspectRatio="none">
                            <path d="M0,8 Q50,0 100,8 T200,8" stroke="currentColor" stroke-width="4" fill="none" />
                        </svg>
                    </span>
                </h1>

                <p class="text-xl md:text-2xl text-gray-600 leading-relaxed mb-10 max-w-2xl mx-auto">
                    TaskFlow helps teams and individuals manage tasks with ease. Create, track, collaborate, and deliver projects faster than ever.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    @auth
                        <a href="{{ url('/dashboard') }}"
                           class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold text-lg hover:from-blue-700 hover:to-purple-700 transition-all shadow-xl shadow-blue-600/30 hover:shadow-2xl hover:shadow-blue-600/40 hover:-translate-y-1 flex items-center justify-center gap-2">
                            Go to Dashboard
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </a>
                    @else
                        <a href="{{ route('register') }}"
                           class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold text-lg hover:from-blue-700 hover:to-purple-700 transition-all shadow-xl shadow-blue-600/30 hover:shadow-2xl hover:shadow-blue-600/40 hover:-translate-y-1 flex items-center justify-center gap-2">
                            Start Free Today
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </a>

                        <a href="{{ route('login') }}"
                           class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-white text-gray-700 font-semibold text-lg border-2 border-gray-200 hover:border-gray-300 hover:bg-gray-50 transition-all flex items-center justify-center gap-2">
                            Watch Demo
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </a>
                    @endauth
                </div>

                <!-- Social Proof -->
                <div class="mt-16 pt-8 border-t border-gray-100">
                    <p class="text-sm text-gray-500 mb-4">Trusted by productive teams worldwide</p>
                    <div class="flex items-center justify-center gap-8 opacity-50 grayscale">
                        <div class="text-2xl font-bold text-gray-400">Acme Inc</div>
                        <div class="text-2xl font-bold text-gray-400">Globex</div>
                        <div class="text-2xl font-bold text-gray-400">Soylent</div>
                        <div class="text-2xl font-bold text-gray-400">Umbrella</div>
                    </div>
                </div>
            </div>

            <!-- Features Grid -->
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-20">
                <div class="group p-8 rounded-3xl bg-gradient-to-br from-white to-gray-50 border border-gray-100 hover:border-blue-200 hover:shadow-xl hover:shadow-blue-500/10 transition-all duration-300 hover:-translate-y-1">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center mb-6 shadow-lg shadow-blue-500/30">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-gray-900">Smart Task Management</h3>
                    <p class="text-gray-600 leading-relaxed">Create, organize, and prioritize tasks with intuitive workflows. Drag and drop to stay on top of your work.</p>
                </div>

                <div class="group p-8 rounded-3xl bg-gradient-to-br from-white to-gray-50 border border-gray-100 hover:border-purple-200 hover:shadow-xl hover:shadow-purple-500/10 transition-all duration-300 hover:-translate-y-1">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center mb-6 shadow-lg shadow-purple-500/30">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-gray-900">Team Collaboration</h3>
                    <p class="text-gray-600 leading-relaxed">Share tasks with team members. Assign roles, add comments, and keep everyone in the loop.</p>
                </div>

                <div class="group p-8 rounded-3xl bg-gradient-to-br from-white to-gray-50 border border-gray-100 hover:border-emerald-200 hover:shadow-xl hover:shadow-emerald-500/10 transition-all duration-300 hover:-translate-y-1">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center mb-6 shadow-lg shadow-emerald-500/30">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-gray-900">Progress Tracking</h3>
                    <p class="text-gray-600 leading-relaxed">Visualize your progress with status updates, priorities, and due dates. Never miss a deadline again.</p>
                </div>

                <div class="group p-8 rounded-3xl bg-gradient-to-br from-white to-gray-50 border border-gray-100 hover:border-orange-200 hover:shadow-xl hover:shadow-orange-500/10 transition-all duration-300 hover:-translate-y-1">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center mb-6 shadow-lg shadow-orange-500/30">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-gray-900">Smart Notifications</h3>
                    <p class="text-gray-600 leading-relaxed">Stay updated with real-time notifications. Know when tasks are assigned, updated, or completed.</p>
                </div>

                <div class="group p-8 rounded-3xl bg-gradient-to-br from-white to-gray-50 border border-gray-100 hover:border-cyan-200 hover:shadow-xl hover:shadow-cyan-500/10 transition-all duration-300 hover:-translate-y-1">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-cyan-500 to-cyan-600 flex items-center justify-center mb-6 shadow-lg shadow-cyan-500/30">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-gray-900">Secure by Design</h3>
                    <p class="text-gray-600 leading-relaxed">Your data is protected with enterprise-grade security. Only you and your collaborators can access your tasks.</p>
                </div>

                <div class="group p-8 rounded-3xl bg-gradient-to-br from-white to-gray-50 border border-gray-100 hover:border-pink-200 hover:shadow-xl hover:shadow-pink-500/10 transition-all duration-300 hover:-translate-y-1">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-pink-500 to-pink-600 flex items-center justify-center mb-6 shadow-lg shadow-pink-500/30">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-gray-900">Lightning Fast</h3>
                    <p class="text-gray-600 leading-relaxed">Built with modern technology for speed and reliability. Access your tasks from anywhere, anytime.</p>
                </div>
            </div>

            <!-- How It Works -->
            <div class="mb-20">
                <div class="text-center mb-12">
                    <h2 class="text-4xl md:text-5xl font-bold tracking-tight mb-4">How It Works</h2>
                    <p class="text-xl text-gray-600">Get started in three simple steps</p>
                </div>

                <div class="grid md:grid-cols-3 gap-8">
                    <div class="relative text-center">
                        <div class="w-20 h-20 mx-auto rounded-3xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-3xl font-bold text-white mb-6 shadow-xl shadow-blue-500/30">1</div>
                        <h3 class="text-xl font-bold mb-2">Create Account</h3>
                        <p class="text-gray-600">Sign up in seconds and start organizing your tasks immediately</p>
                        <svg class="hidden md:block absolute top-10 right-0 w-24 h-8 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </div>

                    <div class="relative text-center">
                        <div class="w-20 h-20 mx-auto rounded-3xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center text-3xl font-bold text-white mb-6 shadow-xl shadow-purple-500/30">2</div>
                        <h3 class="text-xl font-bold mb-2">Add Your Tasks</h3>
                        <p class="text-gray-600">Create tasks, set priorities, and organize them your way</p>
                        <svg class="hidden md:block absolute top-10 right-0 w-24 h-8 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </div>

                    <div class="text-center">
                        <div class="w-20 h-20 mx-auto rounded-3xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-3xl font-bold text-white mb-6 shadow-xl shadow-emerald-500/30">3</div>
                        <h3 class="text-xl font-bold mb-2">Collaborate & Achieve</h3>
                        <p class="text-gray-600">Share with teammates and accomplish goals together</p>
                    </div>
                </div>
            </div>

            <!-- CTA Section -->
            <div class="relative rounded-[2rem] bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 p-12 md:p-16 overflow-hidden">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_50%,rgba(59,130,246,0.15),transparent_50%)]"></div>
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_70%_50%,rgba(147,51,234,0.15),transparent_50%)]"></div>

                <div class="relative text-center max-w-3xl mx-auto">
                    <h2 class="text-4xl md:text-5xl font-bold tracking-tight text-white mb-6">
                        Ready to boost your productivity?
                    </h2>
                    <p class="text-xl text-gray-300 mb-10">
                        Join thousands of teams who already use TaskFlow to manage their work efficiently.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        @auth
                            <a href="{{ url('/dashboard') }}"
                               class="px-8 py-4 rounded-2xl bg-white text-gray-900 font-semibold text-lg hover:bg-gray-100 transition-all shadow-xl hover:-translate-y-1">
                                Go to Dashboard
                            </a>
                        @else
                            <a href="{{ route('register') }}"
                               class="px-8 py-4 rounded-2xl bg-gradient-to-r from-blue-500 to-purple-500 text-white font-semibold text-lg hover:from-blue-600 hover:to-purple-600 transition-all shadow-xl hover:-translate-y-1">
                                Get Started for Free
                            </a>
                            <a href="{{ route('login') }}"
                               class="px-8 py-4 rounded-2xl bg-white/10 text-white font-semibold text-lg hover:bg-white/20 transition-all border border-white/20">
                                Sign In
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-50 border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-6 py-12">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-blue-600 to-purple-600 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <span class="text-lg font-bold text-gray-900">{{ config('app.name', 'TaskFlow') }}</span>
                </div>

                <div class="flex items-center gap-6 text-sm text-gray-500">
                    <a href="#" class="hover:text-gray-900 transition-colors">Privacy Policy</a>
                    <a href="#" class="hover:text-gray-900 transition-colors">Terms of Service</a>
                    <a href="#" class="hover:text-gray-900 transition-colors">Contact</a>
                </div>

                <p class="text-sm text-gray-400">© {{ date('Y') }} {{ config('app.name', 'TaskFlow') }}. All rights reserved.</p>
            </div>
        </div>
    </footer>

</body>
</html>
