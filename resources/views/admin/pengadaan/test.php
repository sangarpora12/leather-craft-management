PHP
public function updateProfile(Request $request)
{
    // 1. Validasi Input Data Personal
    $request->validate([
        'nama' => 'required|string|max:255',
        'no_hp' => 'nullable|string|max:15',
        'foto' => 'nullable|image|mimes:jpg,png|max:2048'
    ]);

    $user = auth()->user();
    $user->name = $request->nama;
    $user->phone = $request->no_hp;

    // 2. Logika Unggah dan Penggantian Foto Profil
    if ($request->hasFile('foto')) {
        $path = $request->file('foto')->store('avatars', 'pub-lic');
        $user->avatar = $path;
    }

    $user->save();
    return redirect()->back()->with('success', 'Informasi profil berhasil diperbarui.');
}

public function changePassword(Request $request)
{
    // 1. Validasi Kredensial Keamanan
    $request->validate([
        'current_password' => 'required',
        'new_password' => 'required|min:8|confirmed',
    ]);

    // 2. Verifikasi Password Lama Menggunakan Hash Check
    if (!Hash::check($request->current_password, auth()->user()->password)) {
        return redirect()->back()->with('error', 'Password lama yang Anda masukkan salah.');
    }

    // 3. Enkripsi dan Penyimpanan Password Baru
    auth()->user()->update([
        'password' => Hash::make($request->new_password)
    ]);

    return redirect()->back()->with('success', 'Password berhasil diperbarui secara aman.');
}
