<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menunggu Persetujuan - REODA</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">
    <div class="max-w-lg w-full bg-white rounded-2xl shadow-sm border border-gray-200 p-8 text-center mx-4">
        <svg class="w-20 h-20 mx-auto text-amber-500 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <h1 class="text-2xl font-bold text-gray-900 mb-3">Akun Dalam Peninjauan</h1>
        <p class="text-gray-600 mb-8">Pendaftaran Anda sebagai pengelola sedang ditinjau oleh Superadmin. Kami akan mengirimkan notifikasi email setelah akun Anda disetujui.</p>
        
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="rounded-xl bg-gray-100 text-gray-700 px-6 py-2.5 font-semibold hover:bg-gray-200 transition">
                Keluar (Logout)
            </button>
        </form>
    </div>
</body>
</html>
