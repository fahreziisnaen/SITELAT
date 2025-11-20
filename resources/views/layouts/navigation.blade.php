<nav x-data="{ open: false, masterOpen: false }" class="bg-gradient-to-r from-indigo-600 to-purple-600 shadow-lg hidden md:block">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 sm:space-x-3 group">
                        <div class="bg-transparent rounded-lg p-1.5 sm:p-2 group-hover:scale-110 transition-transform duration-200">
                            <img src="{{ asset('images/sman21surabaya.png') }}" alt="SILAMBAT" class="object-contain w-8 h-8 sm:w-10 sm:h-auto">
                        </div>
                        <span class="text-white font-bold text-xs sm:text-sm md:text-base lg:text-lg hidden lg:block">
                            <span class="block">SILAMBAT SMAN 21</span>
                            <span class="hidden xl:inline">KOTA SURABAYA</span>
                        </span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-1 md:space-x-2 sm:-my-px sm:ms-4 md:ms-6 lg:ms-10 sm:flex items-center flex-wrap">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-2 md:px-3 lg:px-4 py-2 text-xs md:text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-white/20 text-white' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-4 h-4 mr-1 md:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span class="hidden xl:inline">Dashboard</span>
                        <span class="xl:hidden">Home</span>
                    </a>
                    
                    <!-- Data Master Dropdown (hanya untuk Admin dan TATIB) -->
                    @if(Auth::user()->role !== 'Walikelas')
                    <x-dropdown align="top" width="48">
                        <x-slot name="trigger">
                                <button class="inline-flex items-center px-2 md:px-3 lg:px-4 py-2 text-xs md:text-sm font-medium rounded-lg transition-all duration-200 text-white/80 hover:bg-white/10 hover:text-white">
                                    <svg class="w-4 h-4 mr-1 md:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                                </svg>
                                    <span class="hidden lg:inline">Data Master</span>
                                    <span class="lg:hidden">Master</span>
                                    <svg class="ml-1 md:ml-2 h-3 w-3 md:h-4 md:w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('users.index')">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                    User Management
                                </div>
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('kelas.index')">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    Data Kelas
                                </div>
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('murid.index')">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    Data Murid
                                </div>
                            </x-dropdown-link>
                                <!-- Naik Kelas (hanya untuk Admin) -->
                                @if(Auth::user()->role === 'Admin')
                                    <x-dropdown-link :href="route('naik-kelas.index')">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                            </svg>
                                            Naik Kelas
                                        </div>
                                    </x-dropdown-link>
                                @endif
                        </x-slot>
                    </x-dropdown>
                    @endif
                    
                    <a href="{{ route('keterlambatan.index') }}" class="inline-flex items-center px-2 md:px-3 lg:px-4 py-2 text-xs md:text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('keterlambatan.*') ? 'bg-white/20 text-white' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-4 h-4 mr-1 md:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="hidden xl:inline">Keterlambatan</span>
                        <span class="xl:hidden hidden lg:inline">Keterlambatan</span>
                        <span class="lg:hidden">Keterlambatan</span>
                    </a>
                    
                    <a href="{{ route('report.index') }}" class="inline-flex items-center px-2 md:px-3 lg:px-4 py-2 text-xs md:text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('report.*') ? 'bg-white/20 text-white' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-4 h-4 mr-1 md:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="hidden lg:inline">Report</span>
                        <span class="lg:hidden">Laporan</span>
                    </a>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-2 md:ms-4 lg:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-2 md:px-3 lg:px-4 py-2 bg-white/10 hover:bg-white/20 text-white text-xs md:text-sm font-medium rounded-lg transition-all duration-200">
                            <div class="flex items-center">
                                <div class="bg-white rounded-full p-0.5 md:p-1 mr-1 md:mr-2">
                                    <svg class="w-4 h-4 md:w-5 md:h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <div class="text-left mr-1 md:mr-2 hidden lg:block">
                                    <div class="font-semibold text-xs md:text-sm">{{ Auth::user()->nama_lengkap }}</div>
                                    <div class="text-xs text-indigo-100">{{ Auth::user()->role }}</div>
                                </div>
                                <svg class="fill-current h-3 w-3 md:h-4 md:w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-3 border-b border-gray-100">
                            <p class="text-sm font-medium text-gray-900">{{ Auth::user()->nama_lengkap }}</p>
                            <p class="text-xs text-gray-500">{{ Auth::user()->username }}</p>
                        </div>
                        <x-dropdown-link :href="route('profile.edit')">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Profile
                            </div>
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                <div class="flex items-center text-red-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    Log Out
                                </div>
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center md:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-white hover:bg-white/10 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden md:hidden bg-white/10 backdrop-blur-lg">
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="block pl-3 pr-4 py-2 text-base font-medium {{ request()->routeIs('dashboard') ? 'bg-white/20 text-white' : 'text-white/80 hover:bg-white/10 hover:text-white' }} transition duration-150 ease-in-out">
                Dashboard
            </a>
            
            <!-- Data Master (hanya untuk Admin dan TATIB) -->
            @if(Auth::user()->role !== 'Walikelas')
            <div class="border-t border-white/20 pt-2 mt-2">
                <div class="px-4 py-2 text-xs font-semibold text-white/60 uppercase tracking-wider">Data Master</div>
                <a href="{{ route('users.index') }}" class="block pl-3 pr-4 py-2 text-base font-medium {{ request()->routeIs('users.*') ? 'bg-white/20 text-white' : 'text-white/80 hover:bg-white/10 hover:text-white' }} transition duration-150 ease-in-out">
                    User Management
                </a>
                <a href="{{ route('kelas.index') }}" class="block pl-3 pr-4 py-2 text-base font-medium {{ request()->routeIs('kelas.*') ? 'bg-white/20 text-white' : 'text-white/80 hover:bg-white/10 hover:text-white' }} transition duration-150 ease-in-out">
                    Data Kelas
                </a>
                <a href="{{ route('murid.index') }}" class="block pl-3 pr-4 py-2 text-base font-medium {{ request()->routeIs('murid.*') ? 'bg-white/20 text-white' : 'text-white/80 hover:bg-white/10 hover:text-white' }} transition duration-150 ease-in-out">
                    Data Murid
                </a>
                    <!-- Naik Kelas (hanya untuk Admin) -->
                    @if(Auth::user()->role === 'Admin')
                        <a href="{{ route('naik-kelas.index') }}" class="block pl-3 pr-4 py-2 text-base font-medium {{ request()->routeIs('naik-kelas.*') ? 'bg-white/20 text-white' : 'text-white/80 hover:bg-white/10 hover:text-white' }} transition duration-150 ease-in-out">
                            Naik Kelas
                        </a>
                    @endif
            </div>
            @endif
            
            <a href="{{ route('keterlambatan.index') }}" class="block pl-3 pr-4 py-2 text-base font-medium {{ request()->routeIs('keterlambatan.*') ? 'bg-white/20 text-white' : 'text-white/80 hover:bg-white/10 hover:text-white' }} transition duration-150 ease-in-out">
                Data Keterlambatan
            </a>
            
            <a href="{{ route('report.index') }}" class="block pl-3 pr-4 py-2 text-base font-medium {{ request()->routeIs('report.*') ? 'bg-white/20 text-white' : 'text-white/80 hover:bg-white/10 hover:text-white' }} transition duration-150 ease-in-out">
                Report
            </a>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-white/20">
            <div class="px-4 mb-3">
                <div class="font-medium text-base text-white">{{ Auth::user()->nama_lengkap }}</div>
                <div class="font-medium text-sm text-indigo-100">{{ Auth::user()->username }} • {{ Auth::user()->role }}</div>
            </div>

            <div class="space-y-1">
                <a href="{{ route('profile.edit') }}" class="block pl-3 pr-4 py-2 text-base font-medium text-white/80 hover:bg-white/10 hover:text-white transition duration-150 ease-in-out">
                    Profile
                </a>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left pl-3 pr-4 py-2 text-base font-medium text-red-300 hover:bg-white/10 hover:text-red-200 transition duration-150 ease-in-out">
                        Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

<!-- Bottom Navigation Bar (Mobile Only) -->
<nav class="fixed bottom-0 left-0 right-0 bg-gradient-to-r from-indigo-600 to-purple-600 shadow-lg border-t border-white/20 md:hidden z-50">
    <div class="flex items-center justify-around h-16 px-2">
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center flex-1 py-2 px-2 transition-colors duration-200 {{ request()->routeIs('dashboard') ? 'text-white bg-white/20 rounded-lg' : 'text-white/70 hover:text-white' }}">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span class="text-xs font-medium">Home</span>
        </a>

        <!-- Data Master (hanya untuk Admin dan TATIB) -->
        @if(Auth::user()->role !== 'Walikelas')
            <div class="relative flex-1" x-data="{ open: false }">
                <button @click="open = !open" class="flex flex-col items-center justify-center w-full py-2 px-2 transition-colors duration-200 text-white/70 hover:text-white">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                    </svg>
                    <span class="text-xs font-medium">Master</span>
                </button>
                
                <!-- Dropdown Menu -->
                <div x-show="open" @click.away="open = false" x-cloak class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-56 bg-white rounded-lg shadow-xl border border-gray-200 overflow-hidden z-50">
                    <div class="py-1">
                        <a href="{{ route('users.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 transition-colors duration-150">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                User Management
                            </div>
                        </a>
                        <a href="{{ route('kelas.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 transition-colors duration-150">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                Data Kelas
                            </div>
                        </a>
                        <a href="{{ route('murid.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 transition-colors duration-150">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                Data Murid
                            </div>
                        </a>
                        @if(Auth::user()->role === 'Admin')
                            <a href="{{ route('naik-kelas.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 transition-colors duration-150">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                    </svg>
                                    Naik Kelas
                                </div>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Data Keterlambatan -->
        <a href="{{ route('keterlambatan.index') }}" class="flex flex-col items-center justify-center flex-1 py-2 px-2 transition-colors duration-200 {{ request()->routeIs('keterlambatan.*') ? 'text-white bg-white/20 rounded-lg' : 'text-white/70 hover:text-white' }}">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="text-xs font-medium">Terlambat</span>
        </a>

        <!-- Report -->
        <a href="{{ route('report.index') }}" class="flex flex-col items-center justify-center flex-1 py-2 px-2 transition-colors duration-200 {{ request()->routeIs('report.*') ? 'text-white bg-white/20 rounded-lg' : 'text-white/70 hover:text-white' }}">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span class="text-xs font-medium">Report</span>
        </a>

        <!-- Profile -->
        <div class="relative flex-1" x-data="{ open: false }">
            <button @click="open = !open" class="flex flex-col items-center justify-center w-full py-2 px-2 transition-colors duration-200 text-white/70 hover:text-white">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span class="text-xs font-medium">Profile</span>
            </button>
            
            <!-- Dropdown Menu -->
            <div x-show="open" @click.away="open = false" x-cloak class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-48 bg-white rounded-lg shadow-xl border border-gray-200 overflow-hidden z-50">
                <div class="px-4 py-3 border-b border-gray-100">
                    <p class="text-sm font-medium text-gray-900">{{ Auth::user()->nama_lengkap }}</p>
                    <p class="text-xs text-gray-500">{{ Auth::user()->role }}</p>
                </div>
                <div class="py-1">
                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 transition-colors duration-150">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Edit Profile
                        </div>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors duration-150">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Log Out
                            </div>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>
