<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>H-FlowStock</title>
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        .bg-gradient-dark {
            background: linear-gradient(to bottom, #000000, #0f172a);
        }
    </style>
</head>
<body class="antialiased bg-gradient-dark text-white min-h-screen flex flex-col">
    <header class="flex justify-between items-center px-4 md:px-16 py-4">
<div class="shrink-0">
    <a href="/" class="flex items-center space-x-2 md:space-x-3 focus:outline-none">
        <img src="{{ asset('assets/images/logo.png') }}" 
             alt="H-FlowStock Icon" 
             class="w-7 h-7 md:w-10 md:h-10 object-contain">

        <img src="{{ asset('assets/images/nametag.png') }}" 
             alt="H-FlowStock" 
             class="h-4 md:h-6 w-auto object-contain">
    </a>
</div>

        <div class="flex items-center space-x-2">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/home') }}" class="px-3 py-1.5 md:px-4 md:py-2 rounded-lg text-xs md:text-sm font-medium bg-gray-800">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="px-3 py-1.5 md:px-4 md:py-2 rounded-lg text-xs md:text-sm font-medium border border-gray-600">Log in</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="px-3 py-1.5 md:px-4 md:py-2 rounded-lg text-xs md:text-sm font-medium bg-indigo-600">Get Started</a>
                    @endif
                @endauth
            @endif
        </div>
    </header>

    <main class="flex-grow flex flex-col lg:flex-row items-center justify-center px-6 lg:px-16 py-8 md:py-12 gap-8 md:gap-12">
        <div class="max-w-xl text-center lg:text-left order-2 lg:order-1">
            <p class="text-gray-400 font-semibold tracking-wider text-xs md:text-sm">SOLUTIONS | MAINTENANCE</p>
            
            <h1 class="mt-3 text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight">
                LET US SERVE YOU<br>FOR A CHANGE
            </h1>
            
            <p class="mt-4 md:mt-6 text-gray-300 text-base md:text-lg">
                Supercharge your maintenance team with the latest inventory management technology.
                Track supplies, streamline stock requests, and optimize operations.
            </p>
            
            <div class="mt-6 md:mt-8">
                <a href="{{ route('register') }}" class="inline-block px-6 py-3 md:px-8 md:py-4 rounded-lg font-bold bg-indigo-600 hover:bg-indigo-700 shadow-lg text-sm md:text-base">
                    Get Started
                </a>
            </div>
        </div>

        <div class="flex justify-center order-1 lg:order-2">
            <img src="{{ asset('assets/images/csbwelcome.png') }}"
                 alt="Hospital EMS Illustration"
                 class="w-full max-w-[280px] sm:max-w-md rounded-xl shadow-2xl">
        </div>
    </main>
</body>
</html>