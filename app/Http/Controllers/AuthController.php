<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use RealRashid\SweetAlert\Facades\Alert;


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

            // Simpan token & username dari form (karena API tidak mengembalikan username)
            session([
                'token' => $data['token'] ?? null,
                'username' => $request->username
            ]);

            if ($token) {
                // Ambil data profile
                $profileResponse = Http::withToken($token)->get('https://gateway.telkomuniversity.ac.id/issueprofile');

                if ($profileResponse->successful()) {
                    $profileData = $profileResponse->json();

                    // dd($profileData); // Debugging untuk melihat data profile

                    // Simpan token & data profile ke session
                    session([
                        'token' => $token,
                        'username' => $profileData['fullname'] ?? $request->username,
                        'nim' => $profileData['numberid'] ?? null,
                        'profilephoto' => $profileData['photo'] ?? $request->profilephoto
                    ]);
                }
            } else {
                Alert::warning('Peringatan', 'Silahkan login terlebih dahulu');
                return redirect()->route('signin.show');
            }

            return redirect()->route('index')->with('success', 'Sign in berhasil');
        } else {

            return back()->withErrors(['signin' => 'Username atau password salah']);
        }
    }

    public function logout(Request $request)
    {
        // Hapus semua data session
        $request->session()->flush();

        // Redirect ke halaman sign in
        return redirect()->route('signin.show')->with('success', 'Berhasil logout');
    }
}
