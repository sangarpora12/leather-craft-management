@extends('layouts.operator_app')

@section('title', 'Profil Saya')

@section('content')
<style>
    /* Force Two-Column Layout */
    .profile-wrapper {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        padding: 20px;
    }
    
    .profile-left {
        flex: 1;
        min-width: 300px;
        max-width: 350px;
    }
    
    .profile-right {
        flex: 2;
        min-width: 400px;
    }
    
    .mini-stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
        margin-bottom: 20px;
    }
    
    /* Card Styles */
    .profile-card {
        background-color: #1e1e1e;
        border: 1px solid #333;
        border-radius: 16px;
        padding: 32px;
        text-align: center;
    }
    
    .info-card {
        background-color: #1e1e1e;
        border: 1px solid #333;
        border-radius: 16px;
        padding: 28px;
    }
    
    .stat-mini-card {
        background-color: #252525;
        border: 1px solid #333;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        transition: all 0.3s ease;
    }
    
    .stat-mini-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.4);
        border-color: #444;
    }
    
    /* Avatar */
    .avatar-large {
        width: 140px;
        height: 140px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #b45309;
        box-shadow: 0 8px 30px rgba(180,83,9,0.3);
        margin: 0 auto 20px;
        display: block;
    }
    
    /* Badge */
    .badge-role {
        background: linear-gradient(135deg, #b45309 0%, #92400e 100%);
        color: white;
        padding: 8px 20px;
        border-radius: 24px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        display: inline-block;
        margin-bottom: 20px;
    }
    
    /* Stat */
    .stat-number {
        font-size: 28px;
        font-weight: 700;
        color: #ffffff;
        margin-bottom: 4px;
    }
    
    .stat-label {
        font-size: 11px;
        color: #888;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    /* Info Row */
    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 0;
        border-bottom: 1px solid rgba(255,255,255,0.05);
    }
    
    .info-row:last-child {
        border-bottom: none;
    }
    
    .info-label {
        font-size: 12px;
        color: #888;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .info-value {
        font-size: 15px;
        color: #fff;
        font-weight: 600;
    }
    
    /* Buttons */
    .btn-amber {
        background: linear-gradient(135deg, #b45309 0%, #d97706 100%);
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
        width: 100%;
    }
    
    .btn-amber:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(180,83,9,0.4);
        color: white;
    }
    
    .btn-logout {
        background: rgba(239,68,68,0.1);
        color: #ef4444;
        border: 1px solid rgba(239,68,68,0.2);
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
        width: 100%;
        margin-top: 10px;
    }
    
    .btn-logout:hover {
        background: rgba(239,68,68,0.2);
        color: #ef4444;
    }
    
    /* Stat Icon */
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 12px;
    }
    
    /* Section Title */
    .section-title {
        font-size: 16px;
        font-weight: 700;
        color: #fff;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .section-title i {
        color: #b45309;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .profile-left, .profile-right {
            max-width: 100%;
            flex: 100%;
        }
        
        .mini-stats-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }
</style>

<div class="container-fluid profile-wrapper" style="background-color: #121212; min-height: 100vh;">
    {{-- SISI KIRI: Profile Card --}}
    <div class="profile-left">
        <div class="profile-card">
            {{-- Avatar --}}
            @if($user->foto)
                <img src="{{ asset('storage/' . $user->foto) }}" alt="Foto Profil" class="avatar-large">
            @else
                <div class="avatar-large d-flex align-items-center justify-content-center text-white" 
                     style="background: linear-gradient(135deg, #b45309 0%, #92400e 100%); font-size: 52px; font-weight: 700;">
                    {{ substr($user->name ?? $user->username, 0, 1) }}
                </div>
            @endif

            {{-- Name --}}
            <h4 class="text-white mb-2" style="font-weight: 700; font-size: 20px;">{{ $user->name ?? $user->username }}</h4>
            
            {{-- Role Badge --}}
            <span class="badge-role">Operator Produksi</span>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-logout">
                    <i class="fas fa-sign-out-alt me-2"></i>Keluar
                </button>
            </form>
        </div>
    </div>

    {{-- SISI KANAN: Statistik & Informasi --}}
    <div class="profile-right">
        {{-- Baris Atas: Statistik (Mini Stats Grid) --}}
        <div class="mini-stats-grid">
            <div class="stat-mini-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, rgba(16,185,129,0.2) 0%, rgba(16,185,129,0.1) 100%);">
                    <i class="fas fa-box text-success fa-lg"></i>
                </div>
                <div class="stat-number">{{ number_format($user->productions()->sum('jumlah_produksi')) }}</div>
                <div class="stat-label">Total Produksi</div>
            </div>
            <div class="stat-mini-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, rgba(59,130,246,0.2) 0%, rgba(59,130,246,0.1) 100%);">
                    <i class="fas fa-check-circle text-primary fa-lg"></i>
                </div>
                <div class="stat-number">{{ $user->productions()->where('status', 'SELESAI')->count() }}</div>
                <div class="stat-label">SPK Selesai</div>
            </div>
            <div class="stat-mini-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, rgba(245,158,11,0.2) 0%, rgba(245,158,11,0.1) 100%);">
                    <i class="fas fa-truck text-warning fa-lg"></i>
                </div>
                <div class="stat-number">{{ $user->productions()->where('status', 'PROSES')->count() }}</div>
                <div class="stat-label">Sedang Proses</div>
            </div>
        </div>

        {{-- Baris Bawah: Detail Informasi --}}
        <div class="info-card">
            <div class="section-title">
                <i class="fas fa-user-circle"></i>
                Informasi Akun
            </div>

            <div class="info-row">
                <span class="info-label">
                    <i class="fas fa-user me-2"></i>Username
                </span>
                <span class="info-value">{{ $user->username }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">
                    <i class="fas fa-envelope me-2"></i>Email
                </span>
                <span class="info-value">{{ $user->email }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">
                    <i class="fas fa-calendar me-2"></i>Bergabung Sejak
                </span>
                <span class="info-value">{{ $user->created_at->format('d M Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">
                    <i class="fas fa-shield me-2"></i>Role
                </span>
                <span class="info-value text-capitalize">{{ $user->role }}</span>
            </div>

            {{-- Form Edit Info Akun --}}
            <div class="mt-4 pt-4" style="border-top: 1px solid rgba(255,255,255,0.05);">
                @if(session('success'))
                    <div class="alert alert-success mb-3" style="background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.3); color: #10b981; border-radius: 8px; padding: 10px 14px; font-size: 13px;">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    </div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger mb-3" style="background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); color: #ef4444; border-radius: 8px; padding: 10px 14px; font-size: 13px;">
                        @foreach($errors->all() as $error)
                            <div><i class="fas fa-exclamation-circle me-1"></i>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('operator.updateFoto') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    {{-- Upload Foto Profil --}}
                    <div class="mb-3">
                        <label class="info-label mb-2 d-block">
                            <i class="fas fa-camera me-1"></i>Ganti Foto Profil
                        </label>
                        <input type="file" name="foto" accept="image/jpeg,image/png,image/jpg"
                            class="form-control form-control-sm"
                            style="background: #2a2a2a; border: 1px solid #444; color: #ccc; border-radius: 8px; font-size: 13px;">
                        <small class="text-muted" style="font-size: 11px;">Format: JPG, PNG. Max 2MB.</small>
                    </div>
                    <button type="submit" class="btn btn-amber">
                        <i class="fas fa-save me-2"></i>Simpan Foto Profil
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
</script>
@endsection