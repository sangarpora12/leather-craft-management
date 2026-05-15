PHP
public function updateProfile(Request $request)
{
    $user = auth()->user();
    
    // Validasi data input
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,'.$user->id,
        'image' => 'nullable|image|mimes:jpg,png|max:10240' // Max 10MB
    ]);

    // Update data di database
    $user->update($request->only('name', 'email', 'phone'));

    return redirect()->back()->with('success', 'Perubahan berhasil disimpan.');
}
