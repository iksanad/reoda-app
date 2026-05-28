<ul class="flex flex-col gap-0.5">
    <li>
        <a href="{{ route('tenant.dashboard') }}"
           class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition-colors duration-150
               {{ request()->routeIs('tenant.dashboard') 
                   ? 'bg-[#003648] text-white shadow-sm' 
                   : 'text-white/80 hover:bg-[#003648]/50 hover:text-white' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Beranda
        </a>
    </li>
    <li>
        <a href="{{ route('tenant.explore.index') }}"
           class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition-colors duration-150
               {{ request()->routeIs('tenant.explore.*') 
                   ? 'bg-[#003648] text-white shadow-sm' 
                   : 'text-white/80 hover:bg-[#003648]/50 hover:text-white' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            Explore Market
        </a>
    </li>
    <li>
        <a href="{{ route('tenant.transactions.index') }}"
           class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition-colors duration-150
               {{ request()->routeIs('tenant.transactions.*') 
                   ? 'bg-[#003648] text-white shadow-sm' 
                   : 'text-white/80 hover:bg-[#003648]/50 hover:text-white' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
            Transaksi & Tagihan
        </a>
    </li>

    <li>
        <a href="{{ route('tenant.services.index') }}"
           class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition-colors duration-150
               {{ request()->routeIs('tenant.services.*') 
                   ? 'bg-[#003648] text-white shadow-sm' 
                   : 'text-white/80 hover:bg-[#003648]/50 hover:text-white' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            Layanan
        </a>
    </li>

</ul>
