<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>SIKETSA - SMAN 21 KOTA SURABAYA</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">
        <link rel="shortcut icon" type="image/png" href="{{ asset('favicon.ico') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Alpine.js x-cloak style -->
        <style>
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-50">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="mobile-header-gradient relative overflow-hidden">
                    <style>
                        header.mobile-header-gradient {
                            background: linear-gradient(to right, #4f46e5, #9333ea, #4f46e5);
                            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
                        }
                        @media (min-width: 768px) {
                            header.mobile-header-gradient {
                                background: white !important;
                                box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1);
                            }
                        }
                        @media (max-width: 767px) {
                            header.mobile-header-gradient .mobile-header-content h2,
                            header.mobile-header-gradient .mobile-header-content h2 * {
                                color: white !important;
                            }
                            header.mobile-header-gradient .mobile-header-content p,
                            header.mobile-header-gradient .mobile-header-content p * {
                                color: rgba(255, 255, 255, 0.9) !important;
                            }
                            header.mobile-header-gradient .mobile-header-content div,
                            header.mobile-header-gradient .mobile-header-content div *,
                            header.mobile-header-gradient .mobile-header-content span,
                            header.mobile-header-gradient .mobile-header-content span * {
                                color: rgba(255, 255, 255, 0.9) !important;
                            }
                            header.mobile-header-gradient .mobile-header-content svg {
                                color: white !important;
                            }
                        }
                    </style>
                    <!-- Decorative elements for mobile -->
                    <div class="absolute inset-0 md:hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                        <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full -ml-12 -mb-12"></div>
                    </div>
                    <div class="max-w-7xl mx-auto py-4 md:py-6 px-4 sm:px-6 lg:px-8 relative z-10">
                        <div class="mobile-header-content">
                            {{ $header }}
                        </div>
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="pb-16 md:pb-0">
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
