@component('mail::message')
# {{ $type === 'due_today' ? '🔔 Tagihan Sewa Jatuh Tempo Hari Ini' : ($type === 'overdue_warning' ? '⚠️ Peringatan Batas Toleransi' : '❌ Kontrak Sewa Dihentikan') }}

Halo, **{{ $contract->tenant->name }}**!

@if($type === 'due_today')
Tagihan sewa Anda untuk periode **{{ \Carbon\Carbon::createFromDate($invoice->billing_year, $invoice->billing_month)->isoFormat('MMMM YYYY') }}** jatuh tempo **hari ini**.

@component('mail::panel')
**{{ $contract->unit->property->name ?? 'Properti' }}** — Unit {{ $contract->unit->unit_code ?? '' }}
Nominal: **Rp {{ number_format($invoice->amount, 0, ',', '.') }}**
Jatuh Tempo: **{{ \Carbon\Carbon::parse($invoice->due_date)->isoFormat('D MMMM YYYY') }}**
@endcomponent

Segera lakukan pembayaran untuk menghindari denda keterlambatan.

@elseif($type === 'overdue_warning')
Tagihan sewa Anda sudah melewati tanggal jatuh tempo. Batas toleransi akan **berakhir besok** pada **{{ $deadline?->isoFormat('D MMMM YYYY') }}**.

@component('mail::panel')
**{{ $contract->unit->property->name ?? 'Properti' }}** — Unit {{ $contract->unit->unit_code ?? '' }}
Nominal: **Rp {{ number_format($invoice->amount, 0, ',', '.') }}**
Batas Toleransi: **{{ $deadline?->isoFormat('D MMMM YYYY') }}**
@endcomponent

> ⚠️ Jika pembayaran tidak dilakukan sebelum batas toleransi, kontrak sewa Anda akan **dihentikan otomatis**.

@else
Kami menginformasikan bahwa kontrak sewa Anda telah **dihentikan secara otomatis** karena tagihan melewati batas toleransi.

@component('mail::panel')
**{{ $contract->unit->property->name ?? 'Properti' }}** — Unit {{ $contract->unit->unit_code ?? '' }}
Status: Kontrak dihentikan
@endcomponent

Untuk informasi lebih lanjut, hubungi pengelola properti Anda.
@endif

@component('mail::button', ['url' => url('/tenant/dashboard'), 'color' => 'primary'])
Buka Dashboard REODA
@endcomponent

Terima kasih,<br>
**Tim REODA**
@endcomponent
