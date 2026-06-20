@extends('layouts.app')
@section('title', 'Semua Notifikasi - REODA')
@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-title-md2 font-bold text-black">Semua Notifikasi</h2>
    <nav><ol class="flex items-center gap-2">
        <li><a class="font-medium hover:text-reoda" href="{{ auth()->user()->isManager() ? route('manager.dashboard') : route('tenant.dashboard') }}">Dashboard /</a></li>
        <li class="font-medium text-reoda">Notifikasi</li>
    </ol></nav>
</div>

<div class="rounded-xl border border-stroke bg-white shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-stroke flex justify-between items-center bg-gray-50">
        <h3 class="font-bold text-black">Notifikasi Anda</h3>
        @if($notifications->contains('is_read', false))
        <form method="POST" action="{{ route('notifications.readAll') }}">
            @csrf
            <button type="submit" class="text-sm font-medium text-reoda hover:underline">Tandai semua dibaca</button>
        </form>
        @endif
    </div>

    <div class="divide-y divide-stroke">
        @forelse($notifications as $notification)
        <div class="p-6 flex flex-col sm:flex-row gap-4 sm:items-start hover:bg-gray-50 transition {{ $notification->is_read ? 'opacity-70' : 'bg-blue-50/20' }}">
            <div class="h-10 w-10 flex-shrink-0 rounded-full flex items-center justify-center {{ $notification->is_read ? 'bg-gray-100 text-gray-500' : 'bg-blue-100 text-blue-500' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
            </div>
            
            <div class="flex-grow">
                <div class="flex justify-between items-start mb-1">
                    <h4 class="font-bold text-black flex items-center gap-2">
                        {{ $notification->title }}
                        @if(!$notification->is_read)
                            <span class="inline-block w-2 h-2 rounded-full bg-red-500"></span>
                        @endif
                    </h4>
                    <span class="text-xs text-gray-400 whitespace-nowrap ml-4">{{ $notification->created_at->diffForHumans() }}</span>
                </div>
                <p class="text-sm text-gray-600 mb-3">{{ $notification->message }}</p>
                
                @if($notification->link)
                <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                    @csrf
                    <button type="submit" class="text-sm font-medium text-reoda hover:text-reoda-dark hover:underline flex items-center gap-1">
                        Lihat Detail
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </form>
                @endif
            </div>
        </div>
        @empty
        <div class="p-8 text-center text-gray-500">
            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
            <p>Belum ada notifikasi saat ini.</p>
        </div>
        @endforelse
    </div>
    
    @if($notifications->hasPages())
    <div class="px-6 py-4 border-t border-stroke">
        {{ $notifications->links() }}
    </div>
    @endif
</div>
@endsection
