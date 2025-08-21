<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cookie;


class AuthController extends Controller
{
    public function showSignIn()
    {
        return view('authentication.signin'); // sesuai folder resources/views/authentication/signin.blade.php
    }

    public function processSignIn(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        // Kirim request ke API SSO Telkom University
        $response = Http::asForm()->post('https://gateway.telkomuniversity.ac.id/issueauth', [
            'username' => $request->username,
            'password' => $request->password,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $token = $data['token'] ?? null;

            // Simpan token & username dari form (sementara)
            session([
                'token' => $token,
                'username' => $request->username
            ]);

            if ($token) {
                // Ambil data profile
                $profileResponse = Http::withToken($token)->get('https://gateway.telkomuniversity.ac.id/issueprofile');

                if ($profileResponse->successful()) {
                    $profileData = $profileResponse->json();

                    // Simpan token & data profile ke session
                    session([
                        'token' => $token,
                        'username' => $profileData['fullname'] ?? $request->username,
                        'nim' => $profileData['numberid'] ?? null,
                        'profilephoto' => $profileData['photo'] ?? $request->profilephoto,
                        'email' => $profileData['email'] ?? null,
                        'phone' => $profileData['phone'] ?? null,
                        'fakultas' => $profileData['faculty'] ?? null,
                        'prodi' => $profileData['studyprogram'] ?? null,
                        'kelas' => $profileData['studentclass'] ?? null,
                    ]);
                }

                // ✅ Cek apakah remember me dicentang
                if ($request->has('remember')) {
                    // Simpan cookie selama 30 hari (43200 menit)
                    Cookie::queue('remember_token', $token, 43200);
                } else {
                    // Pastikan cookie hilang saat browser ditutup
                    config(['session.expire_on_close' => true]);
                }

                return redirect()->route('index')->with('success', 'Sign in berhasil');
            } else {
                return redirect()->route('signin.show');
            }

        } else {
            return back()->withErrors(['signin' => 'Username atau password salah']);
        }
    }

    public function logout(Request $request)
    {
        // Hapus semua data session
        $request->session()->flush();

        // Hapus cookie remember me
        Cookie::queue(Cookie::forget('remember_token'));
        Cookie::queue(Cookie::forget('remember_username'));

        // Redirect ke halaman sign in
        return redirect()->route('signin.show')->with('success', 'Berhasil logout');
    }
}
