<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ \App\Models\Pengaturan::where('nama_pengaturan', 'nama_perusahaan')->first()?->nilai ?? 'Berliano Leather' }}</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        'bg-form': '#1A1C24',
                        'input-dark': '#20232A',
                        'primary-orange': '#FD730D',
                        'link-orange': '#D75A00',
                        'text-body': '#A1A1A1',
                        slate: {
                            800: '#1E293B',
                            900: '#0F172A',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#0D1117] min-h-screen flex items-center justify-center p-4">

    @php
        $namaPerusahaan = \App\Models\Pengaturan::where('nama_pengaturan', 'nama_perusahaan')->first()?->nilai ?? 'Berliano Leather';
        $logoPerusahaan = \App\Models\Pengaturan::where('nama_pengaturan', 'logo_perusahaan')->first()?->nilai;
    @endphp

    <div class="w-full max-w-md bg-bg-form rounded-xl shadow-lg p-12">
        
        <div class="text-center mb-8 mt-2">
            @if($logoPerusahaan)
                <div class="mb-4">
                    <img src="{{ asset('storage/' . $logoPerusahaan) }}" alt="Logo" class="h-16 mx-auto object-contain">
                </div>
            @else
                <div class="mb-4">
                    <i class="fa-solid fa-gem text-5xl text-red-600"></i>
                </div>
            @endif
            <h1 class="text-white text-2xl font-bold mb-2">
                {{ strtoupper($namaPerusahaan) }}
            </h1>
            <p class="text-text-body text-sm font-normal">
                Sistem Informasi Manajemen Produksi
            </p>
        </div>

            <form action="{{ route('login.process') }}" method="POST" class="flex flex-col gap-6">
            @csrf

            @error('username')
                <div class="bg-red-500/10 border border-red-500 text-red-300 p-3 rounded-xl text-xs font-medium">
                    {{ $message }}
                </div>
            @enderror

            <div>
                <label for="username" class="block text-text-body text-xs font-bold mb-2 uppercase tracking-wider">
                    Username
                </label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    class="w-full px-4 py-4 bg-input-dark rounded-xl border border-input-dark/50 text-white text-sm focus:outline-none focus:border-primary-orange focus:ring-1 focus:ring-primary-orange/50 transition-all placeholder:text-text-body"
                    placeholder="Masukkan username admin"
                    value="{{ old('username') }}"
                    required
                >
            </div>

            <div>
                <label for="password" class="block text-text-body text-xs font-bold mb-2 uppercase tracking-wider">
                    Password
                </label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="w-full px-4 py-4 bg-input-dark rounded-xl border border-input-dark/50 text-white text-sm focus:outline-none focus:border-primary-orange focus:ring-1 focus:ring-primary-orange/50 transition-all placeholder:text-text-body tracking-widest"
                    placeholder="••••••••"
                    required
                >
                <i class="fas fa-eye absolute right-4 top-[calc(50%+140px)] cursor-pointer text-text-body hover:text-white transition-colors" id="togglePassword" onclick="togglePassword()" style="position: relative; top: 38px;"></i>
                <div class="flex justify-end mt-2">
                    <a href="{{ route('password.request') }}" class="text-link-orange text-xs font-medium hover:underline transition-colors">
                        Lupa Password?
                    </a>
                </div>
            </div>

            <button type="submit" 
                class="w-full py-4 bg-primary-orange hover:bg-link-orange active:scale-[0.98] rounded-xl shadow-lg shadow-primary-orange/25 text-white text-base font-bold transition-all">
                Masuk Dashboard
            </button>
        </form>

        <div class="mt-12 text-center">
            <p class="text-text-body text-xs font-normal">
                © 2026 Berliano Leather Factory. v1.0
            </p>
        </div>

        <script>
            function togglePassword() {
                const passwordInput = document.getElementById('password');
                const toggleIcon = document.getElementById('togglePassword');
                
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    toggleIcon.classList.remove('fa-eye');
                    toggleIcon.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    toggleIcon.classList.remove('fa-eye-slash');
                    toggleIcon.classList.add('fa-eye');
                }
            }
        </script>
    </div>

</body>
</html>
