@component('mail::message')
# {{ $isApproved ? '🎉 Kontrak Sewa Disetujui!' : 'Pengajuan Kontrak Ditolak' }}

Halo, **{{ $contract->tenant->name }}**!

@if($isApproved)
Selamat! Pengajuan kontrak sewa Anda telah **disetujui** oleh pengelola.

@component('mail::panel')
**Properti:** {{ $contract->unit->property->name ?? '-' }}
**Unit:** {{ $contract->unit->unit_code ?? '-' }} — {{ $contract->unit->name ?? '' }}
**Mulai Sewa:** {{ $contract->start_date ? \Carbon\Carbon::parse($contract->start_date)->isoFormat('D MMMM YYYY') : '-' }}
**Harga Sewa:** Rp {{ number_format($contract->rent_amount, 0, ',', '.') }} / bulan
@endcomponent

Anda sekarang dapat melihat detail kontrak dan tagihan Anda melalui Dashboard REODA.

@component('mail::button', ['url' => url('/tenant/dashboard'), 'color' => 'success'])
Lihat Kontrak Saya
@endcomponent

@else
Mohon maaf, pengajuan kontrak sewa Anda untuk **Unit {{ $contract->unit->unit_code ?? '' }}** di **{{ $contract->unit->property->name ?? '' }}** tidak dapat disetujui.

@if($contract->termination_reason)
@component('mail::panel')
**Alasan:** {{ $contract->termination_reason }}
@endcomponent
@endif

Anda dapat mencari properti lain yang tersedia di Explore Market.

@component('mail::button', ['url' => url('/tenant/explore'), 'color' => 'primary'])
Cari Properti Lain
@endcomponent
@endif

Terima kasih telah menggunakan REODA,<br>
**Tim REODA**
@endcomponent
