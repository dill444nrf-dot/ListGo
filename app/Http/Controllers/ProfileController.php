<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage; // 

class ProfileController extends Controller
{
    // Menampilkan data user yang sedang login
    public function show(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user()
        ], 200);
    }

    // Mengupdate data profil (nama, email, username, dan foto)
    public function update(Request $request)
    {
        $user = $request->user();

        // 1. Validasi input termasuk username dan file foto
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'username' => 'nullable|string|max:255|unique:users,username,' . $user->id,
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // Maksimal 2MB
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
        ];

        // 2. Tangani jika ada file foto baru yang diunggah
        if ($request->hasFile('photo')) {
            // Hapus foto lama jika ada agar storage tidak menumpuk file sampah
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }
            
            // Simpan foto baru ke folder storage/app/public/photos
            $path = $request->file('photo')->store('photos', 'public');
            $updateData['photo'] = $path;
        }

        // 3. Update data ke database
        $user->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui!',
            'data' => $user
        ], 200);
    }

    // Mengubah password
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed', 
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password lama salah!'
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diubah!'
        ], 200);
    }
}