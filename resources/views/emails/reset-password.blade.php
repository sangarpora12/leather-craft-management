<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Berliano Leather</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    
    <div style="background: linear-gradient(135deg, #e67e22 0%, #d35400 100%); padding: 30px; border-radius: 10px 10px 0 0; text-align: center;">
        <h1 style="color: white; margin: 0; font-size: 24px;">Berliano Leather</h1>
        <p style="color: white; margin: 10px 0 0 0; font-size: 14px;">Sistem Informasi Manajemen Produksi</p>
    </div>
    
    <div style="background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; border: 1px solid #e9ecef;">
        <h2 style="color: #2c3e50; margin-top: 0;">Reset Password Anda</h2>
        
        <p style="color: #555;">Halo {{ $user->name }},</p>
        
        <p style="color: #555;">
            Kami menerima permintaan untuk mereset password akun Anda. Silakan klik tombol di bawah ini untuk membuat password baru:
        </p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('password.reset', $token) }}" style="display: inline-block; background: linear-gradient(135deg, #e67e22 0%, #d35400 100%); color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-weight: bold;">
                Reset Password
            </a>
        </div>
        
        <p style="color: #777; font-size: 12px;">
            Jika tombol di atas tidak berfungsi, Anda juga dapat menyalin dan menempelkan tautan berikut ke browser Anda:<br>
            <span style="word-break: break-all;">{{ route('password.reset', $token) }}</span>
        </p>
        
        <div style="background: #fff3cd; border: 1px solid #ffeeba; padding: 15px; border-radius: 5px; margin-top: 20px;">
            <p style="color: #856404; margin: 0; font-size: 13px;">
                <strong>Catatan:</strong> Tautan ini akan kedaluarsa dalam 60 menit. Jika Anda tidak meminta reset password, abaikan email ini.
            </p>
        </div>
        
        <hr style="border: none; border-top: 1px solid #e9ecef; margin: 30px 0;">
        
        <p style="color: #999; font-size: 12px; text-align: center; margin: 0;">
            Email ini dikirim secara otomatis oleh Sistem Berliano Leather.<br>
            &copy; 2026 Berliano Leather. All rights reserved.
        </p>
    </div>

</body>
</html>
