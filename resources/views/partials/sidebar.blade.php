<aside
    :class="sidebarToggle ? 'translate-x-0' : '-translate-x-full'"
    class="absolute left-0 top-0 z-9999 flex h-screen w-64 flex-col overflow-y-hidden bg-[#4C74AF] duration-300 ease-linear lg:static lg:translate-x-0"
    @click.outside="sidebarToggle = false"
>
    <!-- SIDEBAR HEADER: Logo -->
    <div class="flex items-center gap-3 px-5 py-5">
        <a href="/" class="flex items-center gap-3 w-full">
            <img src="{{ asset('template/logo/Reoda-White.png') }}" alt="REODA" class="h-16 object-contain drop-shadow-md">
            <div>
                <span class="text-xl font-extrabold text-white tracking-wide drop-shadow-md">REODA</span>
                <p class="text-xs text-white/80 leading-none drop-shadow-sm mt-0.5">Solusi Hunian Terpercaya</p>
            </div>
        </a>
        <!-- Mobile close button -->
        <button class="ml-auto block lg:hidden text-white/80 hover:text-white" @click.stop="sidebarToggle = false">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <!-- SIDEBAR MENU -->
    <div class="no-scrollbar flex flex-col overflow-y-auto flex-1 py-4 px-3">
        <nav x-data="{selected: '{{ request()->segment(2) ?? 'dashboard' }}'}">
            @if(auth()->check() && auth()->user()->isSuperAdmin())
                @php $pendingCount = \App\Models\User::where('role','manager')->where('manager_status','pending')->count(); @endphp
                @include('partials.sidebar-superadmin')
            @elseif(auth()->check() && auth()->user()->isManager())
                @include('partials.sidebar-manager')
            @elseif(auth()->check() && auth()->user()->isTenant())
                @include('partials.sidebar-tenant')
            @endif
        </nav>
    </div>
</aside>
