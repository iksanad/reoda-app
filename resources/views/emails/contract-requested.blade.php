@component('mail::message')
# Pengajuan Kontrak Sewa Baru

Halo **{{ $contract->manager->name ?? 'Pengelola' }}**,

Anda menerima pengajuan kontrak sewa baru untuk unit yang Anda kelola. Berikut adalah rinciannya:

- **Properti:** {{ $contract->unit->property->name ?? '-' }}
- **Kamar/Unit:** {{ $contract->unit->name ?? $contract->unit->unit_code ?? '-' }}
- **Nama Penyewa:** {{ $contract->tenant->name ?? '-' }}
- **Email Penyewa:** {{ $contract->tenant->email ?? '-' }}
- **Nomor Telepon:** {{ $contract->tenant->phone ?? '-' }}
- **Durasi Sewa:** {{ $contract->contract_duration ? $contract->contract_duration . ' ' . ($contract->payment_cycle == 'yearly' ? 'Tahun' : 'Bulan') : 'Tidak ditentukan' }}

Mohon segera tinjau dan berikan persetujuan atau penolakan agar penyewa dapat melanjutkan proses.

@component('mail::button', ['url' => route('manager.contracts.show', $contract)])
Tinjau Pengajuan Kontrak
@endcomponent

Terima kasih,<br>
Tim {{ config('app.name') }}
@endcomponent
