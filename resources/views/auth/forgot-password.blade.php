<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Berliano Leather</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            padding: 40px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        }
        .logo-area {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo-area i {
            font-size: 48px;
            color: #e74c3c;
        }
        .logo-area h1 {
            color: #2c3e50;
            font-size: 24px;
            margin-top: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .logo-area p {
            color: #7f8c8d;
            font-size: 14px;
            margin-top: 5px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            color: #34495e;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #3498db;
        }
        .btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #e67e22 0%, #d35400 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s;
            margin-top: 10px;
        }
        .btn:hover {
            transform: scale(1.02);
        }
        .btn:active {
            transform: scale(0.98);
        }
        .alert {
            padding: 12px;
            border-radius: 5px;
            font-size: 13px;
            margin-bottom: 15px;
        }
        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .alert-error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .error-message {
            color: #e74c3c;
            font-size: 12px;
            margin-top: 5px;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            color: #3498db;
            text-decoration: none;
            font-size: 13px;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    @php
        $namaPerusahaan = \App\Models\Pengaturan::where('nama_pengaturan', 'nama_perusahaan')->first();
        $displayName = $namaPerusahaan ? $namaPerusahaan->nilai : 'Berliano Leather';
    @endphp

    <div class="container">
        <div class="logo-area">
            <i class="fas fa-gem"></i>
            <h1>{{ $displayName }}</h1>
            <p>Reset Password</p>
        </div>

        @if(session('status'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <p style="color: #555; font-size: 13px; margin-bottom: 20px; text-align: center;">
            Masukkan username Anda dan kami akan mengirimkan link reset password ke email yang terdaftar.
        </p>

        <form action="{{ route('password.email') }}" method="POST">
            @csrf

            <div class="form-group">
                <label>Username</label>
                <input type="text" name="login" placeholder="Masukkan username Anda" value="{{ old('login') }}" required>
                @error('login')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="btn">
                <i class="fas fa-paper-plane"></i> Kirim Link Reset
            </button>
        </form>

        <div class="back-link">
            <a href="{{ route('login') }}">
                <i class="fas fa-arrow-left"></i> Kembali ke Login
            </a>
        </div>
    </div>

</body>
</html>
