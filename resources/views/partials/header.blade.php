<header class="sticky top-0 z-999 flex w-full bg-white border-b border-gray-200 shadow-sm">
    <div class="relative flex flex-grow items-center justify-between py-3.5 px-4 md:px-6">
        <!-- Left: hamburger + mobile logo -->
        <div class="flex items-center gap-3 lg:hidden">
            <!-- Hamburger Toggle BTN -->
            <button
                class="z-99999 block rounded-sm border border-stroke bg-white p-1.5 shadow-sm lg:hidden"
                @click.stop="sidebarToggle = !sidebarToggle"
            >
                <span class="relative block h-5.5 w-5.5 cursor-pointer">
                    <span class="du-block absolute right-0 h-full w-full">
                        <span
                            class="relative top-0 left-0 my-1 block h-0.5 w-0 rounded-sm bg-black delay-[0] duration-200 ease-in-out"
                            :class="{ '!w-full delay-300': !sidebarToggle }"
                        ></span>
                        <span
                            class="relative top-0 left-0 my-1 block h-0.5 w-0 rounded-sm bg-black delay-150 duration-200 ease-in-out"
                            :class="{ '!w-full delay-400': !sidebarToggle }"
                        ></span>
                        <span
                            class="relative top-0 left-0 my-1 block h-0.5 w-0 rounded-sm bg-black delay-200 duration-200 ease-in-out"
                            :class="{ '!w-full delay-500': !sidebarToggle }"
                        ></span>
                    </span>
                    <span class="du-block absolute right-0 h-full w-full rotate-45">
                        <span
                            class="absolute left-2.5 top-0 block h-full w-0.5 rounded-sm bg-black delay-300 duration-200 ease-in-out"
                            :class="{ '!h-0 delay-[0]': !sidebarToggle }"
                        ></span>
                        <span
                            class="delay-400 absolute left-0 top-2.5 block h-0.5 w-full rounded-sm bg-black duration-200 ease-in-out"
                            :class="{ '!h-0 delay-200': !sidebarToggle }"
                        ></span>
                    </span>
                </span>
            </button>
            <!-- Hamburger Toggle BTN -->

            <a class="block flex-shrink-0 lg:hidden" href="/">
                <img src="{{ asset('template/logo/Reoda-4C74AF.png') }}" alt="Logo" class="h-8" />
            </a>
        </div>

        <!-- Center: page title -->
        <div class="absolute left-1/2 -translate-x-1/2">
            <span class="text-xl font-bold text-reoda tracking-wide">Reoda</span>
        </div>

        <!-- Right: user dropdown / auth actions -->
        <div class="flex items-center gap-3 ml-auto">
            @auth
            <ul class="flex items-center gap-2">
                <!-- Notifications Area -->
                <li class="relative" x-data="{ notifOpen: false }">
                    <a
                        class="relative flex h-8.5 w-8.5 items-center justify-center rounded-full border-[0.5px] border-stroke bg-gray hover:text-primary"
                        href="#"
                        @click.prevent="notifOpen = !notifOpen"
                    >
                        <span class="absolute -top-0.5 -right-0.5 z-1 h-2.5 w-2.5 rounded-full bg-red-500" @if(($unreadNotificationsCount ?? 0) === 0) style="display: none;" @endif>
                            <span class="absolute -z-1 inline-flex h-full w-full animate-ping rounded-full bg-red-500 opacity-75"></span>
                            @if(($unreadNotificationsCount ?? 0) > 0)
                            <span class="absolute inset-0 flex items-center justify-center text-[8px] font-bold text-white">{{ $unreadNotificationsCount > 9 ? '9+' : $unreadNotificationsCount }}</span>
                            @endif
                        </span>
                        
                        <svg class="fill-current duration-300 ease-in-out w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                            <path d="M427.3 359.1L383 318.9V213.3C383 147.2 338 91.1 275.6 76.5v-10.4c0-10.9-8.9-19.7-19.7-19.7s-19.7 8.9-19.7 19.7v10.4c-62.5 14.6-107.4 70.8-107.4 136.9v105.6l-44.3 40.2c-5.4 4.9-7 12.6-4.1 19.4s9.4 11.2 16.6 11.2h318.5c7.2 0 13.7-4.4 16.6-11.2s1.3-14.5-4.2-19.4zM255.9 465.4c29.1 0 52.8-23.7 52.8-52.8h-105.6c0 29.1 23.6 52.8 52.8 52.8z"/>
                        </svg>
                    </a>

                    <!-- Dropdown -->
                    <div
                        x-show="notifOpen"
                        @click.outside="notifOpen = false"
                        class="absolute right-[-20px] sm:right-0 mt-4 flex max-h-96 w-[300px] sm:w-80 flex-col rounded-sm border border-gray-200 bg-white shadow-default z-50"
                        style="display: none;"
                    >
                        <div class="px-4.5 py-3 border-b border-gray-200 flex justify-between items-center">
                            <h5 class="text-sm font-medium text-gray-900">Notifikasi
                                @if(($unreadNotificationsCount ?? 0) > 0)
                                <span class="ml-1.5 inline-flex items-center rounded-full bg-red-500 px-1.5 py-0.5 text-[10px] font-bold text-white">{{ $unreadNotificationsCount }}</span>
                                @endif
                            </h5>
                            @if(($unreadNotificationsCount ?? 0) > 0)
                            <form method="POST" action="{{ route('notifications.readAll') }}">
                                @csrf
                                <button type="submit" class="text-xs text-reoda hover:underline">Tandai semua dibaca</button>
                            </form>
                            @endif
                        </div>

                        <ul class="flex h-auto flex-col overflow-y-auto">
                            @forelse($recentNotifications ?? [] as $notification)
                            <li>
                                <form method="POST" action="{{ route('notifications.read', $notification->id) }}" class="block w-full">
                                    @csrf
                                    <button type="submit" class="flex w-full text-left flex-col gap-1 border-t border-gray-200 px-4.5 py-3 hover:bg-gray-50 transition {{ $notification->is_read ? 'opacity-60' : 'bg-blue-50/30' }}">
                                        <p class="text-sm font-semibold text-black flex items-center justify-between">
                                            {{ $notification->title }}
                                            @if(!$notification->is_read)
                                                <span class="h-2 w-2 rounded-full bg-red-500 flex-shrink-0 ml-2"></span>
                                            @endif
                                        </p>
                                        <p class="text-xs text-gray-600 line-clamp-2">
                                            {{ $notification->message }}
                                        </p>
                                        <p class="text-[10px] text-gray-400">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </p>
                                    </button>
                                </form>
                            </li>
                            @empty
                            <li class="px-4.5 py-6 text-center text-sm text-gray-500">
                                Belum ada notifikasi
                            </li>
                            @endforelse
                        </ul>
                        
                        <div class="px-4.5 py-3 border-t border-gray-200 text-center">
                            <a href="{{ route('notifications.index') }}" class="text-sm font-medium text-reoda hover:text-reoda-dark hover:underline">Lihat Semua Notifikasi</a>
                        </div>
                    </div>
                </li>

                <!-- User Area -->
                <li class="relative" x-data="{ dropdownOpen: false }">
                    <a
                        class="flex items-center gap-4"
                        href="#"
                        @click.prevent="dropdownOpen = !dropdownOpen"
                    >
                        <span class="hidden text-right lg:block">
                            <span class="block text-sm font-medium text-black">{{ auth()->user()->name }}</span>
                            <span class="block text-xs font-medium">{{ auth()->user()->role_display }}</span>
                        </span>

                        <span class="h-12 w-12 rounded-full overflow-hidden">
                            <img src="{{ auth()->user()->avatar_url }}" alt="User" />
                        </span>

                        <svg class="hidden fill-current sm:block" width="12" height="8" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M0.410765 0.910734C0.736202 0.585297 1.26384 0.585297 1.58928 0.910734L6.00002 5.32148L10.4108 0.910734C10.7362 0.585297 11.2638 0.585297 11.5893 0.910734C11.9147 1.23617 11.9147 1.76381 11.5893 2.08924L6.58928 7.08924C6.26384 7.41468 5.7362 7.41468 5.41077 7.08924L0.410765 2.08924C0.0853277 1.76381 0.0853277 1.23617 0.410765 0.910734Z" fill="" />
                        </svg>
                    </a>

                    <!-- Dropdown -->
                    <div
                        x-show="dropdownOpen"
                        @click.outside="dropdownOpen = false"
                        class="absolute right-0 mt-4 flex w-62.5 flex-col rounded-sm border border-gray-200 bg-white shadow-default z-50 py-2"
                        style="display: none;"
                    >
                        <ul class="flex flex-col gap-5 border-b border-gray-200 px-6 py-4">
                            @if(auth()->user()->isSuperAdmin())
                            <li>
                                <a href="{{ route('superadmin.managers.index') }}" class="flex items-center gap-3.5 text-sm font-medium duration-300 ease-in-out hover:text-reoda">
                                    Approval Pengelola
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('superadmin.settings.index') }}" class="flex items-center gap-3.5 text-sm font-medium duration-300 ease-in-out hover:text-reoda">
                                    Pengaturan Global
                                </a>
                            </li>
                            @else
                            <li>
                                <a href="{{ auth()->user()->isManager() ? route('manager.profile.index') : route('tenant.profile.index') }}" class="flex items-center gap-3.5 text-sm font-medium duration-300 ease-in-out hover:text-reoda">
                                    Profil Saya
                                </a>
                            </li>
                            <li>
                                <a href="{{ auth()->user()->isManager() ? route('manager.settings.index') : route('tenant.settings.index') }}" class="flex items-center gap-3.5 text-sm font-medium duration-300 ease-in-out hover:text-reoda">
                                    Pengaturan
                                </a>
                            </li>
                            @endif
                        </ul>
                        <div class="px-6 py-4 text-sm font-medium duration-300 ease-in-out hover:text-red-500">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left">Keluar</button>
                            </form>
                        </div>
                    </div>
                </li>
            </ul>
            @else
            <div class="flex items-center gap-2 lg:gap-4">
                <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-700 hover:text-reoda transition">Masuk</a>
                <a href="{{ route('register') }}" class="rounded-lg bg-reoda px-4 py-2 text-sm font-bold text-white shadow-sm hover:bg-reoda-dark transition">Daftar</a>
            </div>
            @endauth
        </div>
    </div>
</header>
