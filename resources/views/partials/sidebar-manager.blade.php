<ul class="flex flex-col gap-0.5">
    <li>
        <a href="{{ route('manager.dashboard') }}"
           class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition-colors duration-150
               {{ request()->routeIs('manager.dashboard') ? 'bg-[#003648] text-white shadow-sm' : 'text-white/80 hover:bg-[#003648]/50 hover:text-white' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Beranda
        </a>
    </li>
    <li>
        <a href="{{ route('manager.properties.index') }}"
           class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition-colors duration-150
               {{ request()->routeIs('manager.properties.*') || request()->routeIs('manager.units.*') ? 'bg-[#003648] text-white shadow-sm' : 'text-white/80 hover:bg-[#003648]/50 hover:text-white' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            Lokasi & Properti
        </a>
    </li>
    <li>
        <a href="{{ route('manager.tenants.index') }}"
           class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition-colors duration-150
               {{ request()->routeIs('manager.tenants.*') ? 'bg-[#003648] text-white shadow-sm' : 'text-white/80 hover:bg-[#003648]/50 hover:text-white' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            Penyewa
        </a>
    </li>
    <li>
        <a href="{{ route('manager.payments.index') }}"
           class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition-colors duration-150
               {{ request()->routeIs('manager.payments.*') ? 'bg-[#003648] text-white shadow-sm' : 'text-white/80 hover:bg-[#003648]/50 hover:text-white' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            Pembayaran
        </a>
    </li>
    <li>
        <a href="{{ route('manager.contracts.index') }}"
           class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition-colors duration-150
               {{ request()->routeIs('manager.contracts.*') ? 'bg-[#003648] text-white shadow-sm' : 'text-white/80 hover:bg-[#003648]/50 hover:text-white' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Kontrak Sewa
        </a>
    </li>
    <li>
        <a href="{{ route('manager.reports.index') }}"
           class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition-colors duration-150
               {{ request()->routeIs('manager.reports.*') ? 'bg-[#003648] text-white shadow-sm' : 'text-white/80 hover:bg-[#003648]/50 hover:text-white' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Laporan
        </a>
    </li>
    <li>
        <a href="{{ route('manager.explore.index') }}"
           class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition-colors duration-150
               {{ request()->routeIs('manager.explore.*') ? 'bg-[#003648] text-white shadow-sm' : 'text-white/80 hover:bg-[#003648]/50 hover:text-white' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            Explore Market
        </a>
    </li>

</ul>
