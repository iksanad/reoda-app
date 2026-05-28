<ul class="flex flex-col gap-0.5">
    <li>
        <a href="{{ route('superadmin.dashboard') }}"
           class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition-colors duration-150
               {{ request()->routeIs('superadmin.dashboard') ? 'bg-[#003648] text-white shadow-sm' : 'text-white/80 hover:bg-[#003648]/50 hover:text-white' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Beranda
        </a>
    </li>
    <li>
        <a href="{{ route('superadmin.managers.index') }}"
           class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition-colors duration-150
               {{ request()->routeIs('superadmin.managers.*') ? 'bg-[#003648] text-white shadow-sm' : 'text-white/80 hover:bg-[#003648]/50 hover:text-white' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Approval Pengelola
            @if(($pendingCount ?? 0) > 0)
                <span class="ml-auto bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $pendingCount }}</span>
            @endif
        </a>
    </li>
    <li>
        <a href="{{ route('superadmin.email-logs.index') }}"
           class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition-colors duration-150
               {{ request()->routeIs('superadmin.email-logs.*') ? 'bg-[#003648] text-white shadow-sm' : 'text-white/80 hover:bg-[#003648]/50 hover:text-white' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            Email Logs
        </a>
    </li>
    <li>
        <a href="{{ route('superadmin.settings.index') }}"
           class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition-colors duration-150
               {{ request()->routeIs('superadmin.settings.*') ? 'bg-[#003648] text-white shadow-sm' : 'text-white/80 hover:bg-[#003648]/50 hover:text-white' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Pengaturan Global
        </a>
    </li>
</ul>
